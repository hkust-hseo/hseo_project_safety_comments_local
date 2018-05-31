<?php
  require "db_connect.php";

  $memos = array();
  $memo_count = 0;

  // Get pending memo_no from proj_details
  $today = date("Y-m-d");
  $pending_memo_query = "SELECT * FROM proj_details WHERE due_date <= '$today' AND sent=0 GROUP BY memo;";

  mysqli_real_query($db, $pending_memo_query);
  $result = mysqli_store_result($db);
  while($row = mysqli_fetch_array($result)) {
    if($row['memo'] != "NULL" && !empty($row['memo']) && $row['memo'] != "0"){
      $memos[$memo_count] = $row['memo'];
      $memo_count++;
    }
  }

  if ($memo_count != 0) {
    $mode = "pending_memo_one_week";
    include "send_mail.php";
  }

  require "db_disconnect.php";
?>
