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
                <a class="version" href="https://github.com/nem-developing/">Nix 1.0 - Nem-Developing</a>
            </div>
        </nav>  

        <div class="bg-dark">
            <a href="../index.php"><button type="button" class="btn btn-primary btn-lg btn-block">Retour aux tickets actifs</button></a>
           
            <div class="table-responsive">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th scope="col">Ticket</th>
                            <th scope="col">Date</th>
                            <th scope="col">Serveur</th>
                            <th scope="col">Description</th>
                            <th scope="col">Urgence</th>
                            <th scope="col">État</th>
                            <th scope="col">Désarchiver</th>
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
                            switch ($ligne->etat) {
                                case 0:
                                    $etat = "<span class='bg-danger'>Non-Traité</span>";
                                    break;
                                case 1:
                                    $etat = "<span class='bg-success'>En-cours</span>";
                                    break;
                                case 2:
                                    $etat= "<span class='bg-info'>Archivé</span>";
                                    break;
                            }

                            // Affichage des différents serveurs (Dans des éléments de type card.)
                            echo "
                        <tr>
                            
                            <th scope='row'>$ligne->id</th>
                            <td>$ligne->date</td>
                            <td>$ligne->serveur</td>
                            <td><h4>$ligne->sujetprincipal</h4><br>$ligne->description</td>
                            <td>$urgence</td>
                            <td>$etat</td>
                            <td><a href='../actions/desarchiver.php?id=$ligne->id'>
                                <svg width='1em' height='1em' viewBox='0 0 16 16' class='bi bi-arrow-left-circle' fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
                                    <path fill-rule='evenodd' d='M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/>
                                    <path fill-rule='evenodd' d='M8.354 11.354a.5.5 0 0 0 0-.708L5.707 8l2.647-2.646a.5.5 0 1 0-.708-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 0 0 .708 0z'/>
                                    <path fill-rule='evenodd' d='M11.5 8a.5.5 0 0 0-.5-.5H6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 .5-.5z'/>
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
