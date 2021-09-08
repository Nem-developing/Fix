<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <link href="../css/index.css" rel="stylesheet" type="text/css"/>
        <link href="../css/confirmer-la-suppression.css" rel="stylesheet" type="text/css"/>
        <title>Fix - Tickets</title>
        <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    </head>
    <body>




    <?php
        include '../includes/menu-enfants.html';
        
        // Récupération du compte a supprimer
        $compte = $_GET['compte'];
    ?>

        
        
    <div id="page">
            
        
        <div id="formulaire">
        
            <h1>⚠ Êtes-vous sûr de vouloir supprimer l'utilisateur <a class="text-warning"><?php echo"$compte";?></a> ? </h1>   
            <div id="boutons">
                <div class="col boutons"><a href="parametres.php"><button type="button" class="btn btn-primary btn-lg btn-block">Annuler</button></a></div>
                <div class="col boutons"><a href="../actions/supprimer-utilisateur.php?compte=<?php echo $compte?>"><button type="button" class="btn btn-danger btn-lg btn-block">Supprimer l'utilisateur</button></a></div>
            </div>
        </div>

            
    </div>
       
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    </body>
</html>
