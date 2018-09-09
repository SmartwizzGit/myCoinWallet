<?php
session_start();

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
						To make a deposit, please send coins to the address below.<br />
						Pour faire un dépôt, merci d'utiliser cette adresse. Les adresses précédentes sont également valable<br />
						<?php
						
						$bitcoin = new jsonRPCClient('http://' . USER . ':' . PASS . '@' . SERVER . ':' . PORT .'/',false);
						
						// check for session address
						if(isset($_SESSION['sendaddress'])) {
							$sendaddress = refreshAddressIfStale($bitcoin,$_SESSION['sendaddress']); // session exists, check if its been used before
							$_SESSION['sendaddress'] = $sendaddress;
						} else {
							// if address already exists in wallet (or new unfortunately), check the balance and set as main receivable address if zero
							$curaddress = $bitcoin->getaccountaddress($_SESSION['username']);
							$sendaddress = refreshAddressIfStale($bitcoin,$curaddress);
							$_SESSION['sendaddress'] = $sendaddress;
						}

						// save current balance
						saveCurrentBalance($bitcoin, $_SESSION['sendaddress']);
						echo "<b>" . $_SESSION['sendaddress'] . "</b>";
						
						?>
					</div>
				</div>
			</div>
			<div id="menu">
				<div class="menumargin">
					<a href='index.php'>Home</a>
					<a href='account.php'>Account</a>
					<a href='deposit.php'>Deposit</a>
					<a href='withdraw.php'>Withdraw</a>
					<a href='contact.php'>Contact</a>
					<a href='logout.php'>Logout</a>
				</div>
			</div>
			<div id="footer"><a href="index.php">Home</a> | <a href="account.php">Account</a> | <a href="deposit.php">Deposit</a> | <a href="withdraw.php">Withdraw</a> | <a href="contact.php">Contact</a> | <a href="#">Logout</a> | </div>
		</div>
	</body>
</html>