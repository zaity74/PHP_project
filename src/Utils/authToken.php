<?php

namespace App\Utils;
use Firebase\JWT\JWT;
use \Exception;

class AuthToken
{

  // GENERATE TOKEN
  public static function generateToken($userId)
  {
     //$token = bin2hex(random_bytes($length));
         //return $token;
         // Clé secrète pour signer le token
         $secretKey = $_ENV['JWT_SECRET_KEY'];

         // Données du payload
         $payload = [
             'iss' => 'movie-app',
             'aud' => 'myapp',
             'sub' => $userId,
             'exp' => time() + 3600, 
             'iat' => time(), 
         ];
 
         // Génération du token
         $token = JWT::encode($payload, $secretKey, 'HS256');
 
         // Affichage du token
         return $token;
  }

  // VERIFY TOKEN
  public static function verifyToken($token)
  {
      try {
          // Récupérer la clé secrète
          $secretKey = $_ENV['JWT_SECRET_KEY'];

          // Créer les en-têtes du token
          $headers = new \stdClass();
          $headers->alg = 'HS256';
          $headers->typ = 'JWT';

          // Décoder le token en utilisant la clé secrète
          $decodedToken = JWT::decode($token, $secretKey, ['HS256']);

          // Accéder aux claims du token
          $userId = $decodedToken->sub;
          $expirationTime = $decodedToken->exp;

          // Token valide
          return true;
      } catch (Exception $e) {
          // Gérer les erreurs de token (par exemple, token invalide, expiré, etc.)
          return false;
      }
  }

   // GET USER FROM TOKEN 
   public static function getUserFromToken($token)
   {
       $secretKey = $_ENV['JWT_SECRET_KEY'];
       $decodedToken = JWT::decode($token, $secretKey, ['HS256']);
       $userId = $decodedToken->sub;
   
       return $userId;

   }

}