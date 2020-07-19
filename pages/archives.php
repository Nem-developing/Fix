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
        <link href="../css/index.css" rel="stylesheet" type="text/css"/>
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

        <div class="bg-dark">
            <a href="../index.php"><button type="button" class="btn btn-primary btn-lg btn-block">Retour aux tickets actifs</button></a>
           
            <div class="table-responsive">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th scope="col">Ticket</th>
                            <th scope="col">Création</th>
                            <th scope="col">Prise en Charge</th>
                            <th scope="col">Fermeture</th>
                            <th scope="col">Serveur</th>
                            <th scope="col">Description</th>
                            <th scope="col">Technicien</th>
                            <th scope="col">Urgence</th>
                            <th scope="col">État</th>
                            <th scope="col">Détails</th>
                        </tr>
                    </thead>
                    <tbody>


                        <?php
                        include '../config/config.php';  // Import des informations de connexion à la base de données.
                        // Établissement de la connexion au serveur mysql.
                        $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
                        // Commande SQL permetant de récupérer la liste des tickets archivés..
                        $req = 'SELECT * FROM `tickets` where `etat` = "2";';
                        // Envoie au serveur la commande via le biais des informations de connexion.
                        $res = $cnx->query($req);

                        // Boucle tant qu'il y a de lignes corespondantes à la requettes
                        while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {



                            // Changement de l'INT en texte.
                            switch ($ligne->urgence) {
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

                            // Changement de l'INT en texte.
                            switch ($ligne->etat) {
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


                            // Affichage des différents serveurs (Dans des éléments de type card.)
                            echo "
                        <tr>
                            
                            <th scope='row'>$ligne->id</th>
                            <td>$ligne->date</td>
                            <td>$ligne->date $ligne->heure</td>
                            <td>$ligne->datefin $ligne->heurefin</td>
                            <td>$ligne->serveur</td>
                            <td><h4>$ligne->sujetprincipal</h4><br>$ligne->description</td>
                            <td>$ligne->technicien</td>
                            <td>$urgence</td>
                            <td>$etat</td>
                            <td><a href='ticket.php?id=$ligne->id'>
                                <svg width='1em' height='1em' viewBox='0 0 16 16' class='bi bi-pencil-square' fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
                                <path d='M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z'/>
                                <path fill-rule='evenodd' d='M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z'/>
                                </svg>
                                </a>
                            </td>
                        </tr>
                        ";
                        }
                        ?>
                    


                    </tbody>
                </table>
            </div>

        </div>




        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    </body>
</html>
