<?php
  require("db_connect.php");

  $memo_no = $_POST['memo_no'];

  $update_sent_query = "UPDATE proj_details SET sent = 1 WHERE memo = '$memo_no';";
  mysqli_real_query($db, $update_sent_query);

?>
