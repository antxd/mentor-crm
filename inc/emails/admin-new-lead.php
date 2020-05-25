<b>Un nuevo lead se ha registrado a las: <?php echo $register_date; ?></b>
<table style='border:initial;'>
  <tr>
    <td>Nombre:</td><td><?php echo $fullname; ?></td>
  </tr>
  <tr>
    <td>Email:</td><td><?php echo $email; ?></td>
  </tr>
  <tr>
    <td>Teléfono:</td><td><?php echo $phone; ?></td>
  </tr>
  <tr>
    <td>País:</td><td><?php echo $crmcountries[$country]; ?></td>
  </tr>
  <tr>
    <td>Ciudad:</td><td><?php echo $city; ?></td>
  </tr>
  <tr>
    <td>Cirugía de Interes:</td><td><?php echo $reason; ?></td>
  </tr>
  <tr>
    <td>Fecha de Interes:</td><td><?php echo $date; ?></td>
  </tr>
  <tr>
    <td>Comentario:</td><td><?php echo $comments; ?></td>
  </tr>
  <tr>
    <td>Típo de Consulta:</td><td><?php echo $tipo_cita; ?></td>
  </tr>
</table>