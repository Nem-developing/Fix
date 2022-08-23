<?php
session_start();

if(!isset($_SESSION['utilisateur'])) {
  header('Location: ../connexion.php');
  exit();
}
?> 
<!DOCTYPE html>
<!--
Projet réalisé par Nem-developing, tout droits réservés.
-->
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <title>Fix - Tickets</title>
    </head>
    <body>

        <?php
        include '../includes/menu-enfants.php';
        ?>


 
        <div id="page">
            
            <?php
            include "../config/config.php"; // Import des données de connexion.
            date_default_timezone_set('UTC');   // On informe mysql de la zone temporelle souhaitée.
            $serveur = $_POST['srv'];       // On récupère les informations du formulaire précédent.
            $sujetprincipal = $_POST['sujetprincipal'];       // On récupère les informations du formulaire précédent.
            $description = $_POST['description'];       // On récupère les informations du formulaire précédent.
            $urgence = $_POST['urgence'];       // On récupère les informations du formulaire précédent.
            $date = strftime("%d/%m/%y");       // On entre la date dans la variable $date.
            $heure = strftime("%Hh%M");       // On entre l'heure dans la variable $heure.
            $ip = $_SERVER['REMOTE_ADDR'];      // On récupère l'addresse IP du client. | Note : Cette IP est stoqué sur la base de donné client uniquement.

            (int) $erreur = 0;
            
            if (!$ip) {
                $ip = "0.0.0.0";    // Si l'utilisateur utilise un proxy ; La fonction Remote addr peut dysfonctionner ; C'est une mesure de sécurité.
            }
            
            
            // Rechercher remplacer dans les chaines comportant du texte. 
            // --> Suite aux erreurs quand nous rentrons un apostrophe.
            // --> Donc nous remplaçons les apostrophes en apostrophes-antislash.
            // = ' devien '\
            
            
            $rechercher = "'"; 
            $remplacer = "\'"; 
            
            $serveurok = str_replace($rechercher,$remplacer,$serveur);
            $sujetprincipalok = str_replace($rechercher,$remplacer,$sujetprincipal);
            $descriptionok = str_replace($rechercher,$remplacer,$description);
            $utilisateuremmeteurduticket = $_SESSION['utilisateur'];
            
            
            //  Connexion à la base de donnée.
            $mysqli = new mysqli("$hotedeconnexion", "$utilisateur", "$motdepasse", "$basededonnee");
            if ($mysqli->connect_errno) {
                echo "<div class='alert alert-danger' role='alert'> Echec lors de la connexion à MySQL ! </div>";   // Affichage de l'erreur.
                echo "<div class='alert alert-danger' role='alert'> Erreur N°$mysqli->errno : $mysqli->error.</div>";    // Affichage de l'erreur.
                $erreur = $erreur + 1;
            }
           
            
            // Changement du texte en INT.
            switch ($urgence) {
                case "Faible":
                    $urgence = 0;
                    break;
                case "Normal":
                    $urgence = 1;
                    break;
                case "Urgent":
                    $urgence = 2;
                    break;
                
            }
            
            
            // Envoie des informations du formulaire dans la table.
            if (!$mysqli->query("INSERT INTO `tickets` (`serveur`, `sujetprincipal`, `description`, `date`, `heure`, `utilisateuremmeteurduticket`, `datepec`, `heurepec`,  `datefin`, `heurefin`,  `urgence`, `etat`, `ip`, `technicien`, `commentaire`, `technicienquiarchive`) VALUES ('$serveurok', '$sujetprincipalok', '$descriptionok', '$date', '$heure', '$utilisateuremmeteurduticket', 'N/A', 'N/A','N/A','N/A', '$urgence', '0', '$ip', 'N/A', 'N/A', 'N/A');")) {
                echo "<div class='alert alert-danger' role='alert'> Echec lors l'inssertion des éléments dans la table 'tickets' ! </div>";    // Affichage de l'erreur.
                echo "<div class='alert alert-danger' role='alert'> Erreur N°$mysqli->errno : $mysqli->error.</div>";    // Affichage de l'erreur.
                $erreur = $erreur + 1;
            }
            
            
            if ($erreur === 0) {    // test de la présence d'erreurs ou non.
                echo "pas d'erreurs";
                header('Location: ../index.php');
                exit();
            } else {
                echo "<h1>Il semble y avoir une erreur, veuillez vous référer à l'alerte au dessus !</h1>";
            }
            ?>
        </div>




        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    </body>
</html>
