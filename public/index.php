<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../public/index.php';

// Initialisation de certaines choses
use App\Controller\ContactController;
use App\Controller\IndexController;
use App\Controller\MovieController;
use App\Routing\RouteNotFoundException;
use App\Routing\Router;
use App\DependencyInjection\Container;

use App\Model\AbstractModel;

use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader; 


$query = $_GET;
$request = $_POST;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env.local');

// Doctrine
$isDevMode = $_ENV['APP_ENV'] === 'dev';

[
  'DB_HOST'     => $host,
  'DB_PORT'     => $port,
  'DB_NAME'     => $dbname,
  'DB_CHARSET'  => $charset,
  'MYSQL_USERNAME' => $user,
  'MYSQL_ROOT_PASSWORD' => $password
] = $_ENV;

$dsn = "mysql:dbname=$dbname;host=$host:$port;charset=$charset";

try {
  $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $ex) {
  echo "Erreur lors de la connexion à la base de données : " . $ex->getMessage();
  exit;
}

// Twig
$loader = new FilesystemLoader(__DIR__ . '/../templates/');
$twig = new Environment($loader, [
  'debug' => $_ENV['APP_ENV'] === 'dev',
  'cache' => __DIR__ . '/../var/twig/',
]);

$serviceContainer = new Container();
$serviceContainer
  ->set(Environment::class, $twig)
  ->set(PDO::class, $pdo);

// Appeler un routeur pour lui transférer la requête
$router = new Router($serviceContainer);
$router->registerRoutes();

try {
  $router->execute($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (RouteNotFoundException $ex) {
  http_response_code(404);
  echo "Page not found";
}