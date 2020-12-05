<?php

session_start();

include 'connex.inc.php';

try {

    if (isset($_GET['id']) AND $_GET['id'] > 0) {
        $getid = intval($_GET['id']);
        $requser = $pdo->prepare('SELECT * FROM user WHERE id = ?');
        $requser->execute(array($getid));
        $userinfo = $requser->fetch();
        if (!isset($_SESSION['id'])) 
            header('LOCATION:hebergement.php');
        else if ($userinfo['id'] != $_SESSION['id'])
            header('LOCATION:hebergement.php?id='.$_SESSION['id']);
    }
    else if (isset($_SESSION['id'])) {
        header('LOCATION:hebergement.php?id='.$_SESSION['id']);
    }

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <title>Projet Web</title>

    <link rel="icon shortcut" href="../images/favicon.png">

    <link href="../css/style.css" rel="stylesheet" type="text/css">

    <link href="../css/hebergement.css" rel="stylesheet" type="text/css">

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
                if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
            ?>

                <li><a href="<?php echo 'index.php?id='.$_GET['id'].''; ?>">Accueil</a></li>

                <li><a href="#">Hebergement</a></li>

                <li><a href="<?php echo 'contact.php?id='.$_GET['id'].''; ?>">Contact</a></li>

                <li><a href="<?php echo 'transport.php?id='.$_GET['id'].''; ?>">Transport</a></li>
            <?php
                }
            }

            else {
            ?>

                <li><a href="index.php">Accueil</a></li>

                <li><a href="#">Hebergement</a></li>

                <li><a href="contact.php">Contact</a></li>

                <li><a href="transport.php">Transport</a></li>

            <?php
            }

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

<div class="clearfix"></div>    

<h1 class="text-muted text-center">La liste des chambres</h1>
<?php
    if (isset($_GET['id']) AND $_GET['id'] > 0) {
        if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
            if ($userinfo['admin'] == 1) {
?>
<a href="ajouter_chambre.php?id=<?php echo $_GET['id'] ?>"><button id="button1" name="bouton1"> <b>Ajouter un type de chambre</b> </button></a>
<?php
            }
        }
    }
?>

<div class="container">

<?php
    $affiche = $pdo->query("SELECT * FROM chambre WHERE disponnibilite > 0");
    while ($chambre = $affiche->fetch()) {
        if (isset($_GET['id']) AND $_GET['id'] > 0) {
            if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                if ($userinfo['admin'] == 0) {
                    echo '<form action="formulaire_chambre.php?id='.$_SESSION['id'].'" method="POST">';
                }
                else if ($userinfo['admin'] == 1) {
                    echo '<form action="modifier_chambre.php?id='.$_SESSION['id'].'" method="POST">';
                }
            }
        }
        else {
            echo '<form action="formulaire_chambre.php" method="POST">';
        }
?>

    <div class="row">

        <div class="riads">

            <div class="riad">

                <div class="col-sm-9">

                    <?php
                        if (isset($_GET['id']) AND $userinfo['id'] == $_SESSION['id']) {
                            echo '<input type="hidden" name="id_chambre" value="'.$chambre['id'].'">';
                            if ($userinfo['admin'] == 1) {
                                echo '<input name="type" type="text" value="'.$chambre['type'].'">';
                            }
                            else {
                    ?>

                    <h4 class="title"><!-- <a href="transport_more.php"> --><?php echo $chambre['type'] ?><!-- </a> --></h4>

                    <?php
                            }
                        }
                        else {
                    ?>

                    <h4 class="title"><!-- <a href="transport_more.php"> --><?php echo $chambre['type'] ?><!-- </a> --></h4>

                    <?php
                        }
                    ?>

                    <div class="row">

                        <div class="col-sm-4">

                            <img src="../images/chambres/<?php echo $chambre['image'] ?>" class="photo img img-responsive img-thumbnail">
                            <?php
                                if (isset($_GET['id']) AND $userinfo['id'] == $_SESSION['id']) {
                                    if ($userinfo['admin'] == 1) {
                                        echo '<input type="file" name="image">';
                                    }
                                }
                            ?>

                        </div>

                        <div class="col-sm-8">

                        <?php
                            if (isset($_GET['id']) AND $userinfo['id'] == $_SESSION['id']) {
                                if ($userinfo['admin'] == 1) {
                                    echo '<textarea name="marque"> '.$chambre['description'].' </textarea>';


                                    echo '<div>
                                        Chambres disponnibles :
                                        <input type="number" name="disponnibilite" value="'.$chambre['disponnibilite'].'" min="0"  class="input">
                                    </div>';
                                }
                                else {
                        ?>

                                <p class="description">

                                    <?php echo $chambre['description'] ?>
                                </p>

                        <?php
                                }
                            }
                            else {
                        ?>

                            <p class="description">

                                <?php echo $chambre['description'] ?>
                            </p>

                        <?php
                            }
                        ?>

                        </div>

                    </div>

                </div>


                <div class="col-sm-3">

                    <?php
                        if (isset($_GET['id']) AND $userinfo['id'] == $_SESSION['id']) {
                            if ($userinfo['admin'] == 1) {
                                echo '<div id="prix">';
                                echo 'Tarif'.'<br>';
                                echo '<input id="prixinput" name="prix" type="text" value="'.$chambre['prix'].'"> €';
                                echo '</div>';
                            }
                            else {
                    ?>

                        <span class="price"><span class="text2">à partir de</span><strong class="text0 conversion"><?php echo $chambre['prix'] ?> €</strong></span>

                    <?php
                            }
                        }
                        else {
                    ?>

                    <span class="price"><span class="text2">à partir de</span><strong class="text0 conversion"><?php echo $chambre['prix'] ?> €</strong></span>

                    <?php
                        }
                    ?>

                    <div class="icons">

                        <?php
                            if (isset($_GET['id']) AND $userinfo['id'] == $_SESSION['id']) {
                                if ($userinfo['admin'] == 1) {
                                    echo '<span class="location"><i class="fa fa-map-marker"></i> <input id="localisation" name="localisation" type="text" value="'.$chambre['localisation'].'"> </span>';
                                }
                                else {
                        ?>  

                            <span class="location"><i class="fa fa-map-marker"></i> <?php echo $chambre['localisation'] ?></span>

                        <?php
                                }
                            }
                            else {
                        ?>  

                        <span class="location"><i class="fa fa-map-marker"></i> <?php echo $chambre['localisation'] ?></span>

                        <?php
                            }
                        ?>

                        <!-- <span class="photos"><i class="fa fa-camera"></i> Photos</span> -->

                        <?php
                            if ($chambre['nbpersonnes'] > 1) {
                                $valeur = 'Personnes'; 
                            }
                            else {
                                $valeur = 'Personne'; 
                            }
                            if (isset($_GET['id']) AND $userinfo['id'] == $_SESSION['id']) {
                                if ($userinfo['admin'] == 1) {
                                    echo '<span class="avis"><img src="../images/nb1.png" height="18" width="18"> <input id="localisation" name="nbpersonnes" type="text" value="'.$chambre['nbpersonnes'].'"> </span>';
                                }
                                else {
                        ?>  

                            <span class="avis"><img src="../images/nb1.png" height="18" width="18"> <?php echo $chambre['nbpersonnes'].' '.$valeur; ?> </span>

                        <?php
                                }
                            }
                            else {
                        ?>  

                        <span class="avis"><img src="../images/nb1.png" height="18" width="18"> <?php echo $chambre['nbpersonnes'].' '.$valeur; ?> </span>

                        <?php
                            }
                        ?>

                    </div>

                    <?php 
                        if (isset($_GET['id']) AND $_GET['id'] > 0) {
                            if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                                if ($userinfo['admin'] == 0) {
                                    echo '<br><button type="submit" class="more" name="reserver'.$chambre['id'].'"> <b>Réserver</b> </button>'; 
                                }
                                else if ($userinfo['admin'] == 1) {
                                    echo '<button type="submit" onClick="return confirm(\'Vous êtes sur le point de modifier une chambre\');" name="modifier" class="more"> <b>Modifier</b> </button>';
                    ?>

                    </form>

                    <form method="POST" action="supprimer_chambre.php?id=<?php echo $_SESSION['id'] ?>">

                        <input type="hidden" name="id_chambre" value="<?php echo $chambre['id'] ?>">
                        <br><button type="submit" onClick="return confirm('Vous êtes sur le point de supprimer une chambre');" class="more" id="more" name="supprimer"> <b>Supprimer</b> </button> 
                    </form>

                    <?php
                                }
                            }
                        }
                        else {
                            echo '<br><button type="submit" class="more" name="reserver"> <b>Réserver</b> </button>';
                        }
                    ?>

                </div>

            </div>

        </div>

    </div>

    <?php
    }
    $affiche->closeCursor();
    ?>

    <br>
    
</div>

<div class="clearfix"></div>

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

</body>

<?php
}
catch (PDOException $e) {
    echo '<p> Erreur de connexion </p>';
    die();
}
?>

</html>

