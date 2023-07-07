<?php

namespace App\Controller;

use PDO;
use App\Utils\AuthToken;
use App\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use \Exception;


class UserController extends AbstractController
{

    // REGISTER PAGE 
    #[Route("/inscription", name: "registerpage")]
    public function register()
    {
        return $this->twig->render('connect/register.html.twig');
    }

     // LOGIN PAGE 
     #[Route("/login", name: "loginpage")]
     public function login()
     {
        // Vérifier si le cookie authToken existe
        $user = $this->isLoggedin($this->db);
        if ($user) {
             // Rediriger vers la page de login
             return $this->twig->render('connect/login.html.twig',['user' => $user]);
        } else {
            echo "Vous n'êtes pas connecté";
            return $this->twig->render('connect/login.html.twig',['connexion']);
        }
     }

    // CREATE A USER
    #[Route("/inscription", name: "register_user", httpMethod: "POST")]
    public function registerUser()
    {
        // Récupération de la requête
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Vérifier si l'utilisateur existe déjà dans la base de donnée
        $statement = "SELECT * FROM Users WHERE username = :username OR email = :email";
        $stmt = $this->db->prepare($statement);
        $stmt->execute(['username' => $username, 'email' => $email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            // L'utilisateur existe déjà, afficher un message d'erreur ou rediriger vers une page d'erreur
            echo "<h3>L'utilisateur existe déjà</h3>";
        }else{
            // Hasher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Enregistrer le nouvel utilisateur dans la base de donnée
            $statement = "INSERT INTO Users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->db->prepare($statement);
            $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashedPassword]);
            
            echo "<h3>Vous êtes bien enregistré au site</h3>";
            // Rediriger vers la page de connexion ou afficher un message de succès
            return $this->twig->render('connect/register.html.twig', ['message' => 'L\'utilisateur a été enregistré avec succès.']);
        }
    }

   // LOGGIN USER
    #[Route("/login", name: "login_user", httpMethod: "POST")]
    public function userLoggin()
    {
        if ($_POST)
        {
            // Récupérer les données soumises depuis le formulaire
            $email = $_POST['email'];

            // Vérifier l'existence de l'utilisateur dans la base de données
            $statement = "SELECT * FROM Users WHERE email = :email";
            $stmt = $this->db->prepare($statement);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (empty($user)) {
                // L'utilisateur n'existe pas, afficher un message d'erreur
                header('Location: /inscription');
                exit();
            } else {
                // Générer un token
                $token = AuthToken::generateToken($user['id']);
                $isTokenValid = AuthToken::verifyToken($token);
                $user['token'] = $token;

                if (!$isTokenValid) {
                    // Rediriger vers la page de connexion
                    echo "<h3>Token invalide</h3>";
                    return $this->twig->render('connect/login.html.twig', ['token is not valid']);
                }
                // Stocker le token dans un cookie
                setcookie('authToken', $user['token'], time() + 3600, '/');

                // Vérifier si le cookie authToken existe
                $user = $this->isLoggedin($this->db);

                if ($user) {
                    return $this->twig->render('connect/login.html.twig',['user' => $user]);
                } else {
                    header('Location: /login');
                    exit();
                }
            }
        }
    }

    // LOGOUT USER 
    #[Route("/api/logout", name: "logout_user", httpMethod: "GET")]
    public function logout()
    {
        // Supprimer le cookie authToken en le réinitialisant avec une date d'expiration passée
        setcookie('authToken', '', time() - 3600, '/');
        // Rediriger vers la page de connexion ou afficher un message de déconnexion réussie
    }
}