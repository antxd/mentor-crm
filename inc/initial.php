<?php
$steps = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}mentor_steps ORDER BY order_val,SID ASC");
$sid = $steps[0]->SID;
$tag_color = $steps[0]->color;
$tag_title = $steps[0]->title;
if (!empty($_GET['sid'])) {
  foreach ($steps as $key => $step) {
      if ($step->SID == $_GET['sid']) {
        $sid = $step->SID;
        $tag_color = $step->color;
        $tag_title = $step->title;
        break;
      }
  }
  if ($_GET['sid'] === 'all') {
      $sid = 'all';
      $tag_color = '#8f8f8f';
      $tag_title = 'INACTIVOS';
  }
}
?>
  <div class="wrap mentor-crm-wrap">
      <?php include 'crm-header.php'; ?>
      <div class="mentor-crm-box">
          <div class="mentor-crm-tags-filter">
              <ul class="mentor-crm-tags-filter-list">
                <?php foreach ($steps as $key => $step) {?>
                <li>
                    <a href="<?php echo admin_url('admin.php?page=mentor-crm-admin').'&sid='.$step->SID; ?>" class="mentor-crm-tag-link
                      <?php echo ($sid === $step->SID)?'mentor-crm-tag-link-active':''; ?>">
                        <span class="mentor-crm-tag-count" style="background-color: <?php echo $step->color; ?>"><?php echo $wpdb->get_var( "SELECT COUNT(LID) FROM {$wpdb->prefix}mentor_leads WHERE step_ID={$step->SID}" ); ?></span>
                        <span class="mentor-crm-tag-title" style="background-color: <?php echo $step->color; ?>"><?php echo $step->title; ?></span>
                    </a>
                </li>
                <?php } ?>
                <li>
                    <a href="<?php echo admin_url('admin.php?page=mentor-crm-admin').'&sid=all'; ?>" class="mentor-crm-tag-link
                      <?php echo ($sid === 'all')?'mentor-crm-tag-link-active':''; ?>">
                        <span class="mentor-crm-tag-count" style="background-color: #8f8f8f; ?>;"><?php echo $wpdb->get_var( "SELECT COUNT(LID) FROM {$wpdb->prefix}mentor_leads WHERE state=2" ); ?></span>
                        <span class="mentor-crm-tag-title" style="background-color: #8f8f8f;">INACTIVOS</span>
                    </a>
                </li>
              </ul>
          </div>
          <div class="mentor-crm-box-tag-big-title" style="background-color: <?php echo $tag_color; ?>">
            <?php echo $tag_title; ?>
          </div>
          <div class="mentor-crm-box-leads">
              <table class="mentor-table-basic">
                      <tr>
                        <th><?php echo $lead_name; ?></th>
                        <th><?php echo $reason_label; ?></th>
                        <th><?php echo $booking_date_label; ?></th>
                        <th><?php echo $booking_time_label; ?></th>
                        <th><?php echo $confirm_date_label; ?></th>
                        <th><?php echo $manage_label; ?></th>
                        <th><?php echo $payment_label; ?></th>
                        <th class="text-center">VER/EDITAR</th>
                      </tr>
                      <?php
                        $limit = 15;  
                        if (isset($_GET["crm-page"])) { $page = $_GET["crm-page"]; } else { $page=1; };  
                        $start_from = ($page-1) * $limit;
                        $leads = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}mentor_leads WHERE step_ID={$sid} ORDER BY LID DESC LIMIT $start_from, $limit" );
                        if ($sid == 'all') {
                          $leads = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}mentor_leads WHERE state=2 ORDER BY LID DESC LIMIT $start_from, $limit" );
                        }
                        foreach ($leads as $key => $lead) {
                            echo '<tr>
                                    <td>'.$lead->fullname.'</td>
                                    <td>'.$lead->reason.'</td>
                                    <td>'.date('d/m/Y',strtotime($lead->date)).'</td>
                                    <td>'.date('H:i A',strtotime($lead->time)).'</td>
                                    <td>'.(($lead->confirm_date == 0)?'NO':'SI').'</td>
                                    <td>'.$managers[$lead->manage].'</td>
                                    <td><span class="payment_state_label state_'.$lead->payment_state.'" '.(($lead->payment_state == 1)?'style="background-color:'.$tag_color.'"':'').'>'.$payment_state_text[$lead->payment_state].'</span>
                                    </td>
                                    <td class="text-center">
                                      <a href="'.admin_url('admin.php?page=mentor-crm-admin').'&lid='.$lead->LID.'" class="mentor-crm-btn-edit" style="background-color:'.$tag_color.'">
                                          <span class="dashicons dashicons-edit"></span>
                                      </a>
                                    </td>
                                  </tr>';
                        }
                      ?>
              </table>
              <ul class="mentor-crm-pagination">
                <?php 
                $total_records = $wpdb->get_var( "SELECT COUNT(LID) FROM {$wpdb->prefix}mentor_leads WHERE step_ID={$sid}" );
                $total_pages = ceil($total_records / $limit);
                if ($page > 1) {
                  echo '<li class="crm-page-item">
                        <a class="crm-page-link page-prev-crm" href="'.admin_url('admin.php?page=mentor-crm-admin').'&sid='.$sid.'&crm-page='.($page-1).'"><span class="dashicons dashicons-arrow-left-alt2"></span></a>
                      </li>'; 
                }
                for ($i=1; $i<=$total_pages; $i++) {
                    $active = '';
                    if ($i == $page) {
                        $active = 'style="background-color:'.$tag_color.';color:#fff;"';
                    }
                    $distance = $page - $i;
                    //mostrar solo 5 elementos y se va moviendo de 5 en 5 en la paginación
                    if (abs($distance) < 5){
                       echo '<li class="crm-page-item">
                                <a class="crm-page-link" href="'.admin_url('admin.php?page=mentor-crm-admin').'&sid='.$sid.'&crm-page='.($i).'" '.$active.'>'.$i.'</a>
                            </li>'; 

                    } 
                } 
                if ($page<($total_pages-1)) {
                  echo '<li class="crm-page-item">
                        <a class="crm-page-link page-next-crm" href="'.admin_url('admin.php?page=mentor-crm-admin').'&sid='.$sid.'&crm-page='.($page+1).'"><span class="dashicons dashicons-arrow-right-alt2"></span></a>
                      </li>'; 
                }
                ?>
              </ul>
          </div>
      </div>
</div>