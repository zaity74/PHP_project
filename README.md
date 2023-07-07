# Sciences-U - B3 IW - PHP MVC - 2023

## Démarrage

### Composer

Pour récupérer les dépendances déclarées dans `composer.json` et générer l'autoloader PSR-4, exécuter la commande suivante :

```bash
composer install
```

### DB Configuration

La configuration de la base de données doit être inscrite dans un fichier `.env.local`, sur le modèle du fichier `.env`.

### Démarrer l'application

Commande :

```bash
composer start
```

## Mon projet "Movie Applications"

### Résumé 

L'objectif de mon application est de lister une série de films, dans la page movies.
Nous pouvons voir le détails du film en accédent à l'url du film "/movies/{slug}, et en cliquant dessus. 
Dans la page détails l'utilisateur peut voir le nombre de commentaire et la note moyenne du film des utilisateurs. Il peut également ajouter une review et une note. 
Dans la liste des films l'utilisateur peut liker un film et celui apparaitra dans sa liste de favoris. Il peut également la supprimer de ses favoris. 
Enfin l'utilisateur à la possibilité de se connecter et de se déconnecter de l'application. 
Si celui-ci est l'admin du site, il pourra accéder à la page d'administration pour ajouter un film ou le supprimer. 


### Processus 

Connexion base de donnée : 

Pour réaliser cette application, il ma fallut tout d'abord connecter celle-ci à ma base de donnée. 

```bash
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
```
Création de mes controllers :

Dans un controllers abstract, je déclare dans le constructor de la classe un nouvel environnement twig et PDO. Qui me permettra de les réutiliser dans toute mes classes controllers.  

Pour cette application j'ai besoin de 6 autres controllers. 
- Admin pour gérer l'administration, exécuter la page admin, et méthodes (créer un film) depuis l'administration. 
- User pour gérer l'authentification et l'inscription du user
- Index pour la page d'accueil 
- Movie pour lister les films, voir le détails d'un film, ajouter un film au favoris et supprimer un film
- Review pour gérer les commentaire des utilisateurs pour chaque film. Créer une review et delete une review. 
- Favoris, pour afficher les favoris des utilisateurs et supprimer

Gestion des utilis: 

Pour ce projet j'ai crée un dossier Utils dans lequel j'ai un fichier authToken, qui me permet de gérer les tokens d'authentification. Que ce soit pour la génération d'un token, la vérification et extraire l'id d'un user depuis le token. 

Pour ce faire j'ai utilisé un package de firebase JWT, pour encoder et decoder mes token (payload, secretkey, code). 

