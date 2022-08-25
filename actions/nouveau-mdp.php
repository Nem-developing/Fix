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
        
        // On récupère l'utilisateur en question
        $compte = $_GET['compte'];
        $newmdp = $_POST['motdepasse'];
        $motdepasseHASH = password_hash($newmdp, PASSWORD_DEFAULT);
        // Import des informations de connexion à la base de données.
        include '../config/config.php';  

        $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
        $req = "UPDATE `users` SET `motdepasse` = '$motdepasseHASH' WHERE `utilisateur` = '$compte';";
       
        
        if ($_SESSION['permissions'] == 2){
            $cnx->query($req);
        } elseif ($_SESSION['utilisateur'] == $compte) {
            $cnx->query($req);
        } else {
            header("Location: ../pages/gestion-utilisateurs.php?erreur=2");
            exit(); 
        }
        
        header("Location: ../pages/gestion-utilisateurs.php");
        exit();
        
        
        ?>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    </body>
</html>
