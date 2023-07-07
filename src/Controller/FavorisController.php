<?php

namespace App\Controller;

use PDO;
use App\Utils\AuthToken;
use App\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use \Exception;


class FavorisController extends AbstractController
{

     // FAVORIS PAGE 
     #[Route("/favoris", name: "favoris_page" )]
     public function favoris()
     {
        $user = $this->isLoggedin($this->db);
         if ($user) {
            $userId = $user['id'];
            // Sélectionner tous les favoris de la bdd
            $statement='SELECT * FROM Favoris WHERE user_id = :userId';
            $stmt = $this->db->prepare($statement);
            $stmt->execute(['userId' => $userId]);
            $favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $films = [];
            foreach ($favoris as $favori) {
                $id = $favori['film_id'];
                // Récupérer les infos du film dans le favori
                $statement = "SELECT * FROM Film WHERE id = :id";
                $stmt = $this->db->prepare($statement);
                $stmt->execute(['id' => $id]);
                $film = $stmt->fetch(PDO::FETCH_ASSOC);
        
                $films[] = $film;
            }
        
              // Rediriger vers la page des favoris avec les infos du user
              return $this->twig->render('favoris/favoris.html.twig',['films' => $films,'user' => $user,'favoris' => $favoris]);
         } else {
             return $this->twig->render('favoris/favoris.html.twig',['user is not connected']);
         }
     }

    // DELETE FAVORIS
    #[Route("/api/favoris/{id}", name: "api_delete_favoris", httpMethod: "DELETE")]
    public function apiDeleteFavoris()
    {
        // Supprimer le film de la base de données
        $url = $_SERVER['REQUEST_URI'];
        $id = basename(parse_url($url, PHP_URL_PATH));
        $statement = "DELETE FROM Favoris WHERE id = :id";
        $stmt = $this->db->prepare($statement);
        $stmt->execute(['id' => $id]);
    
        // Retourner une réponse JSON indiquant le succès de la suppression
        return new JsonResponse([
            'success' => true,
        ]);
    }
}   