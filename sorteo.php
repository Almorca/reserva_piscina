<?php

/** Este script se debe planificar para ejecutarse periódicamente mediante cron o similar */

include('config.php');

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'lib/Exception.php';
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';

define("DIAS_A_SORTEAR", 1); /** Número de días para los que se va a hacer el sorteo */
define("DIAS_PARA_COMIENZO", 0); /** Número de días que faltan para el primer día a sortear. Si el sorteo es el miércoles y se sortea de viernes en adelante ponemos 2. Si el sorteo es el mismo día 0 */

/** Envia un email a todos los solicitantes con el resultado de su reserva.
	El correo incluirá un número aleatorio para dificultar su falsificación.
	Todos las reservas recibirán el mismo código por lo tanto para que alguien cree un correo falso de reserva deberá antes obtener este número de correo de reserva legal.
	@codigo	Número aleatorio
*/
function enviarEmail($dia, $mes, $codigo) {
	global $db;
	
	switch ($mes) {
		case 6:
		$mes_texto = 'junio';
		break;
		case 7:
		$mes_texto = 'julio';
		break;
		case 8:
		$mes_texto = 'agosto';
		break;
		case 9:
		$mes_texto = 'septiembre';
		break;
	}
	
	try {
		// obtenemos las papeletas de cada piso
		$reservas = $db->get_results("SELECT r.piso, r.personas, r.estado, r.franja, p.email FROM `reservas_solicitadas` r INNER JOIN `pisos` p ON p.piso = r.piso WHERE r.dia = $dia and r.mes = '$mes_texto' AND p.email IS NOT NULL");
	} catch (Exception $e) {
		echo '<p>Error: ';
		echo $e->getMessage();
		echo '<pre>';
		echo $db->debug();			
		echo '</pre>';
		echo '</p>';
	}
	
	// Instantiation and passing `true` enables exceptions
	$mail = new PHPMailer(true);
	
	// Se crea un array llamado $papeletas cuyo índice es el piso y su valor es el número de papeletas que tiene.
	foreach( $reservas as $reserva ) {
		try {
			//Recipients
			$mail->setFrom('tu-correo@gmail.com', 'Remitente');
			$mail->addAddress($reserva->email, $reserva->piso);     // Add a recipient
			//$mail->addReplyTo('info@example.com', 'Information');
			
			// Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			// Content
			$mail->isHTML(true);                                  // Set email format to HTML
			
			$subject = 'Reserva piscina denegada - ' . $dia . ' ' . $mes_texto . ' - Turno ' . Franja_toString($reserva->franja);
			$body = "<p>Lo sentimos.</p><p>Ha habido más reservas que aforo y <strong>su reserva para el día $dia de $mes_texto - Turno " . Franja_toString($reserva->franja) . " no ha sido seleccionada</strong> en el reparto de aforo.</p>";
			$altbody = "Lo sentimos.Ha habido más reservas que aforo y <strong>su reserva para el día $dia de $mes_texto - Turno " . Franja_toString($reserva->franja) . " no ha sido seleccionada en el reparto de aforo.";
			if ($reserva->estado == 1) { // Reserva aceptada
				$subject = 'Reserva piscina aprobada - ' . $dia . ' ' . $mes_texto . ' ' . Franja_toString($reserva->franja);
				$body = "<p>¡Enhorabuena!</p><p><strong>Su reserva para el día $dia de $mes_texto - Turno ". Franja_toString($reserva->franja) ." está aprobada.</strong></p><ul><li>Fecha: $dia de $mes_texto</li><li>Turno: ". Franja_toString($reserva->franja) ."</li><li>Código del día: $codigo</li></ul><p>Saludos</p>";
				$altbody = "¡Enhorabuena! Su reserva para el día $dia de $mes_texto - Turno ". Franja_toString($reserva->franja) ." está aprobada. Código del día: $codigo. Saludos";
			}
			
			$mail->Subject = $subject;
			$mail->Body    = $body;
			$mail->AltBody = $altbody;
			
			//Read an HTML message body from an external file, convert referenced images to embedded,
			//convert HTML into a basic plain-text alternative body
			//$mail->msgHTML(file_get_contents('contents.html'), __DIR__);

			$mail->send();
			echo 'El mensaje se ha enviado';
		} catch (Exception $e) {
			echo "El mensaje no ha podido ser enviado. Error: {$mail->ErrorInfo}";
		}
	}
}

/** Se aprueban todas las reservas ya que hay menos peticiones que plazas */
function aprobarReservas($dia, $mes, $franja) {
	global $db;
	
	switch ($mes) {
		case 6:
		$mes_texto = 'junio';
		break;
		case 7:
		$mes_texto = 'julio';
		break;
		case 8:
		$mes_texto = 'agosto';
		break;
		case 9:
		$mes_texto = 'septiembre';
		break;
	}
	
	try {
		$db->query("UPDATE `reservas_solicitadas` SET estado = 1 WHERE dia = $dia AND mes = '$mes_texto' AND franja = $franja");
	} catch (Exception $e) {
		echo '<p>Error: ';
		echo $e->getMessage();
		echo '<pre>';
		echo $db->debug();			
		echo '</pre>';
		echo '</p>';
	}
}


/** Al haber más peticiones que plazas estas se sortean */
function sortearReservas($dia, $mes, $franja, $aforo) {
	global $db;
	
	switch ($mes) {
		case 6:
		$mes_texto = 'junio';
		break;
		case 7:
		$mes_texto = 'julio';
		break;
		case 8:
		$mes_texto = 'agosto';
		break;
		case 9:
		$mes_texto = 'septiembre';
		break;
	}
	
	try {
		// obtenemos las papeletas de cada piso
		$reservas = $db->get_results("SELECT reserva.piso, reserva.personas, total_papeletas.total_papeletas/(COALESCE(reservas_adjudicadas.papeletas_gastadas, 0)+1) as papeletas FROM (SELECT piso, personas FROM `reservas_solicitadas` WHERE dia = $dia and mes = '$mes_texto') reserva LEFT JOIN (SELECT piso, COUNT(*) as papeletas_gastadas FROM `reservas_solicitadas` WHERE estado = 1 GROUP BY piso) reservas_adjudicadas ON reservas_adjudicadas.piso = reserva.piso INNER JOIN (select COUNT(*) as total_papeletas FROM `franjas`) as total_papeletas");
	} catch (Exception $e) {
		echo '<p>Error: ';
		echo $e->getMessage();
		echo '<pre>';
		echo $db->debug();			
		echo '</pre>';
		echo '</p>';
	}
	
	// Se crea un array llamado $papeletas cuyo índice es el piso y su valor es el número de papeletas que tiene.
	foreach( $reservas as $reserva ) {
		$papeletas[$reserva->piso]=$reserva->papeletas;
	}
	
	// Se crea un array llamado $personas cuyo índice es el piso y su valor es el número de personas que solicita reservar
	foreach( $reservas as $reserva ) {
		$personas[$reserva->piso]=$reserva->personas;
	}
	
	/*
	Realizamos el sorteo mientras haya aforo.
	A cada piso se le asigna tantos números como papeletas tienen.
	Para el primer piso sus papeletas serán de la 0 hasta el número de papeletas que tenga. Ej: Si piso 1 tiene 4 papeletas sus números serán de 0 a 3.
	Para el segundo piso sus papeletas serán desde la última papeleta del piso anterior tantos números como papeletas tenga. Ej. Si piso 2 tiene 2 papeletas sun números serán de 4 (última papeleta del piso 1 + 1) a 5.
	*/
	$aforo_restante = $aforo;
	$i=0;

	do {
		$papeleta_ultimo_valor=0; // Última papeleta repartida
		foreach( $papeletas as $piso => $num_papeletas ) {
			$papeleta_ultimo_valor += $num_papeletas; // Vamos sumando el número de papeletas repartidas.
		}
		
		$papeleta = rand(1, $papeleta_ultimo_valor); // Elegimos un número al azar entre 1 y el número de papeletas dadas
		
		// Recorremos las papeletas buscando el rango en el que está el número obtenido al azar
		echo "<br />papeleta: $papeleta";
		
		$papeleta_inicial = 1; // Primera papeleta del primer piso. La primera papeleta del siguiente piso será última papeleta del piso anterior + 1.
		foreach ($papeletas as $piso => $num_papeletas) {
			if ( $papeleta >= $papeleta_inicial && $papeleta < $papeleta_inicial+$num_papeletas) {
				echo "<br />Admitido $i";
				try {
					$db->query("UPDATE `reservas_solicitadas` SET estado = 1 WHERE dia = $dia AND mes = '$mes_texto' AND franja = $franja AND piso = '$piso'");
				} catch (Exception $e) {
					echo '<p>Error: ';
					echo $e->getMessage();
					echo '<pre>';
					echo $db->debug();			
					echo '</pre>';
					echo '</p>';
				}
				echo '<br />Piso: ';
				print_r($piso);
				$aforo_restante -= $personas[$piso]; // Se reduce el aforo en el número de personas que haya en esta reserva.
				unset($papeletas[$piso]);
				unset($personas[$piso]);
				break;
			} else {
				$papeleta_inicial+=$num_papeletas; // Se calcula la papeleta inicial del siguiente piso que corresponde con la papele final 
			}
		}
		echo "<br />Aforo: $aforo_restante";
	} while ($aforo_restante >= MAX_PERSONAS_RESERVA);
	
	// Se actualiza el estado de las reservas que no han salido en el sorteo a rechazadas.
	try {
		$db->query("UPDATE `reservas_solicitadas` SET estado = 0 WHERE dia = $dia AND mes = '$mes_texto' AND franja = $franja AND estado is null");
	} catch (Exception $e) {
		echo '<p>Error: ';
		echo $e->getMessage();
		echo '<pre>';
		echo $db->debug();			
		echo '</pre>';
		echo '</p>';
	}
}


$semanaSiguiente = time(); //+ (5 * 24 * 60 * 60); // Lunes siguiente

for ( $i=DIAS_PARA_COMIENZO; $i<DIAS_PARA_COMIENZO+DIAS_A_SORTEAR; $i++) {
	$fecha = $semanaSiguiente + ($i * 24 * 60 * 60); // Día que se está sorteando.
	$dia = date("j", $fecha);
	$mes = date("n", $fecha);
	
	echo "<p>Sorteo de Fecha=".date("d/m/Y", $fecha)."</p>";
	
	try {
		$db->query("START TRANSACTION");
		// Obtenemos cada franja en la que hay reservas con su aforo y el número de plazas reservadas.
		if($franjas = $db->get_results("SELECT reservas.dia, reservas.mes, reservas.franja, coalesce(total_reservas, 0) as reservas, aforo from (SELECT dia, mes, franja, sum(personas) as total_reservas FROM `reservas_solicitadas` GROUP BY dia, mes, franja) total RIGHT JOIN (SELECT dia, mes, franja, aforo FROM `franjas` WHERE dia = '$dia' and num_mes = $mes ) reservas ON reservas.dia = total.dia and reservas.mes = total.mes and reservas.franja = total.franja ORDER BY reservas.dia ASC, CASE WHEN reservas.mes = 'junio' then 1 WHEN reservas.mes = 'julio' then 2 WHEN reservas.mes = 'agosto' then 3 WHEN reservas.mes = 'septiembre' then 4 END ASC, reservas.franja DESC")) {
			foreach( $franjas as $franja ) {
				if ($franja->reservas <= $franja->aforo ) { // Menos plazas reservadas que aforo.
					echo "<p>Se aprueban todas las reservas del $dia - Franja $franja->franja </p>";
					aprobarReservas($dia, $mes, $franja->franja);
					// Enviar correo
				} else { // Más plazas reservadas que aforo. Se sortean las plazas.
					echo "<p>Se sortea la franja $franja->franja </p>";
					sortearReservas($dia, $mes, $franja->franja, $franja->aforo);
					// Enviar correo
				}
			}
		} else {
			echo '<p>Sin reservas</p>';
		}
		// Se marca el día como procesado
		$db->query("UPDATE `franjas` SET estado_franja = 1 WHERE dia = $dia AND num_mes = $mes");
		$db->query("COMMIT");
		enviarEmail($dia, $mes, rand(1, 100000));
	} catch (Exception $e) {
		echo '<p>Error: ';
		echo $e->getMessage();
		echo '<pre>';
		echo $db->debug();			
		echo '</pre>';
		echo '</p>';
		$db->query("ROLLBACK");
	}
}

?>