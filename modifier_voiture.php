<?php

session_start();

include 'connex.inc.php';

try {

    if (isset($_POST['id_voiture'])) {
        if (isset($_GET['id']) AND $_GET['id'] > 0) {
            $getid = intval($_GET['id']);
            $requser = $pdo->prepare('SELECT * FROM user WHERE id = ?');
            $requser->execute(array($getid));
            $userinfo = $requser->fetch();
            if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                if ($userinfo['admin'] == 1) {
                    $affiche = $pdo->prepare("SELECT * FROM voiture WHERE id = ?");
                    $affiche->execute(array($_POST['id_voiture']));
                    $voiture = $affiche->fetch();
                    $_SESSION['type'] = $voiture['type'];

                    if (isset ($_FILES['image'])) {
                        $taillemax = 2097152;
                        $extensionsValides = array('jpg', 'jpeg', 'png', 'gif');
                        if ($_FILES['image']['size'] <= $taillemax) {
                            $extensionUpload = strtolower(substr(strrchr($_FILES['image']['name'], '.'), 1));
                            if (in_array($extensionUpload, $extensionsValides)) {
                                $chemin = "../images/voitures/".$_POST['id_voiture'].".".$extensionUpload;
                                $resultat = move_uploaded_file($_FILES['image']['tmp_name'], $chemin);
                                if ($resultat) {
                                    $updateimage = $pdo->prepare('UPDATE voiture SET image = :image WHERE id = :id');
                                    $updateimage->execute(array(
                                        'image' => $_POST['id_voiture'].".".$extensionUpload,
                                        'id' => $_POST['id_voiture']
                                        ));
                                        header('LOCATION:transport_more.php?id='.$_SESSION['id']);
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

                    if (isset($_POST['marque']) AND !empty($_POST['marque'])) {
                        if ($_POST['marque'] != $voiture['marque']) {
                            $marque = htmlspecialchars($_POST['marque']);
                            $insertmarque = $pdo->prepare("UPDATE voiture SET marque = ? WHERE id = ?");
                            $insertmarque->execute(array($marque, $_POST['id_voiture']));
                            header('LOCATION:transport_more.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['modele']) AND !empty($_POST['modele'])) {
                        if ($_POST['modele'] != $voiture['modele']) {
                            $modele = htmlspecialchars($_POST['modele']);
                            $insertmodele = $pdo->prepare("UPDATE voiture SET modele = ? WHERE id = ?");
                            $insertmodele->execute(array($modele, $_POST['id_voiture']));
                            header('LOCATION:transport_more.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['sieges']) AND !empty($_POST['sieges'])) {
                        if ($_POST['sieges'] != $voiture['sieges']) {
                            $sieges = htmlspecialchars($_POST['sieges']);
                            $insertsieges = $pdo->prepare("UPDATE voiture SET sieges = ? WHERE id = ?");
                            $insertsieges->execute(array($sieges, $_POST['id_voiture']));
                            header('LOCATION:transport_more.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['portes']) AND !empty($_POST['portes'])) {
                        if ($_POST['portes'] != $voiture['portes']) {
                            $portes = htmlspecialchars($_POST['portes']);
                            $insertportes = $pdo->prepare("UPDATE voiture SET portes = ? WHERE id = ?");
                            $insertportes->execute(array($portes, $_POST['id_voiture']));
                            header('LOCATION:transport_more.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['petitesvalises']) AND !empty($_POST['petitesvalises'])) {
                        if ($_POST['petitesvalises'] != $voiture['petitesvalises']) {
                            $petitesvalises = htmlspecialchars($_POST['petitesvalises']);
                            $insertpetitesvalises = $pdo->prepare("UPDATE voiture SET petitesvalises = ? WHERE id = ?");
                            $insertpetitesvalises->execute(array($petitesvalises, $_POST['id_voiture']));
                            header('LOCATION:transport_more.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['grandesvalises']) AND !empty($_POST['grandesvalises'])) {
                        if ($_POST['grandesvalises'] != $voiture['grandesvalises']) {
                            $grandesvalises = htmlspecialchars($_POST['grandesvalises']);
                            $insertgrandesvalises = $pdo->prepare("UPDATE voiture SET grandesvalises = ? WHERE id = ?");
                            $insertgrandesvalises->execute(array($grandesvalises, $_POST['id_voiture']));
                            header('LOCATION:transport_more.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['type']) AND !empty($_POST['type'])) {
                        if ($_POST['type'] != $voiture['type']) {
                            $type = htmlspecialchars($_POST['type']);
                            $inserttype = $pdo->prepare("UPDATE voiture SET type = ? WHERE id = ?");
                            $inserttype->execute(array($type, $_POST['id_voiture']));
                            header('LOCATION:transport_more.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['disponnibilite']) AND !empty($_POST['disponnibilite'])) {
                        if ($_POST['disponnibilite'] != $voiture['disponnibilite']) {
                            $disponnibilite = htmlspecialchars($_POST['disponnibilite']);
                            $insertdisponnibilite = $pdo->prepare("UPDATE voiture SET disponnibilite = ? WHERE id = ?");
                            $insertdisponnibilite->execute(array($disponnibilite, $_POST['id_voiture']));
                            header('LOCATION:transport_more.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['carburant']) AND !empty($_POST['carburant'])) {
                        if ($_POST['carburant'] != $voiture['carburant']) {
                            $carburant = htmlspecialchars($_POST['carburant']);
                            $insertcarburant = $pdo->prepare("UPDATE voiture SET carburant = ? WHERE id = ?");
                            $insertcarburant->execute(array($carburant, $_POST['id_voiture']));
                            header('LOCATION:transport_more.php?id='.$_SESSION['id']);
                        }
                    }

                    if (isset($_POST['prix']) AND !empty($_POST['prix'])) {
                        if ($_POST['prix'] != $voiture['prix']) {
                            $prix = htmlspecialchars($_POST['prix']);
                            $insertprix = $pdo->prepare("UPDATE voiture SET prix = ? WHERE id = ?");
                            $insertprix->execute(array($prix, $_POST['id_voiture']));
                            header('LOCATION:transport_more.php?id='.$_SESSION['id']);
                        }
                    }
                    header('LOCATION:transport_more.php?id='.$_SESSION['id']);
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
