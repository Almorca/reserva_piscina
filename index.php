<?php

/**
	Login code from https://github.com/thedevdojo/php-login-script
*/

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
			tr.reserva_confirmada { background-color: #CAFAFE;}
			footer {text-align: center}
		</style>
	</head>
	<body>
	<header id="top" style="text-align: center;"><h1>Reserva de piscina</h1>
	<p>Piso 
	<?php echo $_SESSION['user_id']; ?>
	&nbsp;&bull;&nbsp; <a href="account.php">Modificar datos</a>
	&nbsp;&bull;&nbsp; <a href="logout.php">Terminar sesión</a>
	</p>
	</header>
	<main>
		<form action="reservar.php">
		<table summary="Reservar piscina">
			<caption>Selecciona los días a reservar y el número de personas que irá cada día (máximo <?php echo MAX_PERSONAS_RESERVA; ?> personas por reserva):</caption>
			<thead>
				<tr>
				  <th scope="col" style="text-align: center;">Fecha</th>
				  <th scope="col" style="text-align: center;">Personas</th>
				  <th scope="col"style="text-align: center;">Estado</th>
				</tr>
			</thead>
			<tbody>
<?php
try {
	if($franjas = $db->get_results("SELECT f.*, r.personas, COALESCE(r.estado, -1) as estado, STR_TO_DATE(CONCAT(f.dia, '/', f.num_mes, '/2020'), '%d/%m/%Y')-CURRENT_DATE() as dias_hasta, estado_franja FROM franjas f LEFT JOIN reservas_solicitadas r ON f.dia = r.dia and f.mes = r.mes and f.franja = r.franja and r.piso ='$piso' ORDER BY dia, franja ASC")) {
		foreach( $franjas as $franja ) {
			if ($franja->estado_franja == 1 ) { // Esta franja ya no admite reservas
				echo '<tr class="reserva_confirmada" title="Esta franja ya no admite modificaciones.">';
			} elseif ( $franja->estado == 1) {
				echo '<tr>';
			} else {
				echo '<tr>';	
			}
			// Se imprime la franja
			echo '<td>' . $franja->dia . ' de ' . $franja->mes . ' - ';
			if ($franja->franja == 1) {
				echo 'Mañana (11:00 - 15:00)';
			} else {
				echo 'Tarde (16:00 - cierre)';
			}
			echo '</td><td style="text-align: center;">';
			// Se imprime el número de personas que se solicitó
			if ($franja->estado_franja == 1 ) { // La franja ya no admite modificaciones
				echo $franja->personas;
			} else {
				echo '<input style="max-width: 5em" type="number" min="0" max="'. MAX_PERSONAS_RESERVA .'" placeholder="0-'. MAX_PERSONAS_RESERVA .'" name="personas_' . $franja->dia . '_' . $franja->mes . '_' . $franja->franja . '"';
				if ( $franja->personas ) {
					echo ' value="' . $franja->personas . '">';
				}
			}
			echo '</td>';
			if ( $franja->estado == 1) {
				echo '<td><strong>Confirmada &#10004;</strong>';
			} elseif ( $franja->estado == 0) {
				echo '<td title="Lo sentimos, ha habido más reservas que aforo y tu reserva no ha sido seleccionada."><strong>Denegada por aforo<span style="color: red">&cross;</span></strong>';
			} else {
				echo '<td>';
			}
			
			echo '</td></tr>';
		}
	}
} catch (Exception $e) {
	echo '<p>Error: ';
	echo $e->getMessage();
	echo '<pre>';
	echo $db->debug();			
	echo '</pre>';
	echo '</p>';
}
?>
			</tbody><tfoot><tr><th colspan="3" style="text-align:center"><input type="submit" value="Modificar reservas"></th></tr></tfoot>
		</table>
	</form>
	</main>
	<footer>
		<a href="#top" title="Subir al inicio de la p&aacute;gina">Arriba <span>&uarr;</span></a> &nbsp;&bull;&nbsp; <a href="mailto:comunidadmaquinilla13@gmail.com">comunidadmaquinilla13@gmail.com</a>
	</footer>
	</body>
</html>
