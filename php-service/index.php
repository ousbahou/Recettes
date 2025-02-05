<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require 'vendor/autoload.php';

use Dotenv\Dotenv;
use MongoDB\Client;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mongoUser = getenv('MONGO_USERNAME');
$mongoPass = getenv('MONGO_PASSWORD');
$mongoHost = getenv('MONGO_HOST') ?: 'mongodb';
$mongoPort = getenv('MONGO_PORT') ?: '27017';
$mongoDB   = getenv('MONGO_DATABASE');

if (!$mongoUser || !$mongoPass || !$mongoDB) {
    http_response_code(500);
    echo json_encode(["error" => "⚠️ Variables d'environnement MongoDB manquantes. Vérifiez votre fichier .env"]);
    exit();
}

try {
    $mongoUri = "mongodb://$mongoUser:$mongoPass@$mongoHost:$mongoPort/$mongoDB?authSource=admin";
    $mongoClient = new Client($mongoUri);
    $db = $mongoClient->$mongoDB;
    $collection = $db->recettes;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "❌ Échec de connexion à MongoDB", "details" => $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $recettes = $collection->find()->toArray();
        http_response_code(200);
        echo json_encode($recettes);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "❌ Erreur lors de la récupération des recettes", "details" => $e->getMessage()]);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['titre']) || !isset($data['description']) || empty($data['titre']) || empty($data['description'])) {
        http_response_code(400);
        echo json_encode(["error" => "⚠️ Données incomplètes"]);
        exit();
    }

    try {
        $collection->insertOne([
            'titre' => $data['titre'],
            'description' => $data['description']
        ]);
        http_response_code(201);
        echo json_encode(["message" => "✅ Recette ajoutée avec succès"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "❌ Erreur lors de l'ajout de la recette", "details" => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "❌ Méthode non supportée"]);
}
?>
