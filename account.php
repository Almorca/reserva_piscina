<?php

include('config.php');

if( isset($_SESSION['user_id']) ){
	try {
		$results = $db->get_results('SELECT piso, email,password FROM pisos WHERE piso = "'. $db->escape(trim($_SESSION['user_id'])). '"');
	} catch (Exception $e) {
		echo '<p>Error: ';
		echo $e->getMessage();
		echo '<pre>';
		echo $db->debug();			
		echo '</pre>';
		echo '</p>';
	}

	if( is_null($results) ) {
		header("Location: login.php");
	}
} else {
	header("Location: login.php");
}

$piso=$db->escape(trim($_SESSION['user_id']));

?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva</title>
	<link rel="stylesheet" href="https://fonts.xz.style/serve/inter.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@exampledev/new.css@1.1.2/new.min.css">
	<style>
		footer {text-align: center}
		.mensaje {padding:1rem; background:var(--nc-bg-2); border-left:5px solid var(--nc-bg-3)}
		.error {padding:1rem; background:var(--nc-bg-2); border-left:5px solid red}
	</style>
</head>
<body>
<header id="top" style="text-align: center;">
	<h1>Mi perfil</h1>
	<p>Piso 
	<?php echo $_SESSION['user_id']; ?>
	&nbsp;&bull;&nbsp; <a href="index.php">Realizar reserva</a>
	&nbsp;&bull;&nbsp; <a href="logout.php">Terminar sesión</a>
	</p>
</header>
<main>
	<form action="account.php" method="GET">
<?php

/** Modificación de los datos. Se guarda en la base de datos. */
if (isset($_GET['user_mail']) || isset($_GET['user_password'])){
	try {
		if (isset($_GET['user_mail']) && ! empty($db->escape(trim($_GET['user_password'])))) {
			$db->query('UPDATE `pisos` SET email = "'. $db->escape(trim($_GET['user_mail'])) . '", password = "'. password_hash($db->escape(trim($_GET['user_password'])), PASSWORD_DEFAULT) . '" WHERE piso = "'. $piso . '"');
			echo "<p class='mensaje'>Se ha actualizado el correo electrónico y la contraseña</p>";
		} elseif (isset($_GET['user_mail'])) {
			 $db->query('UPDATE `pisos` SET email = "'. $db->escape(trim($_GET['user_mail'])) . '" WHERE piso = "'. $piso . '"');
			 echo "<p class='mensaje'>Se ha actualizado el correo electrónico.</p>";
		} else {
			$db->query('UPDATE `pisos` SET password = "'. password_hash($db->escape(trim($_GET['user_password'])), PASSWORD_DEFAULT) . '" WHERE piso = "'. $piso . '"');
			echo "<p class='mensaje'>Se ha actualizado la contraseña.</p>";
		}
	} catch (Exception $e) {
		echo '<p>Error: ';
		echo $e->getMessage();
		echo '<pre>';
		echo $db->debug();			
		echo '</pre>';
		echo '</p>';
	}
}

echo '<ul style="list-style-type: none; padding-left: 0;">';
try {	
	if($pisos = $db->get_results("SELECT * FROM pisos WHERE piso ='$piso'")) {
		echo '<li><label for="mail">Correo electrónico: </label>';
		echo '<input type="email" id="mail" name="user_mail" value="' . $pisos[0]->email .'">';
		echo '<li><label for="password">Contraseña: </label> ';
		echo '<input type="password" id="password" name="user_password" placeholder="Nueva contraseña" title="Escriba la nueva contraseña si desea cambiarla">';
	}
} catch (Exception $e) {
	echo '<p>Error: ';
	echo $e->getMessage();
	echo '<pre>';
	echo $db->debug();			
	echo '</pre>';
	echo '</p>';
}
echo '</li></ul>';
?>
		<button type="submit">Modificar datos</button>
	</form>
	</main>
	<footer>
		<a href="index.php"><span>&larr;</span> Volver al inicio</a> &nbsp;&bull;&nbsp; <a href="#top" title="Subir al inicio de la p&aacute;gina">Arriba <span>&uarr;</span></a> &nbsp;&bull;&nbsp; <a href="mailto:comunidadmaquinilla13@gmail.com">comunidadmaquinilla13@gmail.com</a>
	</footer>
	</body>
</html>