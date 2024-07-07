<?php
// Path: backend/src/Service/JWTService.php
namespace Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    private $secretKey;

    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function generateToken($payload)
    {
        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function verifyToken($token)
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            return false;
        }
    }
}
?>
