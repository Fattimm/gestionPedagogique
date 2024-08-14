<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Src\Core\Router;
use Src\Core\Database\MysqlDatabase;
use Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;

// Démarrage de la session
session_start();
define('ROOT', '/var/www/html/gestionPedagogique');

// Chargement des variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configuration de la base de données
$database = new MysqlDatabase(
    $_ENV['DSN'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD']
);

// Chargement de la configuration des routes depuis YAML
$configPath = __DIR__ . '/../config/config.yaml';
$config = Yaml::parseFile($configPath);

// Définition des routes
foreach ($config['routes'] as $route) {
    $method = strtolower($route['method']);
    Router::$method($route['path'], [
        'Controller' => $route['controller'],
        'action' => $route['action']
    ]);
}

// Routage de la requête
Router::routePage($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $database);
