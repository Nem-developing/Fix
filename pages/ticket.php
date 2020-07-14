<?php
session_start();

if(!isset($_SESSION['utilisateur'])) {
  header('Location: ../connexion.php');
  exit();
  
if (!$id) {
    echo 'Erreur, vous de devez pas être là !';
    header('Location: ../index.php');   // redireciton vers la page d'acceuil.
    exit();
}
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
        <link href="../css/ticket.css" rel="stylesheet" type="text/css"/>
        <title>Fix - Tickets</title>
    </head>
    <body>

        <!-- Menu -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php">Fix</a>
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





        <?php
        include '../config/config.php';  // Import des informations de connexion à la base de données.
        // Établissement de la connexion au serveur mysql.
        $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
        // Commande SQL permetant de récupérer la liste des serveurs actifs.

        $id = $_GET['id'];
        $utilisateurconnecte = $_SESSION['utilisateur'];

        
        // Commande récupérant l'utilisateur connecté.
        $requn = 'SELECT * FROM connexion WHERE utilisateur = "' . $utilisateurconnecte . '";';
        // Envoie au serveur la commande via le biais des informations de connexion.
        $resun = $cnx->query($requn);
        
        
        $reqdeux = 'SELECT * FROM tickets where id = "' . $id . '"';
        // Envoie au serveur la commande via le biais des informations de connexion.
        $resdeux = $cnx->query($reqdeux);
        
        
        // Boucle tant qu'il y a de lignes corespondantes à la requette une.
        while ($ligneune = $resun->fetch(PDO::FETCH_OBJ)) {
        // Boucle tant qu'il y a de lignes corespondantes à la requette deux.
        while ($lignedeux = $resdeux->fetch(PDO::FETCH_OBJ)) {



            // Changement de l'INT en texte.
            switch ($lignedeux->urgence) {
                case 0:
                    $urgence = "<span class='bg-success'>Faible</span>";
                    break;
                case 1:
                    $urgence = "<span class='bg-warning'>Normal</span>";
                    break;
                case 2:
                    $urgence = "<span class='bg-danger'>Urgent</span>";
                    break;
            }

            // Changement de l'INT en texte.
            switch ($lignedeux->etat) {
                case 0:
                    $etat = "<span class='bg-danger'>Non-Traité</span>";
                    break;
                case 1:
                    $etat = "<span class='bg-success'>En-cours</span>";
                    break;
                case 2:
                    $etat = "<span class='bg-info'>Archivé</span>";
                    break;
            }
            
            
            if ($lignedeux->technicien =="N/A"){
                $bouton = "<a href='prendre-en-charge.php?id=$lignedeux->id'><button type='button' class='btn btn-success btn-lg btn-block'>Prendre en charge le ticket</button></a>";
            } else {
                $bouton = "<a href='../actions/archiver.php?id=$lignedeux->id'><button type='button' class='btn btn-warning btn-lg btn-block'>Archiver le ticket</button></a>";
            }
            
            
            
            // Affichage des différents serveurs (Dans des éléments de type card.)
            echo "
                <div class='card bg-dark text-white'>
                    <div class='card-header'>
                        Ticket N° $lignedeux->id - $lignedeux->date
                    </div>
                    <div class='card-body'>
                        <h5 class='card-title'>$lignedeux->sujetprincipal</h5>
                        <p class='card- text'>$lignedeux->description</p>
                        <h6 class='card-title'>Serveur : $lignedeux->serveur | Niveau d'urgence : $urgence | Statut : $etat</h6>
                        <h6 class='card-title'>Personne s'occupant du ticket : $lignedeux->technicien</h6>
                    </div>
                    $bouton
                </div>
                ";
        }
        }
        ?>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    </body>
</html>
