<?php
    require_once('db.php');

    $message_chat = "";

    session_start();

    // Deconnexion
    if (isset($_POST['deconnecter'])) {
        $_SESSION['connecter'] = false;
    }

    // Redirige vers la page d'accueil si pas connecté
    if (!$_SESSION['connecter']) {
        header("Location: index.php");
    } 

    // Connexion à la base données
    $connection = connectDB();

    // Enregistrer le message
    if (isset($_POST['enter'])) {
        $chat = $_POST['chat'];
        // Vérifier si un des 3 champs est vide.
        if (empty($chat)){
            $message_chat = "Veuillez entrez un text.";
        }
        if (!$message_chat) {
            $connection->exec("INSERT INTO chat (texte, user_id) VALUES ('" . addslashes($chat) . "', " . $_SESSION['connecter'] . ")");
        }
    }

    // Récupérer l'utilisateur.
    $statement = $connection->prepare("SELECT admin FROM `users` WHERE id = " . $_SESSION['connecter']);
    $statement->execute();
    $user = $statement->fetchObject();

    if (isset($_POST['supprimer'])) {
        if ($user->admin) {
            $connection->exec("DELETE FROM `chat` WHERE id = " . $_POST['chat_id']);
        }
    }

    // Récuperer les messages
    $statement = $connection->query("SELECT chat.texte, chat.date, users.pseudo, chat.id FROM `chat` INNER JOIN users ON users.id = chat.user_id");
    $statement->execute();
    $textes = $statement->fetchAll();
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
    <form name="formulaire" action="page2.php" method="post" id="form-button">
        <button name="deconnecter" id="deconnecter">Se déconnecter</button>
    </form>
    <h1 id="titre">
    Vous Etes Connecté
        </h1>
        <div>
            <form name="chat-formulaire" action="page2.php" method="post">
                <textarea name="chat" placeholder="chat" id="chat"></textarea>
                <button id="chat-button" name="enter">Chat</button>
            </form>
            <p class="error"><?php echo $message_chat ?></p>
        </div>
        <div class="textes">
            <?php foreach($textes as $chat) { ?>
            <div class="texte">
                <p><?php echo nl2br(stripslashes($chat[0])) ?></p>
                <div class="pseudo-info">
                    <?php echo $chat[1] ?><br />
                    <?php echo $chat[2] ?>
                </div>
                <?php if ($user->admin) { ?>
                <form name="chat-edit" action="page2.php" method="post">
                    <input type="hidden" name="chat_id" value="<?php echo $chat[3] ?>"></button>
                    <button name="supprimer">Supprimer</button>
                </form>
                <?php } ?>
            </div>
            <hr />
            <?php } ?>
        </div>
    <script src="main.js"></script>
</body>
</html>