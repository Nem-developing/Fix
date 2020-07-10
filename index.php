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
                <div class="col boutons"><a href="pages/nouveau-ticket.html"><button type="button" class="btn btn-primary btn-lg btn-block">Nouveau ticket</button></a></div>
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
                            <th scope="col">Archiver</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>12/1/2020</td>
                            <td>Faction</td>
                            <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sagittis feugiat condimentum. Nulla facilisi. Phasellus elit magna, cursus ac vehicula vitae, malesuada eget neque. Duis ac nulla eu dolor cursus pretium. Pellentesque vitae tincidunt dui. Aenean eget ultricies elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nunc dapibus ante vel lectus bibendum porttitor. Aliquam convallis congue fringilla.</td>
                            <td>
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-archive-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M12.643 15C13.979 15 15 13.845 15 12.5V5H1v7.5C1 13.845 2.021 15 3.357 15h9.286zM6 7a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1H6zM.8 1a.8.8 0 0 0-.8.8V3a.8.8 0 0 0 .8.8h14.4A.8.8 0 0 0 16 3V1.8a.8.8 0 0 0-.8-.8H.8z"/>
                                </svg>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>13/1/2020</td>
                            <td>Minage</td>
                            <td>Nunc diam dolor, commodo nec arcu non, facilisis luctus tortor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Mauris purus massa, consectetur et fermentum id, semper ut erat. Vivamus risus nunc, semper convallis rutrum a, imperdiet vitae ipsum. Aliquam luctus erat vitae efficitur iaculis. Proin egestas tristique libero et dignissim. Cras hendrerit lorem ut purus pretium, vel fringilla quam commodo. Praesent non blandit urna. Etiam venenatis elit vel velit varius posuere.</td>
                            <td>
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-archive-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M12.643 15C13.979 15 15 13.845 15 12.5V5H1v7.5C1 13.845 2.021 15 3.357 15h9.286zM6 7a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1H6zM.8 1a.8.8 0 0 0-.8.8V3a.8.8 0 0 0 .8.8h14.4A.8.8 0 0 0 16 3V1.8a.8.8 0 0 0-.8-.8H.8z"/>
                                </svg>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">3</th>
                            <td>14/1/2020</td>
                            <td>Opprison</td>
                            <td>Mauris vel nibh pharetra, tempus sapien vitae, pretium mi. Sed at aliquam augue. Sed in justo nec turpis interdum feugiat. Nunc tincidunt pretium urna varius gravida. Cras nulla quam, pretium finibus sem eu, commodo porttitor sem. Quisque blandit efficitur dolor a sodales. Pellentesque cursus metus at mi malesuada vehicula. Donec quis sollicitudin sapien. Quisque id eros sem. Praesent dignissim ac ex nec euismod. Fusce eleifend augue eget turpis porta luctus. Phasellus dictum ligula mi, quis consequat leo sagittis in.</td>
                            <td>
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-archive-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M12.643 15C13.979 15 15 13.845 15 12.5V5H1v7.5C1 13.845 2.021 15 3.357 15h9.286zM6 7a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1H6zM.8 1a.8.8 0 0 0-.8.8V3a.8.8 0 0 0 .8.8h14.4A.8.8 0 0 0 16 3V1.8a.8.8 0 0 0-.8-.8H.8z"/>
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
