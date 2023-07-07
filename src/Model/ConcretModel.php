<?php
namespace App\Model;

use PDO;
use PDOException;

class ConcretModel
{
    public function executeQuery($query, $params = [])
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function executeStatement($query, $params = [])
    {
        $prepared = $this->pdo->prepare($query);

        return $prepared->execute($params);
    }

    // LIST ALL DATA
    public function listAllMovie()
    {
        /*$sql = "SELECT * FROM Film";
        $stmt = $this->_db->query($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;*/
    }


    // INSERT MOVIE INTO DATABASE
    public function insertMovie($name, $description, $image = "")
    {
        $data = [
            'name' => $name,
            'description' => $description,
            'image' => $image,
        ];
        $sql = "INSERT INTO Film (name, description, image) VALUES (:name, :description, :image)";
       // $stmt = $this->getBdd()->prepare($sql);
       // $stmt->execute($data);
    }

    // DELETE MOVIE
    public function deleteMovie($id)
    {
        //$sql = "DELETE FROM Film WHERE id=?";
        //$stmt = $this->getBdd()->prepare($sql);
        //$stmt->execute([$id]);
    }

    // UPDATE MOVIE
    public function updateMovie($id, $name, $description, $image = "")
    {
        $data = [
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'image' => $image,
        ];
        $sql = "UPDATE Film SET name=:name, description=:description, image=:image WHERE id=:id";
       // $stmt = $this->getBdd()->prepare($sql);
        //$stmt->execute($data);
    }
}
