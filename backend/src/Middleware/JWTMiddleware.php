<?php
// Path: backend/src/Middleware/JWTMiddleware.php
namespace Middleware;

use Service\JWTService;
use Exception;

class JWTMiddleware
{
    private $jwtService;
    private $requiredRoles = [];
    private $requiredStatus = 'approved';

    public function __construct(JWTService $jwtService, array $requiredRoles = [])
    {
        $this->jwtService = $jwtService;
        $this->requiredRoles = $requiredRoles;
    }

    public function __invoke($request, $response, $next)
    {
        error_log("JWTMiddleware: Start processing");

        // Récupérer les en-têtes
        $headers = getallheaders();
        error_log("JWTMiddleware: All headers: " . print_r($headers, true));

        if (!isset($headers['Authorization'])) {
            error_log("JWTMiddleware: Authorization header not found");
            return $response->withStatus(401)->withJson(['error' => 'Authorization header not found']);
        }

        $authHeader = $headers['Authorization'];
        error_log("JWTMiddleware: Authorization header found: " . $authHeader);

        list($jwt) = sscanf($authHeader, 'Bearer %s');
        if (!$jwt) {
            error_log("JWTMiddleware: Bearer token not found");
            return $response->withStatus(401)->withJson(['error' => 'Bearer token not found']);
        }

        error_log("JWTMiddleware: Bearer token: $jwt");

        $decoded = $this->jwtService->verifyToken($jwt);
        if (!$decoded) {
            error_log("JWTMiddleware: Invalid token");
            return $response->withStatus(401)->withJson(['error' => 'Invalid token']);
        }

        error_log("JWTMiddleware: Token verified successfully");

        if (!empty($this->requiredRoles) && !in_array($decoded->role, $this->requiredRoles)) {
            error_log("JWTMiddleware: Access denied for role: " . $decoded->role);
            return $response->withStatus(403)->withJson(['error' => 'Access denied']);
        }

        // Vérifier le statut de l'utilisateur
        if ($decoded->status !== $this->requiredStatus) {
            error_log("JWTMiddleware: User status is not approved");
            return $response->withStatus(403)->withJson(['error' => 'Access denied: User status is not approved']);
        }

        $request = $request->withAttribute('user', $decoded);
        return $next($request, $response);
    }

    public function verifyToken()
    {
        error_log("JWTMiddleware: Verifying token");

        // Récupérer les en-têtes
        $headers = getallheaders();
        error_log("JWTMiddleware: All headers: " . print_r($headers, true));

        if (!isset($headers['Authorization'])) {
            throw new Exception('Authorization header not found');
        }

        $authHeader = $headers['Authorization'];
        list($jwt) = sscanf($authHeader, 'Bearer %s');
        if (!$jwt) {
            throw new Exception('Bearer token not found');
        }

        $decoded = $this->jwtService->verifyToken($jwt);
        if (!$decoded) {
            throw new Exception('Invalid token');
        }

        error_log("JWTMiddleware: Token verification completed");
        return $decoded;
    }
}
?>