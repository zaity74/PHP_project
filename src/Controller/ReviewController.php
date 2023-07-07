<?php

namespace App\Controller;

use PDO;
use App\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;


class ReviewController extends AbstractController
{

    // CREATE A REVIEW
    #[Route("/movies/{slug}", name: "create_movie_review", httpMethod: "POST" )]
    public function registerMovie()
    {
        $user = $this->isLoggedin($this->db);
        $url = $_SERVER['REQUEST_URI'];
        $slug = basename(parse_url($url, PHP_URL_PATH));
        if($user){
            // Get user id
            $userId = $user['id'];
            // GET movie id 
            $statement = "SELECT f.ID FROM Film f WHERE slug = :slug";
            $stmt = $this->db->prepare($statement);
            $stmt->execute(['slug' => $slug]);
            $movieId = $stmt->fetch(PDO::FETCH_ASSOC)['ID'];

            if ($_POST) {
                // Récupérer les données soumises depuis le formulaire
                $rating = $_POST['rating'];
                $commentaire = $_POST['commentaire'];
    
                // Enregistrer le nouvel animal dans la base de donnée
                $statement = "INSERT INTO Reviews (film_id, user_id, rating, commentaire) VALUES (:movieId, :userId, :rating, :commentaire)";
                $stmt = $this->db->prepare($statement);
                $stmt->execute(['movieId' => $movieId,'userId' => $userId, 'rating' => $rating, 'commentaire' => $commentaire]);
            }
            // Rediriger vers la page du film avec le slug
            header("Location: /movies/{$slug}");
            exit();
        }else{
            header("Location: /movies/{$slug}");
            exit();
            return $this->twig->render('movie/showDetails.html.twig',['user is not connected']);
        }
    }

    // DELETE REVIEW
    #[Route("/api/review/{id}", name: "api_delete_review", httpMethod: "DELETE")]
    public function apiDeleteReview()
    {
        $user = $this->isLoggedin($this->db);
        // Supprimer le film de la base de données
        $url = $_SERVER['REQUEST_URI'];
        $id = basename(parse_url($url, PHP_URL_PATH));
        if($user){
            $statement = "DELETE FROM Reviews WHERE id = :id";
            $stmt = $this->db->prepare($statement);
            $stmt->execute(['id' => $id]);
            // Retourner une réponse JSON indiquant le succès de la suppression
            return new JsonResponse([
                'success' => true
            ]);
        }else{
            // Retourner une réponse JSON indiquant le succès de la suppression
            return new JsonResponse([
                'success' => false
            ]);
        }
    }
}