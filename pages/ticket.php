<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
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

                // Changement de l'INT lié à l'état dans la base de données en texte + Atibution du texte lié au technicien.
                switch ($lignedeux->etat) {
                    case 0:
                        $etat = "<span class='bg-danger'>Non-Traité</span>";
                        $textetechnicien = "Personne ne s'occupe du ticket actuellement..."; 
                        break;
                    case 1:
                        $etat = "<span class='bg-success'>En-cours</span>";
                        $textetechnicien = "Personne s'occupant actuellement du ticket : <span class='bg-info'>$lignedeux->technicien</span>"; 
                        break;
                    case 2:
                        $etat = "<span class='bg-info'>Archivé</span>";
                        $textetechnicien = "Personne s'étant occupé du ticket : <span class='bg-info'>$lignedeux->technicien</span>"; 
                        break;
                }

                // Selon la permission de l'utilisateur actif, le bouton change !
                // Donc Toutes ces condtions établissent le bouton qui sera affiché en desous du ticket.
                // Est pris en compte également l'état du ticket pour une entière cohérence !
                // Donc, si un ticket est actif et qu'il y a un technicien qui l'a pris en charge, alors le technicien aura le bouton "Archiver le ticket" avec un formulaire lui permetant de noter des informations concernant le ticket.
                switch ($ligneune->permissions) {
                    case 0:
                        $bouton = "<button type='button' class='btn btn-danger btn-lg btn-block'>Vous n'avez pas les permissions suffisantes ! Vous ne pouvez que lire les tickets !</button>";
                        break;
                    case 1:
                        if ($lignedeux->technicien == "N/A") {
                            $bouton = "<a href='../actions/prise-en-charge.php?id=$lignedeux->id'><button type='button' class='btn btn-success btn-lg btn-block'>Prendre en charge le ticket</button></a>";
                        } else if ($lignedeux->technicien == $utilisateurconnecte && $lignedeux->etat == 1) {
                            $bouton = "<form action='../actions/archiver.php?id=$id' method='post'>
                                            <div class='form-group'>
                                                <label for='exampleFormControlInput1'>Commentaire d'archivage du ticket :</label>
                                                <textarea class='form-control bg-dark text-white' id='exampleFormControlTextarea1' rows='12' placeholder='Exemple : Problème résolu ! | Commandes bien ajoutées : /unecommande ; /unedeuxièmecommande. name='commentaire' required></textarea>
                                            </div>
                                            <button type='submit' class='btn btn-warning btn-lg btn-block' value='ok'>Archiver le ticket</button>
                                        </form>";
                        } else if ($lignedeux->technicien == $utilisateurconnecte && $lignedeux->etat == 2){
                            $bouton = "<a href='../actions/desarchiver.php?id=$lignedeux->id'><button type='button' class='btn btn-warning btn-lg btn-block'>Désarchiver le ticket</button></a>";                           
                        } else {
                            $bouton = "<button type='button' class='btn btn-danger btn-lg btn-block'>Vous n'avez pas les permissions suffisantes ! Vous ne pouvez que modifier vos propres tickets !</button>";                           
                        }
                        break;
                    case 2:
                        if ($lignedeux->technicien == "N/A") {
                            $bouton = "<a href='../actions/prise-en-charge.php?id=$lignedeux->id'><button type='button' class='btn btn-success btn-lg btn-block'>Prendre en charge le ticket</button></a>";
                        } else if($lignedeux->etat == 1){
                            $bouton = "<a href='../actions/archiver.php?id=$lignedeux->id'><button type='button' class='btn btn-warning btn-lg btn-block'>Archiver le ticket</button></a>";
                        } else if ($lignedeux->etat == 2){
                            $bouton = "<a href='../actions/desarchiver.php?id=$lignedeux->id'><button type='button' class='btn btn-warning btn-lg btn-block'>Désarchiver le ticket</button></a>";                           
                        }
                        break;
                }



                // Affichage des différents serveurs (Dans des éléments de type card.)
                echo "
                <a href='../index.php'><button type='button' class='btn btn-primary btn-lg btn-block'>Retour aux tickets actifs</button></a>
                <div class='card bg-dark text-white'>
                    <div class='card-header'>
                        Ticket N° $lignedeux->id - Ouvert le : <span class='bg-success'>$lignedeux->date à $lignedeux->heure</span>  - Pris en charge le : <span class='bg-warning'>$lignedeux->datepec à $lignedeux->heurepec</span> - Fermé le : <span class='bg-danger'>$lignedeux->datefin à $lignedeux->heurefin</span>
                    </div>
                    <div class='card-body'>
                        <h5 class='card-title'>$lignedeux->sujetprincipal</h5>
                        <p class='card- text text-primary'>$lignedeux->description</p>
                        <h6 class='card-title'>Serveur : <span class='bg-primary'>$lignedeux->serveur</span> | Niveau d'urgence : $urgence | Statut : $etat</h6>
                        <h6 class='card-title'>$textetechnicien</h6>
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
