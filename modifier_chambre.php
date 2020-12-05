<?php

session_start();

include 'connex.inc.php';

try {

    if (isset($_POST['id_chambre'])) {
        if (isset($_GET['id']) AND $_GET['id'] > 0) {
            $getid = intval($_GET['id']);
            $requser = $pdo->prepare('SELECT * FROM user WHERE id = ?');
            $requser->execute(array($getid));
            $userinfo = $requser->fetch();
            if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                if ($userinfo['admin'] == 1) {

                    $affiche = $pdo->prepare("SELECT * FROM chambre WHERE id = ?");
                    $affiche->execute(array($_POST['id_chambre']));
                    $chambre = $affiche->fetch();

                    if (isset ($_FILES['image'])) {
                        echo '1';
                        $taillemax = 2097152;
                        $extensionsValides = array('jpg', 'jpeg', 'png', 'gif');
                            if ($_FILES['image']['size'] <= $taillemax) {
                                $extensionUpload = strtolower(substr(strrchr($_FILES['image']['name'], '.'), 1));
                            if (in_array($extensionUpload, $extensionsValides)) {
                                $chemin = "../images/chambres/".$_POST['id_chambre'].".".$extensionUpload;
                                $resultat = move_uploaded_file($_FILES['image']['tmp_name'], $chemin);
                                if ($resultat) {
                                    $updateimage = $pdo->prepare('UPDATE chambre SET image = :image WHERE id = :id');
                                    $updateimage->execute(array(
                                        'image' => $_POST['id_chambre'].".".$extensionUpload,
                                        'id' => $_POST['id_chambre']
                                        ));
                                        header('LOCATION:hebergement.php?id='.$_SESSION['id']);
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
                    }

                    if (isset($_POST['type']) AND !empty($_POST['type'])) {
                        if ($_POST['type'] != $chambre['type']) {
                            $type = htmlspecialchars($_POST['type']);
                            $inserttype = $pdo->prepare("UPDATE chambre SET type = ? WHERE id = ?");
                            $inserttype->execute(array($type, $_POST['id_chambre']));
                            header('LOCATION:hebergement.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['description']) AND !empty($_POST['description'])) {
                        if ($_POST['description'] != $chambre['description']) {
                            $description = htmlspecialchars($_POST['description']);
                            $insertdescription = $pdo->prepare("UPDATE chambre SET description = ? WHERE id = ?");
                            $insertdescription->execute(array($description, $_POST['id_chambre']));
                            header('LOCATION:hebergement.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['disponnibilite']) AND !empty($_POST['disponnibilite'])) {
                        if ($_POST['disponnibilite'] != $chambre['disponnibilite']) {
                            $disponnibilite = htmlspecialchars($_POST['disponnibilite']);
                            $insertdisponnibilite = $pdo->prepare("UPDATE chambre SET disponnibilite = ? WHERE id = ?");
                            $insertdisponnibilite->execute(array($disponnibilite, $_POST['id_chambre']));
                            header('LOCATION:hebergement.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['localisation']) AND !empty($_POST['localisation'])) {
                        if ($_POST['localisation'] != $chambre['localisation']) {
                            $localisation = htmlspecialchars($_POST['localisation']);
                            $insertlocalisation = $pdo->prepare("UPDATE chambre SET localisation = ? WHERE id = ?");
                            $insertlocalisation->execute(array($localisation, $_POST['id_chambre']));
                            header('LOCATION:hebergement.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['nbpersonnes']) AND !empty($_POST['nbpersonnes'])) {
                        if ($_POST['nbpersonnes'] != $chambre['nbpersonnes']) {
                            $nbpersonnes = htmlspecialchars($_POST['nbpersonnes']);
                            $insertnbpersonnes = $pdo->prepare("UPDATE chambre SET nbpersonnes = ? WHERE id = ?");
                            $insertnbpersonnes->execute(array($nbpersonnes, $_POST['id_chambre']));
                            header('LOCATION:hebergement.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['prix']) AND !empty($_POST['prix'])) {
                        if ($_POST['prix'] != $chambre['prix']) {
                            $prix = htmlspecialchars($_POST['prix']);
                            $insertprix = $pdo->prepare("UPDATE chambre SET prix = ? WHERE id = ?");
                            $insertprix->execute(array($prix, $_POST['id_chambre']));
                            header('LOCATION:hebergement.php?id='.$_SESSION['id']);
                        }
                    }
                    header('LOCATION:hebergement.php?id='.$_SESSION['id']);
                }
            }
        }
    }
    else {
        if (isset($_GET['id']) AND $_GET['id'] > 0 AND isset($_SESSION['id'])) {
            if ($userinfo['id'] == $_SESSION['id']) {
                if ($userinfo['admin'] == 0) 
                    header('LOCATION:index.php?id='.$_SESSION['id']);
                else 
                    header('LOCATION:index.php?id='.$_SESSION['id']);
            }
            else 
                header('LOCATION:index.php?id='.$_SESSION['id']);
        }
        else 
            header('LOCATION:index.php');
    }

}
catch (PDOException $e) {
    echo '<p> Erreur de connexion </p>';
    die();
}
?>
