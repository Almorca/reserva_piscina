# reserva_piscina
  Cada franja a reservar tiene asignado un aforo. Los usuarios pueden solicitar una franja con la antelación que deseen y en el caso de que haya más reservas que aforo para una franja dada esta se sortea. El sorteo se realiza de tal manera que los usuarios que van consiguiendo reservas tienen menos probabilidad de obtener una nueva reserva.
  
# Instalación
  - Copiar los ficheros en un servidor con php y sendmail.
  - Dar de alta los pisos y las franjas en las que se pueden realizar reservas.
  - Modificar los datos del fichero config.php y los datos de email del fichero sorteo.php
  - Planificar el fichero sorteo.php mediante cron para realice las reservas con la periodicidad deseada. La parametría que trae por defecto es para que cada día confirme las reservas de ese día pero cambiando DIAS_A_SORTEAR y DIAS_PARA_COMIENZO se puede configurar de cualquier manera (por ejemplo que el domingo se realicen las reservas de toda la semana siguiente poniendo DIAS_A_SORTEAR=7 y DIAS_PARA_COMIENZO=1).
