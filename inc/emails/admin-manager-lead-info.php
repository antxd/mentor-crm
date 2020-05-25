<b>Hola <?php echo $manager_name; ?>, te adjuntmaos la información de: <?php echo $fullname; ?></b><br>
<p>Este correo y cualquier archivo transmitidos con él son confidenciales y previsto solamente para el uso del individuo o de la entidad a quienes se tratan<p>
<table style='border:initial;'>
  <tr>
    <td>Nombre:</td><td><?php echo $fullname; ?></td>
  </tr>
  <tr>
    <td>Email:</td><td><?php echo $lead_email; ?></td>
  </tr>
  <tr>
    <td>Teléfono:</td><td><?php echo $lead_phone; ?></td>
  </tr>
  <tr>
    <td>Ciudad y país:</td><td><?php echo $lead_location; ?></td>
  </tr>
  <tr>
    <td>Cirugía de Interes:</td><td><?php echo $lead_reason; ?></td>
  </tr>
  <tr>
    <td>Comentario:</td><td><?php echo $lead_comment; ?></td>
  </tr>
</table>