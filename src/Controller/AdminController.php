<?php

namespace App\Controller;

use PDO;
use App\Utils\AuthToken;
use App\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use \Exception;


class AdminController extends AbstractController
{

    // Admin PAGE 
    #[Route("/admin", name: "adminpage")]
    public function Admin()
    {
       // Vérifier si le cookie authToken existe
       $user = $this->isLoggedin($this->db);

       // LISTE FILM 
       $statement='SELECT * FROM Film';
       $stmt = $this->db->query($statement);
       $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //All genres
        $statement='SELECT * FROM Genres';
        $stmt = $this->db->query($statement);
        $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

         //All users
         $statement='SELECT * FROM Users';
         $stmt = $this->db->query($statement);
         $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

       if (is_array($user) && isset($user['isAdmin']) && $user['isAdmin'] == true) 
       {
            // Rediriger vers la page de login
            echo"<h1>Bienvenue ".$user['username']."</h1>";
            return $this->twig->render('admin/admin.html.twig',['user' => $user, 'movies' => $movies, 'genres' => $genres, 'users' => $users]);
       } else {
           echo "Vous n'êtes pas connecté en tant que Administrateur";
           return $this->twig->render('admin/admin.html.twig',['you are not connected as admin']);
       }
    }

    // CREATE A MOVIE
    #[Route("/admin", name: "register_movie", httpMethod: "POST" )]
    public function registerMovie()
    {
        if ($_POST) {
            // Récupérer les données soumises depuis le formulaire
            $name = $_POST['name'];
            $description = $_POST['description'];
            $slug = $_POST['slug'];
            $genres = $_POST['genre'];

            

            // Enregistrer le nouvel animal dans la base de donnée
            $statement = "INSERT INTO Film (name, description, slug, genre_id) VALUES (:name, :description, :slug, :genres)";
            $stmt = $this->db->prepare($statement);
            $stmt->execute(['name' => $name,'description' => $description, 'slug' => $slug, 'genres' => $genres]);

            // Rediriger vers la page affichant tous les films
            header('Location: /admin');
            exit();

        }
        // Récupérer les films existants depuis la base de données
        $statement = 'SELECT * FROM Film';
        $stmt = $this->db->query($statement);
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->twig->render('admin/admin.html.twig', ['movies' => $movies]);
    }

}