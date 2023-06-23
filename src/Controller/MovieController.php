<?php 

namespace App\Controller;

use App\Model\AbstractModel;

require_once __DIR__ . '/../Controller/MovieController.php';

class MovieController extends AbstractModel
{
    private $movies;

    // we set movies as an array to list all he movies with the attr movie
    public function addMovies($movie)
    {
        $this -> movies[] = $movie;
    }

    public function getMovies()
    {
        return $this->movies;
    }

    // Method to get all the movies from database 
    public function getAllMovies()
    {
        $req = $this->getBdd()->query("SELECT * FROM Film");
        // on fetch tous les films dans la variable $movies
        $movies = $req->fetchAll();
        var_dump(($movies));

    }

}

