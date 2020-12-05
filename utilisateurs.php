<?php

session_start();

include 'connex.inc.php';

try {

if (isset($_GET['supprimer']) AND !empty($_GET['supprimer'])) {
    $supprimer = (int) $_GET['supprimer'];
    $req = $pdo->prepare('DELETE FROM user WHERE id = ?');
    $req->execute(array($supprimer));
    header('LOCATION:utilisateurs.php?id='.$_SESSION['id']);
}

if (isset($_GET['definir']) AND !empty($_GET['definir'])) {
    $definir = (int) $_GET['definir'];
    $req2 = $pdo->prepare('UPDATE user SET admin = 1 WHERE id = ?');
    $req2->execute(array($definir));
    header('LOCATION:utilisateurs.php?id='.$_SESSION['id']);
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <title>Projet Web</title>

    <link rel="icon shortcut" href="../images/favicon.png">

    <link href="../css/style.css" rel="stylesheet" type="text/css">

    <link href="../css/utilisateurs.css" rel="stylesheet" type="text/css">

</head>

<body>

<header>

    <div id="logo">

        <div class="inner">

            <a href="index.php">

                <img src="../images/favicon.png" alt="logo">

            </a>

        </div>

        <div id="profil">
        <?php 
        if (isset($_GET['id']) AND $_GET['id'] > 0) {
            $getid = intval($_GET['id']);
            $requser = $pdo->prepare('SELECT * FROM user WHERE id = ?');
            $requser->execute(array($getid));
            $userinfo = $requser->fetch();

            if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                if ($userinfo['admin'] == 0) {
        ?>
            <h2> Bienvenue <?php echo $userinfo['prenom'].' '.$userinfo['nom']; ?></h2>
            <a href="deconnexion.php"> Se déconnecter </a>
        <?php
                }
                else {
        ?>
            <h2> Espace administrateur - <?php echo ' '.$userinfo['prenom'].' '.$userinfo['nom']; ?></h2>
            <a href="deconnexion.php"> Se déconnecter </a>
        <?php
                }
            }
        }
        ?>
        </div>

    </div>

    <div id="mainmenu-container">

        <ul id="mainmenu">

            <?php 
            if (isset($_GET['id']) AND $_GET['id'] > 0) {
            ?>

                <?php echo '<li><a href="index.php?id='.$_GET['id'].'">Accueil</a></li>'; ?>

                <li><a href="<?php echo 'hebergement.php?id='.$_GET['id'].''; ?>">Hebergement</a></li>

                <li><a href="<?php echo 'contact.php?id='.$_GET['id'].''; ?>">Contact</a></li>

                <li><a href="<?php echo 'transport.php?id='.$_GET['id'].''; ?>">Transport</a></li>
            <?php
            }
            ?>

            <?php 
            if (isset($_GET['id']) AND $_GET['id'] > 0) {
                if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                    if ($userinfo['admin'] == 0) {
            ?>
                    <li><a href="#">Mon profil</a>

                        <ul>

                        <?php 

                        echo '<li><a href="modifier.php?id='.$_GET['id'].'">Modifier mes informations</a>';

                        $reqreservetion = $pdo->prepare('SELECT * FROM reservation WHERE id_client = ?');
                        $reqreservetion->execute(array($_SESSION['id']));
                        $reservation = $reqreservetion->fetch();
                        $nbreservation2 = $reqreservetion->rowCount();

                        $reqreservetion1 = $pdo->prepare('SELECT * FROM transfert_aeroport WHERE id_client = ?');
                        $reqreservetion1->execute(array($_SESSION['id']));
                        $reservation1 = $reqreservetion1->fetch();
                        $nbreservation1 = $reqreservetion1->rowCount();

                        $nbreservation = $nbreservation1 + $nbreservation2;

                        if ($nbreservation > 0) {
                            echo '<li><a href="reservations.php?id='.$_GET['id'].'">Mes réservations - ('.$nbreservation.')</a>';
                        }

                        ?>

                        <?php echo '<li><a href="deconnexion.php">Deconnexion</a>'; ?>

                        </ul>

                    </li>

                <?php
                    }
                    else {
                ?>
                    <li><a href="#">Utilisateurs</a>

                        <ul>

                        <?php echo '<li><a href="modifier.php?id='.$_GET['id'].'">Mon profil</a>'; ?>

                        <?php echo '<li><a href="utilisateurs.php?id='.$_GET['id'].'">Utilisateurs</a>'; 

                        $reqreservetion = $pdo->prepare('SELECT * FROM reservation');
                        $reqreservetion->execute(array($_SESSION['id']));
                        $reservation = $reqreservetion->fetch();
                        $nbreservation2 = $reqreservetion->rowCount();

                        $reqreservetion1 = $pdo->prepare('SELECT * FROM transfert_aeroport');
                        $reqreservetion1->execute(array($_SESSION['id']));
                        $reservation1 = $reqreservetion1->fetch();
                        $nbreservation1 = $reqreservetion1->rowCount();

                        $nbreservation = $nbreservation1 + $nbreservation2;

                        if ($nbreservation > 0) {
                            echo '<li><a href="reservations.php?id='.$_GET['id'].'">Voir les réservations - ('.$nbreservation.')</a>';
                        }

                        ?>

                        <?php echo '<li><a href="deconnexion.php">Deconnexion</a>'; ?>

                        </ul>

                    </li>
            <?php
                    }
                }
            }
            else {
                echo '<li><a href="inscription.php">Inscription - Connexion</a></li>';
            }
            ?>

        </ul>

    </div>

    <div id="menu-btn"></div>

</header>

<div id="pere">

    <div id="utilisateurs">
        <br>
        <?php 
            if (isset($_GET['id']) AND $_GET['id'] > 0) {
                if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                    if ($userinfo['admin'] == 1) {
                        $client = $pdo->query("SELECT * FROM user WHERE admin = 0");
                        $nbclient = $client->rowCount();
        ?>
        <h2> Client(s) - <?php echo '('.$nbclient.')'; ?></h2>
        <table>
        <?php

                        while ($utilisateur = $client->fetch()) {
                            echo '<tr>';
                                echo '<td>';
                                    echo $utilisateur['prenom'].' '.$utilisateur['nom'].'<br>';
                                echo '</td>';

                                echo '<td>';

                                    echo '<span></span>';
                                    echo '<a href="utilisateurs.php?supprimer='.$utilisateur['id'].'"> <input type="button" onClick="return confirm(\'Vous êtes sur le point de supprimer cet utilisateur\');" class="more" value="Supprimer"></input></a>';

                                    echo '<a href="utilisateurs.php?definir='.$utilisateur['id'].'"> <input type="button" class="more" onClick="return confirm(\'Vous êtes sur le point de définir cet utilisateur comme administrateur\');" value="Définir comme administrateur"></input></a>';
                                echo '</td>';
        ?>
                            </tr>
        <?php
                        }
                        echo '</table>';
                        $client->closeCursor();
                    }
                }
            }
        ?>
        <br>
    </div>

    <div id="admin">
        <br>
        <?php 
            if (isset($_GET['id']) AND $_GET['id'] > 0) {
                if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                    if ($userinfo['admin'] == 1) {
                        $admin = $pdo->query("SELECT * FROM user WHERE admin = 1");
                        $nbadmin = $admin->rowCount();
        ?>
        <h2> Administrateur(s) - <?php echo '('.$nbadmin.')'; ?></h2>
        <table>
        <?php
                        while ($utilisateur = $admin->fetch()) {
                            echo '<tr>';
                                echo '<td>';
                                    echo $utilisateur['prenom'].' '.$utilisateur['nom'].'<br>';
                                echo '</td>';

                                if ($utilisateur['id'] == $_SESSION['id']) {
                                    echo '<td>';

                                        echo '<span></span>';
                                        echo '<a href="modifier.php?id='.$utilisateur['id'].'"> <input type="button" class="more" value="Modifier mes informations"></input></a>';
                                    echo '</td>';
                                }
        ?>
                            </tr>
        <?php
                        }
                        echo '</table>';
                        $admin->closeCursor();
                    }
                }
            }
        ?>
        <br>
    </div>
    
</div>

<div class="clearfix"></div>

<footer>

    <div class="container">

        <div class="row">

            <div class="col-md-4">

                <h3>Termes d'utilisation</h3>

                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.

            </div>

            <div class="col-md-4 col-md-offset-4">

                <h3>Nous Contacter</h3>

                <address>


                    <span><strong>Tel:</strong>00 66 666 666 666</span>

                    <span><strong>Email:</strong><a href="mailto:ouchtitiwalid@gmail.com">ouchtitiwalid@gmail.com</a></span>

                </address>



                <div class="social-icons">

                    <a href="#">

                        <img src="../images/social-icons/facebook.png" alt=""></a>

                    <a href="#">

                        <img src="../images/social-icons/twitter.png" alt=""></a>

                    <a href="#">

                        <img src="../images/social-icons/dribbble.png" alt=""></a>

                    <a href="#">

                        <img src="../images/social-icons/blogger.png" alt=""></a>

                    <a href="#">

                        <img src="../images/social-icons/youtube.png" alt=""></a>

                    <a href="#">

                        <img src="../images/social-icons/vimeo.png" alt=""></a>

                </div>

            </div>

        </div>

    </div>



    <div class="subfooter">

        <div class="container">

            <div class="row">

                <div class="col-md-6">

                    &copy; Siteweb développé par <span class="text-muted text-danger">Walid OUCHTITI et Mohammed LYAHYAOUI</span>

                </div>

            </div>

        </div>

    </div>



</footer>

<input type="hidden" min="0" max="1" value="0" id="inout">
</body>

<?php
}
catch (PDOException $e) {
    echo '<p> Erreur de connexion </p>';
    die();
}
?>

</html>
