<?php
  require "db_connect.php";

  // Get memo details
  $memo_no = $_POST['memo_no'];
  $memo_link = "documents/memos/".$memo_no.".pdf";
  $ref_pass = $_POST["ref_array"];
  $ref_array = json_decode($ref_pass, true);
  $ref_count = 0;
  for($ref_count = 0; !empty($ref_array[$ref_count]); $ref_count++);

  // Add new memo to memo_details
  $add_memo = "INSERT INTO memo_details (memo_no, create_date, file_link) ";
  $add_memo .= "VALUES ('$memo_no', CURDATE(), '$memo_link');";
  mysqli_real_query($db, $add_memo) or die("memo insertion failed".mysqli_error($db));

  // Update each ref_no with memo_no
  for($i = 0; $i < $ref_count; $i++) {
    $update_memo = "UPDATE proj_details SET memo = '$memo_no' ";
    $update_memo .= "WHERE ref_no = '". $ref_array[$i]. "';";
    mysqli_real_query($db, $update_memo);
  }
?>
