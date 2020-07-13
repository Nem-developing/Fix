<?php
session_start();
if($_SESSION['utilisateur']){
    header('Location: index.php');
    exit();
}


if(isset($_POST['submit'])){
    $utilisateurconnexion = $_POST['utilisateur'];
    $mdpconnexion = $_POST['mdp'];
    
    include './config/config.php';  // Import des informations de connexion à la base de données.
    // Établissement de la connexion au serveur mysql.
    $cnx = new PDO("mysql:host=$hotedeconnexion;dbname=$basededonnee", "$utilisateur", "$motdepasse");
    // Commande SQL permetant de récupérer la liste des tickets archivés..
    $req = 'SELECT * FROM connexion WHERE utilisateur = "' . $utilisateurconnexion . '" and motdepasse = "' . $mdpconnexion . '";';
    // Envoie au serveur la commande via le biais des informations de connexion.
    $res = $cnx->query($req);

    // Boucle tant qu'il y a de lignes corespondantes à la requettes
    while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {
        $utilisateurconnecte = $ligne->utilisateur;
        $_SESSION['utilisateur'] = $utilisateurconnecte;
    }    
    
    if ($_SESSION['utilisateur']){
        header('Location: index.php');
        exit();
    }
}
?>
<form method="post">
    <input type="text" name="utilisateur" placeholder="Nom d'utilisateur">
    <input type="password" name="mdp" placeholder="Mot de passe">
    <input type="submit" name="submit" value="Connexion">
</form>
