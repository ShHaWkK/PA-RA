<?php
namespace Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Entity\RouteModel;
use Entity\CollectionModel;
use Entity\DeliveryModel;
use Entity\ServiceRegistrationModel;
use Entity\ServiceModel;
use DateTime;

class PlanningController
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function processRequest($method, $uriParts, $input)
    {
        if ($method === 'GET' && isset($uriParts[0])) {
            $userId = $_GET['user_id'] ?? null;
            $startDate = $_GET['startDate'] ?? null;
            $endDate = $_GET['endDate'] ?? null;
            return $this->getPlanning($userId, $startDate, $endDate);
        }

        http_response_code(405);
        return ['error' => 'Method Not Allowed'];
    }

    private function getPlanning($userId, $startDate = null, $endDate = null)
    {
        try {
            $routeRepository = $this->entityManager->getRepository(RouteModel::class);
            $collectionRepository = $this->entityManager->getRepository(CollectionModel::class);

            $queryBuilderRoutes = $routeRepository->createQueryBuilder('r');
            $queryBuilderCollections = $collectionRepository->createQueryBuilder('c');

            // Ajouter le filtre de date si les dates de début et de fin sont fournies
            if (!empty($startDate) && !empty($endDate)) {
                $startDateObj = new \DateTime($startDate);
                $endDateObj = (new \DateTime($endDate))->setTime(23, 59, 59); // Fin de journée pour l'endDate

                $queryBuilderRoutes->andWhere('r.start_time BETWEEN :startDate AND :endDate')
                    ->setParameter('startDate', $startDateObj)
                    ->setParameter('endDate', $endDateObj);

                $queryBuilderCollections->andWhere('c.collection_date BETWEEN :startDate AND :endDate')
                    ->setParameter('startDate', $startDateObj)
                    ->setParameter('endDate', $endDateObj);
            }

            // Ajouter le filtre pour l'utilisateur si l'ID utilisateur est fourni
            if (!empty($userId)) {
                $queryBuilderRoutes->andWhere('r.driver = :userId')
                    ->setParameter('userId', $userId);

                $queryBuilderCollections->andWhere('c.volunteer = :userId')
                    ->setParameter('userId', $userId);
            }

            // Exécuter les requêtes
            $routes = $queryBuilderRoutes->getQuery()->getResult();
            $collections = $queryBuilderCollections->getQuery()->getResult();

            // Construire la réponse JSON
            $response = [
                'routes' => array_map(function ($route) {
                    return $route->jsonSerialize();
                }, $routes),
                'collections' => array_map(function ($collection) {
                    return $collection->jsonSerialize();
                }, $collections),
            ];

            http_response_code(200);
            return $response;

        } catch (\Exception $e) {
            http_response_code(500);
            return ['error' => 'Internal Server Error'];
        }
    }

}