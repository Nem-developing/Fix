<?php
session_start();

// On envoie balader les utilisateurs qui n'utilisent pas correctement le logiciel.
if(!isset($_SESSION['utilisateur'])) {
  header('Location: ../connexion.php');
  exit();
} elseif (!isset($_GET['compte'])) {
  header('Location: ../index.php');
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
        include '../includes/menu-enfants.html';
        ?>
        <div id="page">
            
            <?php
            // On initialise la variable de recherche d'erreurs.
            $erreur = 0;
            // On récupère l'utilisateur à supprimer.
            $compte = $_GET['compte'];
            // On récupère l'utilisateur actuellement connecté.
            $utilisateuractif = $_SESSION['utilisateur'];
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

                 
                 if (($_SESSION['permissions'] == 2) && ($_SESSION['utilisateurid'] != $ligne->id)){
                     // Si superAdmin et pas moi --> Je peut supprimer
                    echo " superadmin et pas moi";
                    suppressionutilisateur($compte, $hotedeconnexion, $utilisateur, $motdepasse, $basededonnee);
                    sortir($erreur);
                 } else if (($_SESSION['permissions'] == 2) && ($_SESSION['utilisateurid'] == $ligne->id)){
                     // Si superadmin et moi  --> Je ne peut pas supprimer
                    echo "superadmin et moi";
                    sortir($erreur);
                 } 
                 
                 if (($_SESSION['permissions'] != 2) && ($_SESSION['utilisateurid'] == $ligne->id)) {
                     // Si pas superadmin et moi --> Je peut supprimer
                     echo "pas superadmin et moi";
                     suppressionutilisateur($compte, $hotedeconnexion, $utilisateur, $motdepasse, $basededonnee);
                     sortir($erreur);
                 } 
                 
                 if (($_SESSION['permissions'] != 2) && ($_SESSION['utilisateurid'] != $ligne->id)){
                     // Si pas superadmin et pas moi  --> Je peut supprimer
                     echo "pas superadmin et pas moi";
                     suppressionutilisateur($compte, $hotedeconnexion, $utilisateur, $motdepasse, $basededonnee);
                     sortir($erreur);
                 }
                 
                        
                 $compteur = 1;
             }
            
             if ($compteur === 0){
                echo "<div class='alert alert-danger' role='alert'> Un problème est survenue ! </div>";   // Affichage de l'erreur.
                echo "<h1>Il semble y avoir une erreur, veuillez vous référer à l'alerte au dessus !</h1>";
             }
             
             
            function suppressionutilisateur($utilisateurasupprimer, $hote, $dbutil, $dbmdp, $db) {
                //  Connexion à la base de donnée.
                $mysqli = new mysqli("$hote", "$dbutil", "$dbmdp", "$db");
                if ($mysqli->connect_errno) {
                    echo "<div class='alert alert-danger' role='alert'> Echec lors de la connexion à MySQL ! </div>";   // Affichage de l'erreur.
                    echo "<div class='alert alert-danger' role='alert'> Erreur N°$mysqli->errno : $mysqli->error.</div>";    // Affichage de l'erreur.
                    $erreur = $erreur + 1;
                }
                // Suppresion de l'utilisateur
                if (!$mysqli->query("DELETE FROM `connexion` WHERE ((`utilisateur` = '$utilisateurasupprimer'));")) {
                    echo "<div class='alert alert-danger' role='alert'> Echec lors de la création de la table serveurs ! </div>";    // Affichage de l'erreur.
                    echo "<div class='alert alert-danger' role='alert'> Erreur N°$mysqli->errno : $mysqli->error.</div>";    // Affichage de l'erreur.
                    $erreur = $erreur + 1;
                }

                sortir($erreur);
 
            }                   
             
            function sortir($e){
                if ($e === 0) {    // test de la présence d'erreurs ou non.
                      echo "pas d'erreurs";
                      header("Location: ../pages/parametres.php");
                      exit();
                  } else {
                      echo "<h1>Il semble y avoir une erreur, veuillez vous référer à l'alerte au dessus !</h1>";
                      
                  }
            }
             
        
            ?>
        </div>




        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    </body>
</html>
