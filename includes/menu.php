<?php
$type = $_GET['type'];
if ($_GET['type'] == "enfants") {
   $link = "..";
} else {
   $link = "..";
}





switch ($_GET['type']) {
    case "niveau-enfants":
        $link = "..";
        break;
    case "niveau-parents":
        $link = ".";
        break;
}



?>

<nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
    <a class='navbar-brand' href='<?php echo $link;?>/index.php'>Fix - Gestionnaire de Tickets</a>
    <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarColor01' aria-controls='navbarColor01' aria-expanded='false' aria-label='Toggle navigation'>
        <span class='navbar-toggler-icon'></span>
    </button>

    <div class='collapse navbar-collapse' id='navbarColor01'>
        <ul class='navbar-nav mr-auto'>
            <li class='nav-item'>
                <a class='nav-link' href='https://github.com/nem-developing/'>Fix 1.0 - Nem-Developing</a>
            </li>
        </ul>
        <a href='<?php echo $link;?>/pages/parametres.php'>
        <button type='button' class='btn btn-success'>Paramètres</button>
        </a>
    </div>
</nav>  





