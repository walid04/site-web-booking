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
            header('LOCATION:index.php');
        else if ($userinfo['id'] != $_SESSION['id']) {
            if ($userinfo['admin'] == 0)
                header('LOCATION:index.php?id='.$_SESSION['id']);
            if ($userinfo['admin'] == 1)
                header('LOCATION:ajouter_chambre.php?id='.$_SESSION['id']);
        }
        else if ($userinfo['id'] == $_SESSION['id']) {
            if ($userinfo['admin'] == 0)
                header('LOCATION:index.php?id='.$_SESSION['id']);
        }
    }
    else {
        header('LOCATION:index.php');
    }

    if (isset($_GET['id']) AND $_GET['id'] > 0) {
        if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
            if ($userinfo['admin'] == 1) {

                if (isset($_POST['type']) AND isset($_POST['localisation']) AND isset($_POST['description']) AND isset($_POST['prix']) AND isset($_POST['nbpersonnes']) AND isset($_POST['disponnibilite']) AND isset ($_FILES['image'])) { 

                    $type = htmlspecialchars($_POST['type']);
                    $localisation = htmlspecialchars($_POST['localisation']);
                    $description = htmlspecialchars($_POST['description']);
                    $prix = htmlspecialchars($_POST['prix']);
                    $nbpersonnes = htmlspecialchars($_POST['nbpersonnes']);
                    $disponnibilite = htmlspecialchars($_POST['disponnibilite']);

                    $taillemax = 2097152;
                    $extensionsValides = array('jpg', 'jpeg', 'png', 'gif');
                    if ($_FILES['image']['size'] <= $taillemax) {
                        $extensionUpload = strtolower(substr(strrchr($_FILES['image']['name'], '.'), 1));
                        if (in_array($extensionUpload, $extensionsValides)) {
                            $dernier_id = $pdo->prepare('SELECT id FROM chambre ORDER BY id DESC LIMIT 1');
                            $dernier_id->execute();
                            $dernier = $dernier_id->fetch();
                            $id_v = $dernier['id']+1;
                            $chemin = "../images/chambres/".$id_v.".".$extensionUpload;
                            $resultat = move_uploaded_file($_FILES['image']['tmp_name'], $chemin);
                            if ($resultat) {
                                $image = $id_v.".".$extensionUpload;
                                $updateimage = $pdo->prepare('INSERT INTO chambre (id, type, disponnibilite, nbpersonnes, image, prix, localisation, description) VALUES (:id, :type, :disponnibilite, :nbpersonnes, :image, :prix, :localisation, :description)');
                                $updateimage->execute(array(
                                    'id' => $id_v,
                                    'type' => $type,
                                    'disponnibilite' => $disponnibilite,
                                    'nbpersonnes' => $nbpersonnes,
                                    'image' => $id_v.".".$extensionUpload,
                                    'prix' => $prix,
                                    'localisation' => $localisation,
                                    'description' => $description,
                                    ));
                            }
                            else {
                                $msg = "Erreur durant l'importation de l'image";
                            }
                        }
                        else {
                            $msg = "La photo doit être au format jpg, jpeg, png ou gif";
                        }
                    }
                    else {
                        $msg = "La photo ne doit pas dépasser 2 Mo";
                    }

                    header('LOCATION:hebergement.php?id='.$_SESSION['id']);

                }
            }
        }
    }

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <title>Projet Web</title>

    <link rel="icon shortcut" href="../images/favicon.png">

    <link href="../css/style.css" rel="stylesheet" type="text/css">

    <link href="../css/ajouter_chambre.css" rel="stylesheet" type="text/css">

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
                        $nbreservation = $reqreservetion->rowCount();

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
                        $nbreservation = $reqreservetion->rowCount();

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

<div id="modifier">
    <br>
    <h2> Ajout d'un type de chambre </h2>
    <br>
    <form class="bookform form-inline row" action="ajouter_chambre.php?id=<?php echo $_GET['id'] ?>" method="POST" enctype="multipart/form-data">
        <table>
            <tr>
                <td>
                    <label for="nom"> Type :  </label>
                </td>
                <td>
                    <input id="nom" class="form-control" type="text" name="type" value="" required><br><br>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="prenom"> Localisation : </label> 
                </td>
                <td>
                    <input id="prenom" class="form-control" type="text" name="localisation" value="" required><br><br>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="desc"> Description : </label> 
                </td>
                <td>
                    <textarea id="desc" name="description" class="form-control"></textarea>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="nbpersonnes"> Nombre de personnes : </label> 
                </td>
                <td>
                    <select class="form-control" name="nbpersonnes" style="margin-top: 20px;" required>

                        <option value="Nombre de personnes..." selected>Nombre de personnes...</option>

                        <option value="1">1</option>

                        <option value="2">2</option>

                        <option value="3">3</option>

                        <option value="4">4</option>

                    </select>
                    <br><br>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="prix"> Prix : </label> 
                </td>
                <td>
                    <input id="prix" maxlength="3" class="form-control" type="text" name="prix" value="" required><br><br>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="email" style="margin-top: 30px;"> Nombres de chambres disponnibles : </label> 
                </td>
                <td>
                    <input id ="disponnibilite" type="number" name="disponnibilite" min="1" class="form-control" placeholder="1" required>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="confnewemail">  Image : </label>
                </td>
                <td>
                    <input id="confnewemail" type="file" name="image" required><br><br>
                </td>
            </tr>

        </table>

        <span id="span"></span>
        <input name="valider" id="valider" type="submit" value="Ajouter">

        <br><br>

        <?php
            if (isset($erreur)) {
                echo $erreur;
            }
        ?>

    </form>
</div>


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
