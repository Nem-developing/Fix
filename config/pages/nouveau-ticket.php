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
        <link href="../css/nouveau-ticket.css" rel="stylesheet" type="text/css"/>
        <title>Fix - Tickets</title>
        <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    <body>

        <?php
        include '../includes/menu.php?type=niveau-enfants';
        ?>


        <!-- Formulaire de création d'un nouveau ticket-->

        <div id="page">
            <center>
                <p class="h1">Création d'un nouveau ticket</p>




                <form action="../actions/nouveau.php" method="post">
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Serveur concerné</label>
                        <input class="form-control form-control-lg bg-dark" type="text" name="srv" placeholder="Exemple : OP-Prison" required>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1 ">Sujet principal</label>
                        <input class="form-control form-control-lg bg-dark" type="text" name="sujetprincipal" placeholder="Exemple : Problème de permitions." maxlength="50" required>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Description précise du ticket :</label>
                        <textarea class="form-control bg-dark" id="exampleFormControlTextarea1" rows="12" placeholder="Exemple : Problème de permitions sur le spawn, les joueurs peuvent prendre des dégats." name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Urgence du ticket</label>
                        <select class="form-control bg-dark" id="exampleFormControlSelect1" name="urgence" required>
                            <option>Faible</option>
                            <option>Normal</option>
                            <option>Urgent</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block" value="ok" onclick="javascript=this.disabled = true; form.submit();">Créer le ticket</button>
                </form>
            </center>

        </div>



        
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
   
    </body>
</html>


