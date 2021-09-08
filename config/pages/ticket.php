<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../connexion.php');
    exit();
    
    $id = $_GET['id'];
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
        <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    </head>
    <body>

        <?php
        include '../includes/menu.php?type=niveau-enfants';
        ?>




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
                        $urgence = "<strong><span class='text-success'>Faible</span></strong>";
                        break;
                    case 1:
                        $urgence = "<strong><span class='text-primary'>Normal</span></strong>";
                        break;
                    case 2:
                        $urgence = "<strong><span class='text-danger'>Urgent</span></strong>";
                        break;
                }

                // Changement de l'INT lié à l'état dans la base de données en texte + Atibution du texte lié au technicien.
                switch ($lignedeux->etat) {
                    case 0:
                        $etat = "<strong><span class='text-danger'>Non-Traité</span></strong>";
                        break;
                    case 1:
                        $etat = "<strong><span class='text-success'>En-cours</span></strong>";
                        break;
                    case 2:
                        $etat= "<strong><span class='text-info'>Archivé</span></strong>";
                        break;
                }

                // Selon la permission de l'utilisateur actif, le bouton change !
                // Donc Toutes ces condtions établissent le bouton qui sera affiché en desous du ticket.
                // Est pris en compte également l'état du ticket pour une entière cohérence !
                // Donc, si un ticket est actif et qu'il y a un technicien qui l'a pris en charge, alors le technicien aura le bouton "Archiver le ticket" avec un formulaire lui permetant de noter des informations concernant le ticket.
                switch ($ligneune->permissions) {
                    case 0:
                        $textedynamique = "<button type='button' class='btn btn-danger btn-lg btn-block'>Vous n'avez pas les permissions suffisantes ! Vous ne pouvez que lire les tickets !</button>";
                        break;
                    case 1:
                        if ($lignedeux->technicien == "N/A") {
                            $textedynamique = "<a href='../actions/prise-en-charge.php?id=$lignedeux->id'><button type='button' class='btn btn-success btn-lg btn-block'>Prendre en charge le ticket</button></a>";
                        } else if ($lignedeux->technicien == $utilisateurconnecte && $lignedeux->etat == 1) {
                            $textedynamique = "<form action='../actions/archiver.php?id=$id' method='post'>
                                                <div class='form-group'>
                                                    <label for='exampleFormControlInput1'>Commentaire d'archivage du ticket :</label>
                                                    <textarea class='form-control bg-dark text-white' name='commentaire' id='exampleFormControlTextarea1' rows='12' placeholder='Exemple : Problème résolu ! | Commandes bien ajoutées : /unecommande ; /unedeuxièmecommande.' required></textarea>
                                                </div>
                                                <button type='submit' class='btn btn-warning btn-lg btn-block' value='ok'>Archiver le ticket</button>
                                            </form>";
                        } else if ($lignedeux->technicien == $utilisateurconnecte && $lignedeux->etat == 2) {
                            $textedynamique = "
                                  <div class='card-body'>
                                      <h6 class='card-title'>Ticket pris en charge par le technicien <span class='text-warning'><strong>$lignedeux->technicien</strong></span> - Ticket archivé par le technicien : <span class='text-success'><strong>$lignedeux->technicienquiarchive</strong></span></h6>                                                                
                                      <br>
                                      <h6 class='card-title'>Commentaire d'archivage :</h6>                                    
                                      <p class='card-text text-success'><strong>$lignedeux->commentaire</strong></p>
                                  </div>
                                <a href='../actions/desarchiver.php?id=$lignedeux->id'><button type='button' class='btn btn-warning btn-lg btn-block'>Désarchiver le ticket</button></a>";
                        } else {
                            $textedynamique = "<button type='button' class='btn btn-danger btn-lg btn-block'>Vous n'avez pas les permissions suffisantes ! Vous ne pouvez que modifier vos propres tickets !</button>";
                        }
                        break;
                    case 2:
                        if ($lignedeux->technicien == "N/A") {
                            $textedynamique = "<a href='../actions/prise-en-charge.php?id=$lignedeux->id'><button type='button' class='btn btn-success btn-lg btn-block'>Prendre en charge le ticket</button></a>";
                        } else if ($lignedeux->etat == 1) {
                            $textedynamique = "<form action='../actions/archiver.php?id=$id' method='post'>
                                                <div class='form-group'>
                                                    <label for='exampleFormControlInput1'>Commentaire d'archivage du ticket :</label>
                                                    <textarea class='form-control bg-dark text-white' name='commentaire' id='exampleFormControlTextarea1' rows='12' placeholder='Exemple : Problème résolu ! | Commandes bien ajoutées : /unecommande ; /unedeuxièmecommande.' required></textarea>
                                                </div>
                                                <button type='submit' class='btn btn-warning btn-lg btn-block' value='ok'>Archiver le ticket</button>
                                            </form>";
                        } else if ($lignedeux->etat == 2) {
                            $textedynamique = "
                                  <div class='card-body'>
                                      <h6 class='card-title'>Ticket pris en charge par le technicien <span class='text-warning'><strong>$lignedeux->technicien</strong></span> - Ticket archivé par le technicien : <span class='text-success'><strong>$lignedeux->technicienquiarchive</strong></span></h6>                                                                
                                      <br>
                                      <h6 class='card-title'>Commentaire d'archivage :</h6>                                    
                                      <p class='card-text text-success'><strong>$lignedeux->commentaire</strong></p>
                                  </div>
                                <a href='../actions/desarchiver.php?id=$lignedeux->id'><button type='button' class='btn btn-warning btn-lg btn-block'>Désarchiver le ticket</button></a>";
                        }
                        break;
                }



                // Affichage des différents serveurs (Dans des éléments de type card.)
                echo "
                    <a href='../index.php'><button type='button' class='btn btn-primary btn-lg btn-block'>Retour aux tickets actifs</button></a>
                    <div class='card bg-dark text-white'>
                        <div class='card-header'>
                            Ticket N° $lignedeux->id - Ouvert <strong><span class='text-info'>par $lignedeux->utilisateuremmeteurduticket </span></strong> le : <strong><span class='text-success'>$lignedeux->date à $lignedeux->heure</span></strong>  - Pris en charge le : <strong><span class='text-warning'>$lignedeux->datepec à $lignedeux->heurepec</span></strong> - Fermé le : <strong><span class='text-danger'>$lignedeux->datefin à $lignedeux->heurefin</span></strong>
                        </div>
                        <div class='card-header'>
                            Serveur : <strong><span class='text-primary'>$lignedeux->serveur</span></strong> | Niveau d'urgence : $urgence | Statut : $etat
                        </div>
                        <div class='card-body'>
                            <h5 class='card-title'>Sujet : $lignedeux->sujetprincipal</h5>
                            <p class='card- text'>Description : <span class='text-primary'>$lignedeux->description</span></p>
                        </div>
                        $textedynamique
                        <br>
                        
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
