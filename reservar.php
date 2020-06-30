<?php

include('config.php');

if( ! isset($_SESSION['user_id']) ) {
	header("Location: login.php"); 
}

$piso=$_SESSION['user_id'];

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
	<main>
<?php

try {
	foreach ($_GET as $reserva => $personas) {
		$fecha = explode("_", $reserva);

		/* $fecha[1] es el día
			$fecha[2] es el mes
			$fecha[3] es la franja
		*/
		if ( ! (is_numeric($fecha[1]) && in_array($fecha[2], array("junio", "julio", "agosto", "septiembre")) && is_numeric($fecha[3]) ) ) {
			echo '<p class="error"><strong>Hubo un error al realizar la reserva.</strong> Uno de los parámetros no es correcto.</p>';
			echo '<p>Envíe este código y el mensaje que aparece a continuación al reportar el error. CODIGO: 1<br />';
			echo var_dump($fecha);
			echo '</p>';
			break;
		} else {
			$db->query('DELETE FROM reservas_solicitadas WHERE dia = ' . $db->escape($fecha[1]) . ' AND mes = "' . $db->escape(trim($fecha[2])) . '" AND franja = ' . $db->escape($fecha[3]) . ' AND piso ="'. $piso .'"');
			if ( is_numeric($personas) && $personas > 0 && $personas <= MAX_PERSONAS_RESERVA) {
				$db->query('INSERT INTO reservas_solicitadas (dia, mes, franja, personas, piso) VALUES (' . $db->escape($fecha[1]) . ', "' . $db->escape(trim($fecha[2]))  . '", '  . $db->escape($fecha[3])  . ', '  . $db->escape($personas) . ', "'. $piso .'")');
			}
		}
	}
	
	echo '<p><strong>Reserva realizada correctamente.</strong></p><p><a href="index.php">&larr;&nbsp; Volver al inicio</a></p>';
} catch (Exception $e) {
	Error::printError($e->getMessage());
}

?>
	</main>
</body>
</html>