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
            // Connexion à la base de donnée.
             $mysqli = new mysqli("$hotedeconnexion", "$utilisateur", "$motdepasse", "$basededonnee");
            if ($mysqli->connect_errno) {
                echo "<div class='alert alert-danger' role='alert'> Echec lors de la connexion à MySQL ! </div>";   // Affichage de l'erreur.
                echo "<div class='alert alert-danger' role='alert'> Erreur N°$mysqli->errno : $mysqli->error.</div>";    // Affichage de l'erreur.
                $erreur = $erreur + 1;
            }
            
            
            // On initialise la variable de recherche d'erreurs.
            $erreur = 0;
            
            // On récupère les valleurs du formulaire.
            $compte = $_POST['utilisateur']; 
            $motdepassecompte = $_POST['motdepasse']; 
            $choix = $_POST['choix']; 
            
            
            // On vérifie que l'utilisateur n'existe pas déjà.
            
            // Import des informations de connexion à la base de données.
            include '../config/config.php';  
            // Établissement de la connexion au serveur mysql.
            $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
            // Requette SQL.
            $req = "SELECT * FROM `connexion` where utilisateur = '$compte';";
            // Envoie au serveur la commande via le biais des informations de connexion.
            $res = $cnx->query($req);
            
            $compteur = 0;
            while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {
                $compteur = 1;
            }
            if ($compteur === 1) {
                header("Location: ../pages/gestion-utilisateurs.php?erreur=1");
                exit();
            }
            
            
            // Rechercher remplacer dans les chaines comportant du texte. 
            
            $rechercher = "'"; 
            $remplacer = "\'"; 
            
            $utilisateurok = str_replace($rechercher,$remplacer,$compte);
            
            
            // On initialise une valleur.
            $privileges = 99;
            
            switch ($choix) {
                case "Faibles":
                    $privileges = 0;

                    break;
                
                case "Normaux":
                    $privileges = 1;

                    break;
                
                case "Élevés":
                    $privileges = 2;

                    break;
            }
            
                       
            $motdepasseHASH = password_hash($motdepassecompte, PASSWORD_DEFAULT);
          
            
            
            
            
            // On entre la date dans une variable.
            $date = strftime("%d/%m/%y"); 

            if (!$mysqli->query("INSERT INTO `connexion` (`utilisateur`, `motdepasse`, `permissions`, `creation`) VALUES ('$utilisateurok', '$motdepasseHASH', '$privileges', '$date');")) {
                echo "<div class='alert alert-danger' role='alert'> Echec lors de l'insertion des données ! </div>";    // Affichage de l'erreur.
                echo "<div class='alert alert-danger' role='alert'> Erreur N°$mysqli->errno : $mysqli->error.</div>";    // Affichage de l'erreur.
                $erreur = $erreur + 1;
            }
            echo "INSERT INTO `connexion` (`utilisateur`, `motdepasse`, `permissions`, `creation`) VALUES ('$utilisateurok', '$motdepasseHASH', '$privileges', '$date');";
            if ($erreur === 0) {    // test de la présence d'erreurs ou non.
                header("Location: ../pages/gestion-utilisateurs.php");
                exit();
            } else {
                echo "<h1>Il semble y avoir une erreur, veuillez vous référer à l'alerte au dessus !</h1>";
            }
            ?>
            
            ?>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    </body>
</html>
