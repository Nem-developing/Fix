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

        <!-- Menu -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="../index.php">Fix</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarColor01">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="../index.php">Accueil <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://github.com/Nem-developing/fix/">Source code</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">À propos</a>
                    </li>
                </ul>
                <a class="version" href="https://github.com/nem-developing/">Fix 1.0 - Nem-Developing</a>
            </div>
        </nav>  


 
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
            
            
            
            //  Connexion à la base de donnée.
            $mysqli = new mysqli("$hotedeconnexion", "$utilisateur", "$motdepasse", "$basededonnee");
            if ($mysqli->connect_errno) {
                echo "<div class='alert alert-danger' role='alert'> Echec lors de la connexion à MySQL ! </div>";   // Affichage de l'erreur.
                echo "<div class='alert alert-danger' role='alert'> Erreur N°$mysqli->errno : $mysqli->error.</div>";    // Affichage de l'erreur.
                $erreur = $erreur + 1;
            }
            // Création de la table où l'on stoque les informations du ticket.
            if (!$mysqli->query("CREATE TABLE IF NOT EXISTS `tickets` ( `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT, `serveur` varchar(50) NOT NULL, `sujetprincipal` varchar(50) NOT NULL, `description` longtext NOT NULL, `date` varchar(10) NOT NULL, `heure` varchar(10) NOT NULL, `datepec` varchar(10) NOT NULL, `heurepec` varchar(10) NOT NULL, `datefin` varchar(10) NOT NULL, `heurefin` varchar(10) NOT NULL, `urgence` int NOT NULL, `etat` int NOT NULL, `ip` varchar(19) NOT NULL , `technicien` varchar(25) NOT NULL, `commentaire` longtext NOT NULL );")) {
                echo "<div class='alert alert-danger' role='alert'> Echec lors de la création de la table serveurs ! </div>";    // Affichage de l'erreur.
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
            if (!$mysqli->query("INSERT INTO `tickets` (`serveur`, `sujetprincipal`, `description`, `date`, `heure`, `datepec`, `heurepec`,  `datefin`, `heurefin`,  `urgence`, `etat`, `ip`, `technicien`, `commentaire`) VALUES ('$serveurok', '$sujetprincipalok', '$descriptionok', '$date', '$heure', 'N/A', 'N/A','N/A','N/A', '$urgence', '0', '$ip', 'N/A', 'N/A');")) {
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
