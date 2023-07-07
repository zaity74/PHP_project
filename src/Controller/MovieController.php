<?php

namespace App\Controller;

use PDO;
use App\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;


class MovieController extends AbstractController
{
    private $movies;

    public function addMovies($movie)
    {
        $this->movies[] = $movie;
    }

    public function getMovies()
    {
        return $this->movies;
    }


    // GET ALL MOVIES
    #[Route("/movies", name: "movies", httpMethod: "GET" )]
    public function getAllMovies(): string
    {
        // All movie
        $statement='SELECT * FROM Film';
        $stmt = $this->db->query($statement);
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //All genres
        $statement='SELECT * FROM Genres';
        $stmt = $this->db->query($statement);
        $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $user = $this->isLoggedin($this->db);
        if ($user) {
                // Rediriger vers la page de login
                return $this->twig->render('movie/show.html.twig',['user' => $user, 'movies' => $movies, 'genres' => $genres]);
        } else {
            echo "Vous n'êtes pas connecté";
            return $this->twig->render('movie/show.html.twig',['movies' => $movies, 'genres' => $genres]);

        }
    }

    // SHOW MOVIE DETAILS
    #[Route("/movies/{slug}", name: "movie_details", httpMethod: "GET")]
    public function movieDetails(): string
    {
        $url = $_SERVER['REQUEST_URI'];
        $slug = basename(parse_url($url, PHP_URL_PATH));
    
        // Afficher le film correspondant au slug
        $statement = "SELECT * FROM Film f WHERE f.slug = :slug";
        $stmt = $this->db->prepare($statement);
        $stmt->execute(['slug' => $slug]);
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);
        $movieId = $movie['ID'];


        //All genres
        $statement='SELECT * FROM Genres';
        $stmt = $this->db->query($statement);
        $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtenir le nombre total de critiques pour le film
        $statement = "SELECT COUNT(*) AS total_reviews FROM Reviews WHERE film_id = :movieId";
        $stmt = $this->db->prepare($statement);
        $stmt->execute(['movieId' => $movieId]);
        $totalReviews = $stmt->fetch(PDO::FETCH_ASSOC)['total_reviews'];

        // Calculer la moyenne des ratings des critiques pour le film
        $statement = "SELECT AVG(rating) AS average_rating FROM Reviews WHERE film_id = :movieId";
        $stmt = $this->db->prepare($statement);
        $stmt->execute(['movieId' => $movieId]);
        $averageRating = $stmt->fetch(PDO::FETCH_ASSOC)['average_rating'];


        // Afficher les reviews correspondant à l'id du film
        $statement = 'SELECT * FROM Reviews WHERE film_id = :movieId';
        $stmt = $this->db->prepare($statement);
        $stmt->execute(['movieId' => $movieId]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // GET REVIEW USER INFO
        $authors = [];
        foreach ($reviews as $rev) {
            $id = $rev['user_id'];
            // Récupérer les infos du user dans la review
            $statement = "SELECT * FROM Users WHERE id = :id";
            $stmt = $this->db->prepare($statement);
            $stmt->execute(['id' => $id]);
            $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            $authors[$id] = $userInfo;
        }

        $user = $this->isLoggedin($this->db);
        if ($user) {
                // Afficher les détails du film
            return $this->twig->render('movie/showDetails.html.twig', 
            ['user' => $user,'movie' => $movie, 'reviews' => $reviews, 
            'authors' => $authors, 'averageRating' => $averageRating, 'totalReviews' => $totalReviews, 'genres' => $genres]);
        } else {
            return $this->twig->render('movie/showDetails.html.twig', ['movie' => $movie, 
            'reviews' => $reviews, 'authors' => $authors,
            'averageRating' => $averageRating, 'totalReviews' => $totalReviews]);

        }
    }

    // DELETE MOVIE
    #[Route("/api/movies/{id}", name: "api_delete_movie", httpMethod: "DELETE")]
    public function apiDeleteMovie()
    {
        $user = $this->isLoggedin($this->db);
        $url = $_SERVER['REQUEST_URI'];
        $id = basename(parse_url($url, PHP_URL_PATH));

        if ($user) {
            // Supprimer les enregistrements correspondants dans la table favoris
            $statement = "DELETE FROM Favoris WHERE film_id = :id";
            $stmt = $this->db->prepare($statement);
            $stmt->execute(['id' => $id]);

            // Supprimer les enregistrements correspondants dans la table reviews
            $statement = "DELETE FROM Reviews WHERE film_id = :id";
            $stmt = $this->db->prepare($statement);
            $stmt->execute(['id' => $id]);

            // Supprimer le film de la base de données
            $statement = "DELETE FROM Film WHERE ID = :id";
            $stmt = $this->db->prepare($statement);
            $stmt->execute(['id' => $id]);

            // Retourner une réponse JSON indiquant le succès de la suppression
            return new JsonResponse([
                'success' => true
            ]);
        } else {
            // Retourner une réponse JSON indiquant le succès de la suppression
            return new JsonResponse([
                'success' => false
            ]);
        }
    }
    
    // UPDATE A MOVIE


    // ADD TO FAVORITE
    #[Route("/api/movies/add-favorite/{userId}/{movieId}", name: "add_favorite_movie", httpMethod: "POST")]
    public function addFavoriteMovie()
    {
        $url = $_SERVER['REQUEST_URI'];
        $path = parse_url($url, PHP_URL_PATH);
        $pathSegments = explode('/', $path);

        $userId = $pathSegments[count($pathSegments) - 2];
        $movieId = $pathSegments[count($pathSegments) - 1];
        // Récupérer l'utilisateur connecté
        $user = $this->isLoggedin($this->db);
        $loggedInUserId = $user['id'];

        // Vérifier si l'ID de l'utilisateur connecté correspond à l'ID fourni dans l'URL
        if ($loggedInUserId != $userId) {
            // L'utilisateur connecté ne correspond pas à l'ID fourni, renvoyer une réponse d'erreur
            return new JsonResponse(['error' => 'Access denied.'], 403);
        }
        
        // Vérifier si le film existe
        $statement = $this->db->prepare("SELECT COUNT(*) FROM Film WHERE ID = :movieId");
        $statement->execute(['movieId' => $movieId]);
        $movieExists = $statement->fetchColumn();

        if (!$movieExists) {
            // erreur Le film n'existe pas
            return new JsonResponse(['error' => 'Movie not found.'], 403);
        }

        // Vérifier si l'utilisateur a déjà ajouté ce film à ses favoris
        $statement = $this->db->prepare("SELECT COUNT(*) FROM Favoris WHERE user_id = :userId AND film_id = :movieId");
        $statement->execute(['userId' => $userId, 'movieId' => $movieId]);
        $favoriteExists = $statement->fetchColumn();

        if ($favoriteExists) {
            // Erreur le film est déjà ajouté aux favoris
            echo "Movie already added to favorites.";
            return new JsonResponse(['error' => 'Movie already added to favorites'], 403);
        }

        
        // Insérer le favori dans la table Favoris avec l'ID spécifié
        $statement = "INSERT INTO Favoris (film_id, user_id) VALUES (:film_id, :user_id)";
        $stmt = $this->db->prepare($statement);
        $stmt->execute([
            'film_id' => $movieId,
            'user_id' => $userId
        ]);
        // Récupérer les films restants depuis la base de données
        $statement = 'SELECT * FROM Favoris';
        $stmt = $this->db->query($statement);

        // Renvoyer une réponse réussie
        return new JsonResponse(['message' => 'Movie added to favorites.'], 200);
    }

}


