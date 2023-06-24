# Sciences-U - B3 IW - PHP MVC - 2023

### Requirements
---

- Apache 2.4
- PHP 8.1
- MySQL 5.7
- Composer 2

### Usage
---

### Installation
---

```
docker-compose up -d
```

### Configuration database
---

```
# Créer la base de donnée si cette base n'hesite pas encore 
# -f signifie --force pour force l'excecution 
- bin/console doctrine:database:create -f

# met a jour les entites en base de donnée
- bin/console doctrine:schema:update -f

```

### Documentation
---

### DB Configuration

La configuration de la base de données doit être inscrite dans un fichier `.env.local`, sur le modèle du fichier `.env`.

## Cours

Le cours complet se trouve à [cette adresse](https://ld-web.github.io/su-2023-php-mvc-course/).
