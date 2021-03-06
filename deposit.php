<?php
session_start();

if(!isset($_SESSION['username']))
	header('location:index.php');

require_once('includes/config.php');
require_once('includes/jsonRPCClient.php');
require_once('includes/bcfunctions.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?php printf(SITENAME);?> - Deposit</title>
		<link rel="stylesheet" href="css/styles.css"  type="text/css" />
	</head>
	<body>
		<div id="main">
			<div id="top"><div style='float:left;position:relative;top:25px;'><h2><?php printf(SITENAME);?></h2></div></div>
			<div id="wrapper">
				<div id="content">
					<div class="innermargin">
						<h1><?php printf(SITENAME);?> Deposit</h1>
						<br />
						Pour faire un dépôt, merci d'utiliser cette adresse. Les adresses précédentes sont également valable<br />
						<?php
						
						$bitcoin = new jsonRPCClient('http://' . USER . ':' . PASS . '@' . SERVER . ':' . PORT .'/',false);
						
						// Controle adresse du client avec Anon si besoin
						if($_SESSION['anon'] == 1)
						{
							if(isset($_SESSION['sendaddress'])) {
								$sendaddress = refreshAddressIfStale($bitcoin,$_SESSION['sendaddress']); // session exists, check if its been used before
								$_SESSION['sendaddress'] = $sendaddress;
							} else {
								// if address already exists in wallet (or new unfortunately), check the balance and set as main receivable address if zero
								$curaddress = $bitcoin->getaccountaddress($_SESSION['username']);
								$sendaddress = refreshAddressIfStale($bitcoin,$curaddress);
								$_SESSION['sendaddress'] = $sendaddress;
							}
							$DBReq = "UPDATE comptes SET wallet = '" . $_SESSION['sendaddress'] . "' WHERE login LIKE '" . $_SESSION['username'] . "';";
							$conn->query($DBReq);
						if(DEBUG) printf("DEBUG: Enregistre en BDD le Wallet avec -> " . $DBReq);
						}
						
						// Sauvegarde de la balance du Wallet
						saveCurrentBalance($bitcoin, $_SESSION['sendaddress']);
						echo "Votre Wallet courant : <b>" . $_SESSION['sendaddress'] . "</b><br />";
						
						// Récupération des autres adresses du compte.
						$listAddr = $bitcoin->getaddressesbyaccount($_SESSION['username']);
						$i = 0;
						if ( count($listAddr) > 1 )
						{
							// Dans le cas ou il existe plus d'une adresse sur le compte
							echo "Voici la liste de vos adresses disponibles<br />";
							echo "<ul>";
							while ($i < count($listAddr))
							{
								echo "<li>" . $listAddr[$i] . "</li>";
								$i++;
							}
							echo "</ul>";
						}
						
						?>
					</div>
				</div>
			</div>
			<div id="menu">
				<div class="menumargin">
					<a href='index.php'>Acceuil</a>
					<a href='account.php'>Compte</a>
					<a href='deposit.php'>Dépôt</a>
					<a href='withdraw.php'>Transfert</a>
					<a href='contact.php'>Contact</a>
					<a href='logout.php'>Logout</a>
				</div>
			</div>
			<div id="footer"><a href="index.php">Acceuil</a> | <a href="account.php">Compte</a> | <a href="deposit.php">Dépôt</a> | <a href="withdraw.php">Transfert</a> | <a href="contact.php">Contact</a> | <a href="#">Logout</a> | </div>
		</div>
	</body>
</html>
