<?php

include('config.php');

if( isset($_SESSION['user_id']) ) {
	header("Location: index.php");
}

?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva</title>
	<link rel="stylesheet" href="https://fonts.xz.style/serve/inter.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@exampledev/new.css@1.1.2/new.min.css">
</head>
<body>
<header id="top"><h1>Reserva de piscina</h1></header>

<?php
	if( isset($_SESSION['user_id']) ) {
		header("Location: index.php");
	}

	if( ! empty($_POST['piso']) && ! empty($_POST['password']) ) {
		$results = $db->get_results('SELECT piso, email, password FROM pisos WHERE piso = "'. $db->escape(trim($_POST['piso'])) . '"');

		if( ! is_null($results) && password_verify($db->escape(trim($_POST['password'])), $results[0]->password) ){
			$_SESSION['user_id'] = $results[0]->piso;
			header("Location: index.php");
		} else {	
			echo '<p><strong>Error.</strong> La contraseña es incorrecta.</p>';
		}
	}
?>
	<h2>Acceso</h2>
	<form action="login.php" method="POST">
		<input type="text" placeholder="Portal-Planta-Puerta. Ej: A-1-1" name="piso">
		<input type="password" placeholder="Contraseña" name="password">
		<input type="submit" value="Enviar">
	</form>
</body>
</html>
