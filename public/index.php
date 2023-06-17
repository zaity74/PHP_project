<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../public/index.php';

// Initialisation de certaines choses
use App\Controller\ContactController;
use App\Controller\IndexController;
use App\Routing\RouteNotFoundException;
use App\Routing\Router;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader; 

use Doctrine\ORM\EntityManager; 
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

// Mode développement 
$isDevMode = $_ENV['APP_ENV'] === 'dev';

// DB CONNECTIONS
$dbParams = [
  'driver'   => 'pdo_mysql',
  'host'     => $_ENV['DB_HOST'],
  'port'     => $_ENV['DB_PORT'],
  'user'     => $_ENV['DB_USER'],
  'password' => $_ENV['DB_PASSWORD'],
  'dbname'   => $_ENV['DB_NAME'],
];

$dsn = "mysql:dbname={$dbParams['dbname']};host={$dbParams['host']}:{$dbParams['port']};charset=utf8";

// ENTITY 
$paths = [__DIR__ . '/../src/Entity']; 

// ENTITY MANAGER 
$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
$connection = DriverManager::getConnection($dbParams, $config);
$entityManager = new EntityManager($connection, $config);

// Twig
$loader = new FilesystemLoader(__DIR__ . '/../templates/');
$twig = new Environment($loader, [
  'debug' => $_ENV['APP_ENV'] === 'dev',
  'cache' => __DIR__ . '/../var/twig/',
]);

// Appeler un routeur pour lui transférer la requête
$router = new Router($twig);
$router->addRoute(
  'homepage',
  '/',
  'GET',
  IndexController::class,
  'home'
);
$router->addRoute(
  'contact_page',
  '/contact',
  'GET',
  ContactController::class,
  'contact'
);

try {
  $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
  $httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
  $router->execute($requestUri, $httpMethod);
} catch (RouteNotFoundException $ex) {
  http_response_code(404);
  echo "Page not found";
}
