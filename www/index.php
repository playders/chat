<?php
    require_once('db.php');

    $message_connexion = "";
    $message_inscrire = "";


    function connexion($action, $email, $psw, $pseudo) {
        $message = "";
        // Connexion à la base données
        $connection = connectDB();
        // Vérifier si un des 3 champs est vide.
        if (empty($email) || empty($psw) || empty($pseudo)){
            $message = "Veuillez entrez un E-mail et un mot de passe";
        }
        /* Requête "Select" retourne un jeu de résultats*/
        if (!$message) {
            if ($action == 'inscrire') {
                $result = $connection->prepare("SELECT * FROM users WHERE email = '" . $email . "'");
                $result->execute();
                if ($result->fetchColumn() == 0) {
                    $connection->exec("INSERT INTO users (email, psw, pseudo) VALUES ('" . $email . "', '" . sha1($psw) . "','" . ($pseudo) . "')");
                    $statement = $connection->prepare("SELECT * FROM users WHERE email = '" . $email . "'");
                    $statement->execute();
                    $objet = $statement->fetchObject();
                    if ($objet) {
                        $_SESSION['connecter'] = $objet->id;
                        header("Location: page2.php");
                    } else {
                        $message = "Impossible de créer l'utilisateur";
                    }
                } else {
                    $message = "Cet e-mail existe déjà ou n'est pas valable.";
                }
            } else {
                $statement = $connection->prepare("SELECT * FROM users WHERE (email = '" . $email . "' OR pseudo = '" . $email . "') AND psw = '" . sha1($psw) . "'");
                $statement->execute();
                $result = $statement->fetchObject();
                if (!$result) {
                    $message = "Email/pseudo ou mot de passe introuvable";
                } else {
                    $statement->execute();
                    $objet = $statement->fetchObject();
                    $_SESSION['connecter'] = $objet->id;
                    header("Location: page2.php");
                }        
            }
        }

        return $message;
    }

    session_start();

  // S'inscrire
  if (isset($_POST['inscrire'])) {
    $email = $_POST['email'];
    $psw = $_POST['psw'];
    $pseudo = $_POST['pseudo'];
    $message_inscrire = connexion('inscrire', $email, $psw, $pseudo);
  }

  // Se connecter
  if (isset($_POST['connecter'])) {
    $email_pseudo = $_POST['email_pseudo'];
    $psw = $_POST['psw'];
    $message_connexion = connexion('connecter', $email_pseudo, $psw, true);
  }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Site Ephram</title>
</head>
<body>
    <!-- Commentaire -->
    <ul id="nav">
        <li class="item1"><a href="https://ephram.lahode.ch/" target="_blank">Mon site de jeu</a></li>
    </ul>
    <div id="se-connecter">
        <h1 id="titre-se-connecter">Se connecter</h1>
        <form name="formulaire" action="index.php" method="post">
            <input type="text" name="email_pseudo" placeholder="E-mail ou pseudo">
            <input type="password" name="psw" placeholder="Mot de passe">
            <button id="signin-button" name="connecter">Se Connecter</button>
        </form>
        <p class="error"><?php echo $message_connexion ?></p>
    </div>
    <hr />
    <div id="s-inscrire">
        <h1 id="titre-s-inscrire">S'inscrire</h1>
        <form name="formulaire" action="index.php" method="post">
            <input type="email" name="email" placeholder="E-mail" />
            <input type="password" name="psw" placeholder="Mot de passe" />
            <input type="pseudo" name="pseudo" placeholder="pseudo" />
            <button id="signup-button" name="inscrire">S'inscrire</button>
        </form>
        <p class="error"><?php echo $message_inscrire ?></p>
    </div>
    <script src="main.js"></script>
</body>
</html>