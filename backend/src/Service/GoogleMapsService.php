<?php
namespace Service;

class GoogleMapsService
{
    public function getOptimizedRoute(array $addresses, string $apiKey): array
    {
        try {
            // Étape 1 : Obtenir la matrice des distances
            $distanceMatrix = $this->getDistanceMatrix($addresses, $apiKey);

            // Étape 2 : Résoudre le TSP (ici avec un algorithme glouton simple)
            $optimizedOrder = $this->solveTSP($distanceMatrix);

            // Étape 3 : Obtenir l'itinéraire détaillé
            $route = $this->getDetailedRoute($addresses, $optimizedOrder, $apiKey);

            return $route;

        } catch (\Exception $e) {
            // Gérer les exceptions et les erreurs
            return ['error' => $e->getMessage()];
        }
    }

    private function getDistanceMatrix(array $addresses, string $apiKey): array
    {
        $origins = implode('|', array_map('urlencode', $addresses));
        $destinations = $origins;

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$origins&destinations=$destinations&key=$apiKey";

        $response = $this->makeApiRequest($url);

        if ($response['status'] !== 'OK') {
            throw new \Exception("Error fetching distance matrix: " . $response['error_message'] ?? 'Unknown error');
        }

        return $response;
    }

    private function solveTSP(array $distanceMatrix): array
    {
        $n = count($distanceMatrix['rows']);
        $visited = array_fill(0, $n, false);
        $order = [0];
        $visited[0] = true;

        for ($i = 1; $i < $n; $i++) {
            $lastCity = $order[count($order) - 1];
            $nextCity = -1;
            $minDistance = PHP_INT_MAX;

            for ($j = 0; $j < $n; $j++) {
                if (!$visited[$j]) {
                    $distance = $distanceMatrix['rows'][$lastCity]['elements'][$j]['distance']['value'];
                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $nextCity = $j;
                    }
                }
            }

            if ($nextCity === -1) {
                throw new \Exception("Error solving TSP: No unvisited city found.");
            }

            $order[] = $nextCity;
            $visited[$nextCity] = true;
        }

        return $order;
    }

    private function getDetailedRoute(array $addresses, array $order, string $apiKey): array
    {
        $waypoints = [];
        foreach ($order as $index) {
            if ($index != 0 && $index != count($addresses) - 1) {
                $waypoints[] = 'via:' . urlencode($addresses[$index]);
            }
        }
        $waypointsString = implode('|', $waypoints);

        $origin = urlencode($addresses[$order[0]]);
        $destination = urlencode($addresses[$order[count($order) - 1]]);

        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=$origin&destination=$destination&waypoints=$waypointsString&key=$apiKey";

        $response = $this->makeApiRequest($url);

        if ($response['status'] !== 'OK') {
            throw new \Exception("Error fetching detailed route: " . $response['error_message'] ?? 'Unknown error');
        }

        return $response;
    }

    private function makeApiRequest(string $url): array
    {
        $response = file_get_contents($url);

        if ($response === false) {
            throw new \Exception("Error making API request to URL: $url");
        }

        return json_decode($response, true);
    }
}