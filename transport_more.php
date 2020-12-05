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
            header('LOCATION:transport_more.php');
        else if ($userinfo['id'] != $_SESSION['id'])
            header('LOCATION:transport_more.php?id='.$_SESSION['id']);
    }
    else if (isset($_SESSION['id'])) {
        header('LOCATION:transport_more.php?id='.$_SESSION['id']);
    }

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <title>Projet Web</title>
    
    <link rel="icon shortcut" href="../images/favicon.png">

    <link href="../css/style.css" rel="stylesheet" type="text/css">

    <link href="../css/transport_more.css" rel="stylesheet" type="text/css">

 

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

                <?php echo '<li><a href="index.php?id='.$_GET['id'].'">Accueil</a></li>'; ?>

                <li><a href="<?php echo 'hebergement.php?id='.$_GET['id'].''; ?>">Hebergement</a></li>

                <li><a href="<?php echo 'contact.php?id='.$_GET['id'].''; ?>">Contact</a></li>

                <li><a href="<?php echo 'transport.php?id='.$_GET['id'].''; ?>">Transport</a></li>
            <?php
                }
            }

            else {
            ?>

                <li><a href="index.php">Accueil</a></li>

                <li><a href="hebergement.php">Hebergement</a></li>

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
<h1 class="text-muted text-center">
<?php
    if (isset($_POST['voiture'])) {
        echo 'Voitures de type : '.$_POST['voiture'];
    }
    else {
        echo 'La liste des voitures';
    }
?>
</h1>
<?php
    if (isset($_GET['id']) AND $_GET['id'] > 0) {
        if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
            if ($userinfo['admin'] == 1) {
?>
<a href="ajouter_voiture.php?id=<?php echo $_GET['id'] ?>"><button id="button11" name="bouton1"> <b>Ajouter un type de voiture</b> </button></a>
<?php
            }
        }
    }
?>
<div class="container">

    <div class="stage">




        <div class="carResultDiv">
        <?php
            if (isset($_POST['voiture'])) {
                $type=$_POST['voiture'];
                if (isset($_GET['id']) AND $_GET['id'] > 0) {
			        if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
			            if ($userinfo['admin'] == 1) {
			                $affiche = $pdo->prepare("SELECT * FROM voiture WHERE type = '".$type."'");
			                $affiche->execute();
			            }
			            else if ($userinfo['admin'] == 0) {
			                $affiche = $pdo->prepare("SELECT * FROM voiture WHERE disponnibilite > 0  AND type = '".$type."'");
			                $affiche->execute();
			            }
			        }
			    }
			    else {
			    	$affiche = $pdo->prepare("SELECT * FROM voiture WHERE disponnibilite > 0  AND type = '".$type."'");
			        $affiche->execute();
			    }
            }

            else if (isset($_SESSION['type'])) {
			    $type=$_SESSION['type'];
                if (isset($_GET['id']) AND $_GET['id'] > 0) {
			        if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
			            if ($userinfo['admin'] == 1) {
			                $affiche = $pdo->prepare("SELECT * FROM voiture WHERE type = '".$type."'");
			                $affiche->execute();
			            }
			            else if ($userinfo['admin'] == 0) {
			            	$affiche = $pdo->prepare("SELECT * FROM voiture WHERE disponnibilite > 0  AND type = '".$type."'");
			                $affiche->execute();
			            }
			        }
			    }
			    else {
			            	$affiche = $pdo->prepare("SELECT * FROM voiture WHERE disponnibilite > 0  AND type = '".$type."'");
			                $affiche->execute();
			    }
            }
            else {
                if (isset($_GET['id']) AND $_GET['id'] > 0) {
			        if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
			            if ($userinfo['admin'] == 1) {
			                $affiche = $pdo->prepare("SELECT * FROM voiture");
			                $affiche->execute();
			            }
			            else if ($userinfo['admin'] == 0) {
			                $affiche = $pdo->prepare("SELECT * FROM voiture WHERE disponnibilite > 0");
			                $affiche->execute();
			            }
		            }
		        }
		        else {
		        	$affiche = $pdo->prepare("SELECT * FROM voiture WHERE disponnibilite > 0");
			        $affiche->execute();
		        }
            }
            while ($location = $affiche->fetch()) {
                if (isset($_GET['id']) AND $_GET['id'] > 0) {
                    if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                        if ($userinfo['admin'] == 0) {
                            echo '<form action="formulaire1.php?id='.$_SESSION['id'].'" method="POST">';
                        }
                        else if ($userinfo['admin'] == 1) {
                            echo '<form action="modifier_voiture.php?id='.$_SESSION['id'].'" method="POST" enctype="multipart/form-data">';
                        }
                    }
                }
                else {
                    echo '<form action="formulaire1.php" method="POST">';
                }
        ?>

            <!-- car-result -->

            <!-- Car class:  mini -->

            <table class="carResultRow">

                <tr>

                    <td class="carResultRow_GridFix-1"></td>

                    <td class="carResultRow_GridFix-2"></td>

                    <td class="carResultRow_GridFix-3"></td>

                    <td class="carResultRow_GridFix-4"></td>

                    <td class="carResultRow_GridFix-5"></td>

                    <td class="carResultRow_GridFix-6"></td>

                    <td class="carResultRow_GridFix-7"></td>

                </tr>

                <tr class="carResultRow_CarDetails">
                    <!-- car image area -->

                    <td class="carResultRow_CarImage" colspan="3">

                        <img class="col reg hero-img" src="<?php echo '../images/voitures/'.$location['image']; ?>" alt="<?php echo $location['marque'].' '.$location['modele'] ?>" title="<?php echo $location['marque'].' '.$location['modele'] ?>" onerror="this.src='../images/erreur.gif'">
                        <?php
                            if (isset($_GET['id']) AND $userinfo['id'] == $_SESSION['id']) {
                                if ($userinfo['admin'] == 1) {
                                    echo '<input type="file" name="image">';
                                }
                            }
                        ?>

                    </td>



                    <!-- car specifications -->

                    <td class="carResultRow_CarSpec" colspan="2">
                        
                        <?php
                            if (isset($_GET['id']) AND $userinfo['id'] == $_SESSION['id']) {

                                echo '<input type="hidden" name="id_voiture" value="'.$location['id'].'">';
                                if ($userinfo['admin'] == 1) {
                                	if ($location['disponnibilite'] < 1) {
                                		echo '<h2> <font color="red"> Voiture Non disponnible </font></h2>';
                                	}
                                    echo '<input class="input" name="marque" type="text" value="'.$location['marque'].'">';
                                    echo '<input class="input" name="modele" type="text" value="'.$location['modele'].'">';
                                }
                                else {
                        ?>

                            <h2><?php echo $location['marque'].' '.$location['modele'] ?>&nbsp;

                                <small>ou similaire</small>

                            </h2>

                        <?php
                                }
                            }
                            else {
                        ?>

                        <h2><?php echo $location['marque'].' '.$location['modele'] ?>&nbsp;

                            <small>ou similaire</small>

                        </h2>

                        <?php
                            }
                        ?>

                        <?php
                            if (isset($_GET['id']) AND $userinfo['id'] == $_SESSION['id']) {
                                if ($userinfo['admin'] == 1) {
                        ?>

                        <ul class="carResultRow_CarSpec-strong">
                            <li class="carResultRow_CarSpec_Doors">
                                <select class="form-control" name="sieges" >

                                    <option value="2"<?php if ($location['sieges'] == 2) echo 'selected'; ?>>2 Sièges</option>

                                    <option value="4"<?php if ($location['sieges'] == 4) echo 'selected'; ?>>4 Sièges</option>

                                    <option value="5"<?php if ($location['sieges'] == 5) echo 'selected'; ?>>5 Sièges</option>

                                    <option value="7"<?php if ($location['sieges'] == 7) echo 'selected'; ?>>7 Sièges</option>

                                </select>

                            </li>
                            
                            <li class="carResultRow_CarSpec_Doors">

                                <select class="form-control" name="portes" >

                                    <option value="2"<?php if ($location['portes'] == 2) echo 'selected'; ?>>2 Portes</option>

                                    <option value="4"<?php if ($location['portes'] == 4) echo 'selected'; ?>>4 Portes</option>

                                    <option value="5"<?php if ($location['portes'] == 5) echo 'selected'; ?>>5 Portes</option>
Portes
                                </select>

                            </li>

                            <li class="carResultRow_CarSpec_Luggage">

                                <?php
                                    if ($location['grandesvalises'] != 0) {
                                ?>

                                
                                <select class="form-control1" name="grandesvalises" >

                                    <option value="1"<?php if ($location['grandesvalises'] == 1) echo 'selected'; ?>>1 Grande valise</option>

                                    <option value="2"<?php if ($location['grandesvalises'] == 2) echo 'selected'; ?>>2 Grandes valises</option>

                                    <option value="3"<?php if ($location['grandesvalises'] == 3) echo 'selected'; ?>>3 Grandes valises</option>

                                    <option value="4"<?php if ($location['grandesvalises'] == 4) echo 'selected'; ?>>4 Grandes valises</option>

                                </select>
                                
                                <?php
                                }
                                if ($location['petitesvalises'] != 0) {
                                ?>

                                <select class="form-control1" name="petitesvalises" >
                                
                                    <option value="1"<?php if ($location['petitesvalises'] == 1) echo 'selected'; ?>>1 Petite valise</option>

                                    <option value="2"<?php if ($location['petitesvalises'] == 2) echo 'selected'; ?>>2 Petites valises</option>

                                    <option value="3"<?php if ($location['petitesvalises'] == 3) echo 'selected'; ?>>3 Petites valises</option>

                                    <option value="4"<?php if ($location['petitesvalises'] == 4) echo 'selected'; ?>>4 Petites valises</option>

                                </select>
                                
                                <?php
                                }     
                                ?>

                            </li>

                        </ul>

                        <?php
                                }
                                else {
                        ?>

                        <ul class="carResultRow_CarSpec-strong">
                            <li class="carResultRow_CarSpec_Seats"><?php echo $location['sieges']; ?>&nbsp;Sièges&nbsp;<em>|</em>

                            </li>
                            
                            <li class="carResultRow_CarSpec_Doors"><?php echo $location['portes']; ?>&nbsp;Portes&nbsp;<em>|</em>

                            </li>

                            <li class="carResultRow_CarSpec_Luggage">

                                <?php
                                    if ($location['grandesvalises'] != 0) {
                                ?>

                                
                                    <span class="carResultRow_CarSpec_Luggage-large"><?php echo $location['grandesvalises']; ?> Grande(s) valise(s)</span>
                                
                                <?php
                                }
                                if ($location['petitesvalises'] != 0) {
                                ?>

                                    <span class="carResultRow_CarSpec_Luggage-small"><?php echo $location['petitesvalises']; ?> Petite(s) valise(s)</span>
                                
                                <?php
                                }     
                                ?>

                            </li>

                        </ul>

                        <?php
                                }
                            }
                            else {
                        ?>

                        <ul class="carResultRow_CarSpec-strong">
                            <li class="carResultRow_CarSpec_Seats"><?php echo $location['sieges']; ?>&nbsp;Sièges&nbsp;<em>|</em>

                            </li>
                            
                            <li class="carResultRow_CarSpec_Doors"><?php echo $location['portes']; ?>&nbsp;Portes&nbsp;<em>|</em>

                            </li>

                            <li class="carResultRow_CarSpec_Luggage">

                                <?php
                                    if ($location['grandesvalises'] != 0) {
                                ?>

                                
                                    <span class="carResultRow_CarSpec_Luggage-large"><?php echo $location['grandesvalises']; ?> Grande(s) valise(s)</span>
                                
                                <?php
                                }
                                if ($location['petitesvalises'] != 0) {
                                ?>

                                    <span class="carResultRow_CarSpec_Luggage-small"><?php echo $location['petitesvalises']; ?> Petite(s) valise(s)</span>
                                
                                <?php
                                }     
                                ?>

                            </li>

                        </ul>

                        <?php
                            }
                        ?>  

                        <?php
                            if (isset($_GET['id']) AND $userinfo['id'] == $_SESSION['id']) {
                                if ($userinfo['admin'] == 1) {
                        ?>

                        <span class="carResultRow_CarSpec_CarCategory">
                            <select class="form-control2" name="type">

                                <option value="Mini"<?php if (strcmp($location['type'],'Mini') == 0) echo 'selected'; ?>>Mini</option>

                                <option value="Economique"<?php if (strcmp($location['type'],'Economique') == 0) echo 'selected'; ?>>Economique</option>

                                <option value="Compacte"<?php if (strcmp($location['type'],'Compacte') == 0) echo 'selected'; ?>>Compacte</option>

                                <option value="Monospace"<?php if (strcmp($location['type'],'Monospace') == 0) echo 'selected'; ?>>Monospace</option>

                                <option value="Intérmediaire"<?php if (strcmp($location['type'],'Intérmediaire') == 0) echo 'selected'; ?>>Intérmediaire</option>

                                <option value="SUV"<?php if (strcmp($location['type'],'SUV') == 0) echo 'selected'; ?>>SUV</option>

                            </select>

                            <div>
                                Voitures disponnibles :
                                <input type="number" name="disponnibilite" value="<?php echo $location['disponnibilite'] ?>" min="0"  class="input">
                            </div>

                        </span>

                        <?php
                                }
                                else {
                        ?>

                            <ul class="carResultRow_CarSpec-tick">

                                <li>Air conditionné</li>

                                <li>Boîte manuelle</li>

                            </ul>

                            <span class="carResultRow_CarSpec_CarCategory"><?php echo $location['type']; ?></span>

                        <?php
                                }
                            }
                            else {
                        ?>

                        <ul class="carResultRow_CarSpec-tick">

                            <li>Air conditionné</li>

                            <li>Boîte manuelle</li>

                        </ul>

                        <span class="carResultRow_CarSpec_CarCategory"><?php echo $location['type']; ?></span>

                        <?php
                            }
                        ?>

                    </td>



                    <!-- price -->

                    <td class="carResultRow_Price " colspan="2">

                        <span class="carResultRow_Price-duration">Prix  ​​pour  2  jours :</span>

                        <?php
                            if (isset($_GET['id']) AND $userinfo['id'] == $_SESSION['id']) {
                                if ($userinfo['admin'] == 1) {
                        ?>

                        <input id="prix" name="prix" type="text" value="<?php echo $location['prix']; ?>"> EUR

                        <?php
                                }
                                else {
                        ?>
                            
                            <span class="carResultRow_Price-now"><?php echo $location['prix'].' EUR'; ?></span>

                        <?php
                                }
                            }
                            else {
                        ?>
                        
                        <span class="carResultRow_Price-now"><?php echo $location['prix'].' EUR'; ?></span>

                        <?php
                            }
                        ?>

                    </td>



                </tr>



                <tr class="carResultRow_OfferInfo">


                    <td class="carResultRow_OfferInfo_FuelInfo" colspan="2">

                        <div class="carResultRow_OfferInfo_FuelInfo-wrap">

                            <span class="carResultRow_OfferInfo_FuelInfo-img"></span>

                            <h4>Politique de carburant </h4>

                            <?php
                                if (isset($_GET['id']) AND $userinfo['id'] == $_SESSION['id']) {
                                    if ($userinfo['admin'] == 1) {
                            ?>

                                <select class="form-control3"  name="carburant">

                                    <option value="0" <?php if ($location['carburant'] == 0) echo 'selected'; ?>>Payable &agrave; l&rsquo;avance</option>

                                    <option value="1" <?php if ($location['carburant'] == 1) echo 'selected'; ?>>Plein &agrave; rendre plein</option>

                                </select>

                            <?php
                                    }
                                    else {
                            ?>

                                <p>

                                    <?php
                                        if ($location['carburant'] == 0) {
                                    ?>
                                    <strong>Payable &agrave; l&rsquo;avance</strong>
                                    <?php 
                                        }
                                        else {
                                    ?>

                                    <strong>Plein &agrave; rendre plein</strong>
                                    <?php
                                        }
                                    ?>

                                </p>

                            <?php
                                    }
                                }
                                else {
                            ?>

                            <p>

                                <?php
                                    if ($location['carburant'] == 0) {
                                ?>
                                <strong>Payable &agrave; l&rsquo;avance</strong>
                                <?php 
                                    }
                                    else {
                                ?>

                                <strong>Plein &agrave; rendre plein</strong>
                                <?php
                                    }
                                ?>

                            </p>

                            <?php
                                }
                            ?>

                        </div>

                    </td>



                    <!-- free goodies -->



                    <td class="carResultRow_OfferInfo_FreeGoodies" colspan="4" rowspan="2">

                        <div class="carResultRow_OfferInfo_FreeGoodies-wrap">

                            <h4 class="txt-green-1">

                                <strong>Nous vous offrons les options suivantes GRATUITEMENT :</strong></h4>



                            <ul class="result_included">

                                <li class="result_includes">

                                    Modifications

                                </li>

                                <li class="result_includes">

                                    Protection en cas de vol

                                </li>

                                <li class="result_includes">

                                    Couverture partielle en cas de collision

                                </li>



                            </ul>

                        </div>

                    </td>



                    <!-- toolbar -->

                    <td class="carResultRow_OfferInfo-toolbar" rowspan="2">
                    <?php
                    if (isset($_GET['id']) AND $_GET['id'] > 0) {
                        if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
                            if ($userinfo['admin'] == 0) {

                                echo '<button type="submit" id="button1" name="reserver'.$location['id'].'"> <b>Réserver</b> </button>';
                                if (isset($_POST['voiture']) AND isset($_POST['date1'])) {
                                    $_SESSION['type'] = $_POST['voiture'];
                                    $_SESSION['date1'] = $_POST['date1'];
                                }
                            }

                            else if ($userinfo['admin'] == 1) {
                    ?>
                            
                            <button type="submit" onClick="return confirm('Vous êtes sur le point de modifier une voiture');" id="button1" name="bouton1"> <b>Modifier</b> </button>

                    </form>

                            <form method="POST" action="supprimer_voiture.php?id=<?php echo $_GET['id'] ?>">
                            <input type="hidden" name="id_voiture" value="<?php echo $location['id'] ?>">
                            <button type="submit" onClick="return confirm('Vous êtes sur le point de supprimer une voiture');" id="button2" name="bouton2"> <b>Supprimer</b> </button> 
                            </form>
                    <?php
                                if (isset($_POST['voiture']) AND isset($_POST['date1'])) {
                                    $_SESSION['idvoiture'] = $location['id'];
                                }
                    ?>
                            <div class="book-loader"></div>


                    <?php
                            }
                        }
                    }
                    else {
                        echo '<button type="submit" id="button1" name="reserver"> <b>Réserver</b> </button>';
                    }
                    ?>

                    </td>



                </tr>

                <tr class="carResultRow_OfferInfo">

                    <!-- supplier location -->

                    <td class="carResultRow_OfferInfo_SupplierLocation " colspan="2">

                        <div class="carResultRow_OfferInfo_SupplierLocation-wrap">

                            <div class="carResultRow_OfferInfo_SupplierLocation_Airport  carResultRow_OfferInfo_SupplierLocation_PickUp">

                                <span class="carResultRow_OfferInfo_SupplierLocation-img"></span>

                                <h4>Marrakech Aéroport</h4>

                                <p ">Dans le terminal</p>

                            </div>

                        </div>

                    </td>

                </tr>



            </table>



        <?php
        }
        $affiche->closeCursor();
        ?>
        </div>



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

</body>

<?php
}
catch (PDOException $e) {
    echo '<p> Erreur de connexion </p>';
    die();
}
?>

</html>

