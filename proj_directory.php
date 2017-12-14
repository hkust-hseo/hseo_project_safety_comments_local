<?php
  require "db_connect.php";

  $completed = $_POST["completed"];
  $incomplete = $_POST["incomplete"];
  $sent = $_POST["sent"];
  $where_exist = true;
  $query = "";

  // Grab all search words
  $ref_no = $_POST["ref_no"];
  $supervisor = $_POST["supervisor"];
  $title = $_POST["title"];
  $room = $_POST["room"];
  $reviewer = $_POST["reviewer"];
  $not_first = false;

  $query_head = "SELECT * FROM proj_details ";
  $details_query = "";
  $reviewer_query = "";

  if(!empty($reviewer)) {
    $reviewer_query = "SELECT ref_no FROM proj_comments WHERE MATCH(occ_hygiene_pic,safety_eng_pic,envr_protect_pic,health_phys_pic,peer_review_pic) AGAINST ('$reviewer' IN BOOLEAN MODE)";
  }

  if(empty($ref_no) && empty($supervisor) && empty($title) && empty($room) && empty($reviewer)) {
    // All empty input --> Return all entries
    $where_exist = false;
  }
  else {
    if(!empty($ref_no)) {
      if($not_first) {
        $details_query .= "AND ";
      }
      $details_query .= "ref_no LIKE '%$ref_no%' ";
      $not_first = true;
    }
    if(!empty($supervisor)) {
      if($not_first) {
        $details_query .= "AND ";
      }
      $details_query .= "MATCH(supervisor) AGAINST('$supervisor' IN BOOLEAN MODE) ";
      $not_first = true;
    }
    if(!empty($title)) {
      if($not_first) {
        $details_query .= "AND ";
      }
      $details_query .= "MATCH(proj_title) AGAINST('$title' IN BOOLEAN MODE) ";
      $not_first = true;
    }
    if(!empty($room)) {
      if($not_first) {
        $details_query .= "AND ";
      }
      $details_query .= "MATCH(room) AGAINST('$room' IN BOOLEAN MODE) ";
      $not_first = true;
    }
  }

  if(!empty($details_query) && !empty($reviewer_query)) {
    $query = $query_head . "WHERE " . $details_query . "AND ref_no IN (" . $reviewer_query . ") ";
  }
  else if(!empty($details_query)) {
    $query = $query_head . "WHERE " . $details_query;
  }
  else if(!empty($reviewer_query)) {
    $query = $query_head . "WHERE ref_no IN (" . $reviewer_query . ") ";
  }
  else {
    $query = $query_head;
  }


  // TODO: Add order to results
  // project status check
   if($completed == 'true') {
     if($where_exist) {
       $query .= "AND ";
     }
     else {
       $query .= "WHERE ";
       $where_exist = true;
     }
     $query .= "completed = 1 ";
   }
   else if ($incomplete == 'true'){
       if($where_exist) {
         $query .= "AND ";
       }
       else {
         $query .= "WHERE ";
         $where_exist = true;
       }
       $query .= "completed = 0 ";
   }
  if($sent == 'true') {
    if($where_exist) {
      $query .= "AND ";
    }
    else {
      $query .= "WHERE ";
    }
    $query .= "AND sent = 1 ";
  }
  $query .= ";";

  //echo $query;

  // Execute query
  $data_count = 0;
  $results = array();
  mysqli_real_query($db, $query);

  // Obtain results
  if($result = mysqli_store_result($db)) {
    while($row = mysqli_fetch_array($result)) {
      $data = array($row['ref_no'], $row['proj_title'], $row['supervisor'], $row['room'], $row['completed'], $row['memo'], $row['sent']);
      $results[$data_count] = $data;

      $data_count++;
    }
  }

  echo json_encode($results);
?>
