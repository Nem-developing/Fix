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
        <link href="css/index.css" rel="stylesheet" type="text/css"/>
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
                <a class="version" href="https://github.com/nem-developing/">Nix 1.0 - Nem-Developing</a>
            </div>
        </nav>  

        <div class="bg-dark">

            <div class="row mx-md-n5 bg-dark">
                <div class="col boutons"><a href="pages/nouveau-ticket.php"><button type="button" class="btn btn-primary btn-lg btn-block">Nouveau ticket</button></a></div>
                <div class="col boutons"><button type="button" class="btn btn-warning btn-lg btn-block">Tickets Archivés</button></div>
            </div>


            <?php
            include './config/config.php';  // Import des informations de connexion à la base de données.
            // Établissement de la connexion au serveur mysql.
            $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");

            // Commande SQL permetant de récupérer la liste des tickets non-traités.
            $req = 'SELECT * FROM `tickets` where `statut` = "0";';
            ?>

            <div class="table-responsive">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th scope="col">Ticket</th>
                            <th scope="col">Date</th>
                            <th scope="col">Serveur</th>
                            <th scope="col">Description</th>
                            <th scope="col">État</th>
                            <th scope="col">Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>12/1/2020</td>
                            <td>Faction</td>
                            <td><h4>Permisions</h4><br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sagittis feugiat condimentum. Nulla facilisi. Phasellus elit magna, cursus ac vehicula vitae, malesuada eget neque. Duis ac nulla eu dolor cursus pretium. Pellentesque vitae tincidunt dui. Aenean eget ultricies elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nunc dapibus ante vel lectus bibendum porttitor. Aliquam convallis congue fringilla.</td>
                            <td>Non-Traité</td>
                            <td>
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                </svg>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>13/1/2020</td>
                            <td>Minage</td>
                            <td><h4>Permisions</h4><br>Nunc diam dolor, commodo nec arcu non, facilisis luctus tortor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Mauris purus massa, consectetur et fermentum id, semper ut erat. Vivamus risus nunc, semper convallis rutrum a, imperdiet vitae ipsum. Aliquam luctus erat vitae efficitur iaculis. Proin egestas tristique libero et dignissim. Cras hendrerit lorem ut purus pretium, vel fringilla quam commodo. Praesent non blandit urna. Etiam venenatis elit vel velit varius posuere.</td>
                            <td>Non-Traité</td>
                            <td>
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                </svg>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">3</th>
                            <td>14/1/2020</td>
                            <td>Opprison</td>
                            <td><h4>Permisions</h4><br>Mauris vel nibh pharetra, tempus sapien vitae, pretium mi. Sed at aliquam augue. Sed in justo nec turpis interdum feugiat. Nunc tincidunt pretium urna varius gravida. Cras nulla quam, pretium finibus sem eu, commodo porttitor sem. Quisque blandit efficitur dolor a sodales. Pellentesque cursus metus at mi malesuada vehicula. Donec quis sollicitudin sapien. Quisque id eros sem. Praesent dignissim ac ex nec euismod. Fusce eleifend augue eget turpis porta luctus. Phasellus dictum ligula mi, quis consequat leo sagittis in.</td>
                            <td>En-cours</td>
                            <td>
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                </svg>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>




        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    </body>
</html>
