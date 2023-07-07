<?php

namespace App\Controller;

use PDO;
use App\Utils\AuthToken;
use App\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use \Exception;

class IndexController extends AbstractController
{
  #[Route("/", name: "homepage")]
  public function home()
  {
    // Vérifier si le cookie authToken existe
    $user = $this->isLoggedin($this->db);
    if ($user) {
         // Rediriger vers la page de login
         return $this->twig->render('index.html.twig',['user' => $user]);
    } else {
        return $this->twig->render('index.html.twig',['your are not login']);
    }
  }
}
