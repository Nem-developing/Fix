<?php
session_start();
if($_SESSION['utilisateur']){
    header('Location: index.php');
    exit();
}


if(isset($_POST['submit'])){
    // On récupère les valleurs du form.    
    $utilisateurconnexion = $_POST['utilisateur'];
    $mdp = $_POST['mdp'];
        
    include './config/config.php';  // Import des informations de connexion à la base de données.
    // Établissement de la connexion au serveur mysql.
    $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
    // Commande SQL permetant de récupérer la liste des tickets archivés..
    $req = 'SELECT * FROM connexion WHERE utilisateur = "' . $utilisateurconnexion . '";';
    // Envoie au serveur la commande via le biais des informations de connexion.
    $res = $cnx->query($req);

    // Boucle tant qu'il y a de lignes corespondantes à la requettes
    while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {
        
        if (password_verify($_POST['mdp'], $ligne->motdepasse)){
                      
            $utilisateurconnecte = $ligne->utilisateur;
            $_SESSION['utilisateur'] = $utilisateurconnecte;
            $utilisateurperms = $ligne->permissions;
            $_SESSION['permissions'] = $utilisateurperms;
            $utilisateurid = $ligne->id;
            $_SESSION['utilisateurid'] = $utilisateurid;
        }      
        
    }    
    
    if ($_SESSION['utilisateur']){
        header('Location: index.php');
        exit();
    } else {
        $erreurmdpoupass = True;
    }
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <link href="css/connexion.css" rel="stylesheet" type="text/css"/>
        <title>Fix - Tickets</title>
        <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
    </head>
    <body>
        <div id="formulaire" class="bg-dark text-white">
            <div class="p-3 mb-2 bg-info text-white" id="titre">Fix - Gestionnaire de tickets</div>
            <form method="post">
                <div class="form-group">
                    <label for="exampleInputEmail1" name="utilisateur" id="id">Identifiant</label>
                    <input type="text" name="utilisateur" class="form-control text-white" id="exampleInputEmail1" placeholder="Identifiant">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1" name="mdp" class="text-white">Mot de passe</label>
                    <input type="password" name="mdp" class="form-control" id="exampleInputPassword1" placeholder="Mot de passe sécurisé">
                </div>
                <?php
                if ($erreurmdpoupass == True){
                    echo '<div class="form-group">
                                <div class="text-danger">Votre identifiant ou votre mot de passe est faux.</div>
                          </div>';
                }
                ?>
                <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block">Connexion</button>
            </form>
        </div>
        
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    </body>
</html>
