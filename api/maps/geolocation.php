<?php 

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/config.php';

use \GuzzleHttp\Client;

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    if(isset($_GET['action']) && $_GET['action'] === 'get-geolocation') {
        $unitNumber = isset($_GET['unitNumber']) ? $_GET['unitNumber'] : '';
        $street = isset($_GET['street']) ? $_GET['street'] : '';
        $city = isset($_GET['city']) ? $_GET['city'] : '';
        $province = isset($_GET['province']) ? $_GET['province'] : '';
        $postalCode = isset($_GET['postalCode']) ? $_GET['postalCode'] : '';
        $country = isset($_GET['country']) ? $_GET['country'] : '';

        $data = trim("$unitNumber $street, $city, $province, $postalCode, $country");
        $httpClinet = new Client([
            'base_uri' => 'https://nominatim.openstreetmap.org',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'Journeolink/1.0 ' . EMAIL
            ]
        ]);
        
        try {
            $res = $httpClinet->request('GET', '/search', [
                'query' => [
                    'q' => $data,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => 1
                ]
            ]);
            echo $res->getBody();
            exit;
        } catch (Exception $e) {
            echo json_encode(['error' => 'Failed to fetch geolocation data']);
            exit;
        }
        $data = str_replace(' ', '%20', $data);
        echo json_encode($data);
    }

}

?>
