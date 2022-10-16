<?php
session_start();

if(!isset($_SESSION['utilisateur'])) {
  header('Location: ./connexion.php');
  exit();
}



// Données GRAPH TICKETS
include './config/config.php';  // Import des informations de connexion à la base de données.
include './includes/verif_licence_parents.php';  

// Établissement de la connexion au serveur mysql.
$conn = new mysqli($hotedeconnexion, $utilisateur, $motdepasse, $basededonnee);
$sql = "SELECT * FROM `tickets` where `etat` = '0';";
if ($result=mysqli_query($conn,$sql)) {
    $ticketsnontraitees=mysqli_num_rows($result);
}


$sql2 = "SELECT * FROM `tickets` where `etat` = '1';";
if ($result=mysqli_query($conn,$sql2)) {
    $ticketsencours=mysqli_num_rows($result);
}


$sql3 = "SELECT * FROM `tickets` where `etat` = '2';";
if ($result=mysqli_query($conn,$sql3)) {
    $ticketsarchives=mysqli_num_rows($result);
}


// Données GRAPH URGENCE DES TICKETS
$sql4 = "SELECT * FROM `tickets` where `urgence` = '0';";
if ($result=mysqli_query($conn,$sql4)) {
     $urgencefaible=mysqli_num_rows($result);
}


$sql5 = "SELECT * FROM `tickets` where `urgence` = '1';";
if ($result=mysqli_query($conn,$sql5)) {
    $urgencenormale=mysqli_num_rows($result);
}


$sql6 = "SELECT * FROM `tickets` where `urgence` = '2';";
if ($result=mysqli_query($conn,$sql6)) {
    $ticketsurgents=mysqli_num_rows($result);
}



// SI 0 TICKETS --> Tout nouvelle 
$req10 = 'SELECT * FROM `tickets` LIMIT 1;';
$res10 = $cnx->query($req10);
// Création de la table des tickets
$date_tick = strftime("%d/%m/%y");  
$heure_tick = strftime("%Hh%M"); 
$req11 = 'CREATE TABLE IF NOT EXISTS `tickets` ( `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT, `serveur` varchar(50) NOT NULL, `objet` varchar(50) NOT NULL, `description` longtext NOT NULL, `date` varchar(10) NOT NULL, `heure` varchar(10) NOT NULL, `utilisateur_emmeteur_du_ticket` varchar(25) NOT NULL, `date_pec` varchar(10) NOT NULL, `heure_pec` varchar(10) NOT NULL, `date_fin` varchar(10) NOT NULL, `heure_fin` varchar(10) NOT NULL, `urgence` int NOT NULL, `etat` int NOT NULL, `technicien` varchar(25) NOT NULL, `technicien_qui_archive` varchar(25) NOT NULL);';
$req12 = "INSERT INTO `tickets` (`serveur`, `objet`, `description`, `date`, `heure`, `utilisateur_emmeteur_du_ticket`, `date_pec`, `heure_pec`,  `date_fin`, `heure_fin`,  `urgence`, `etat`, `technicien`, `technicien_qui_archive`) VALUES ('nehemiebarkia.fr', 'Bienvenue sur Fix $versiondefix !', 'Crée un ticket pour commencer ! Tu peux également afficher les détails de ce ticket en cliquant sur le bouton tout à droite !', '$date_tick', '$heure_tick', 'Néhémie Barkia',  'N/A','N/A','N/A','N/A', '0', '0',  'N/A', 'N/A');";



if (!$res10){
    $cnx->query($req11);
    $cnx->query($req12);
    header('Location: ./index.php');
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
        <link href="css/index.css" rel="stylesheet" type="text/css"/>
        <title>Fix - Tickets</title>
        <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
        <!--
        Graphiques
        -->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
          google.charts.load('current', {'packages':['corechart']});
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {

            var datatickets = google.visualization.arrayToDataTable([
              ['Task', 'Tickets'],
              ['Tickets non-traités',     <?php echo $ticketsnontraitees;?>],
              ['Tickets en cours',      <?php echo $ticketsencours;?>],
              ['Tickets archivés',    <?php echo $ticketsarchives;?>]
            ]);

            var dataurgence = google.visualization.arrayToDataTable([
              ['Task', 'Urgence'],
              ['Urgence Élevée',     <?php echo $ticketsurgents;?>],
              ['Urgence Normale',      <?php echo $urgencenormale;?>],
              ['Urgence Faible',    <?php echo $urgencefaible;?>]
            ]);

            var optionstickets = {
              title: 'États des tickets',
              backgroundColor: '#343a40',
              colors: ['red', 'green', 'orange'],
              titleTextStyle: { color: "white", fontSize: 16},
              legend: {textStyle: {color: 'white'}}

              
            };
                    
            var optionsurgence = {
              title: 'Urgence des tickets',
              backgroundColor: '#343a40',
              colors: ['red', 'blue', 'green'],
              titleTextStyle: { color: "white", fontSize: 16},
              legend: {textStyle: {color: 'white'}}
              
              
            };

            var charttickets = new google.visualization.PieChart(document.getElementById('piechart-tickets'));
            var charturgence = new google.visualization.PieChart(document.getElementById('piechart-urgent'));

            charttickets.draw(datatickets, optionstickets);
            charturgence.draw(dataurgence, optionsurgence);
            
            function masquernotification()
            {
              $("#notif").fadeOut().empty();
            }
             window.setTimeout(masquernotification, 4000);
                      }
        </script>
    </head>
    <body>

        <?php
        include './includes/menu-parents.php';
        
        // Affichage enregistrement de la licence :
        if ($_GET['licence'] === "succes"){
             echo "<div id='notif' class='d-block p-2 bg-success text-light' href='https://github.com/Nem-developing/API/releases/latest' role='alert'>
                    Félicitations, votre clef de licence a fonctionnée ! 
                  </div>";
        }
       
        ?>

        <div class="bg-dark">
            <div style="align-content: center; border: solid; border-color: grey; height: 250px;" >
                <div id="piechart-tickets" style="width: 50%; height: 100%; float: left;"></div>
              
                <div id="piechart-urgent" style="width: 50%; height: 100%; float: left;"></div>
            </div>
            <div id="boutons">
                 <div class="boutons"><a href="pages/nouveau-ticket.php"><button type="button" class="btn btn-primary btn-lg btn-block">Nouveau ticket</button></a></div>
                <div class="boutons"><a href="pages/archives.php"><button type="button" class="btn btn-warning btn-lg btn-block">Tickets Archivés</button></a></div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th scope="col">Ticket</th>
                            <th scope="col">Création</th>
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
                        include './config/config.php';  // Import des informations de connexion à la base de données.
                        // Établissement de la connexion au serveur mysql.
                        $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
                        // Commande SQL permetant de récupérer la liste des serveurs actifs.
                        $req = 'SELECT * FROM `tickets` where `etat` = "0" OR `etat` = "1";';
                        // Envoie au serveur la commande via le biais des informations de connexion.
                        $res = $cnx->query($req);

                        // Boucle tant qu'il y a de lignes corespondantes à la requettes
                        while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {
                            $cpt += 1;


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
                            <td>$ligne->date $ligne->heure</td>
                            <td>$ligne->serveur</td>
                            <td><h4>$ligne->objet</h4><br>$ligne->description</td>
                            <td>$ligne->technicien</td>
                            <td>$urgence</td>
                            <td>$etat</td>
                            <td><a href='pages/ticket.php?id=$ligne->id'>
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
