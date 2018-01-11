<?php

  include "db_connect.php";

/* Form handling */
  $ref_no = $_POST["ref_no"];
  $receive_date = $_POST["receive_date"];
  $due_date = $_POST["due_date"];
  $proj_title = $_POST["proj_title"];
  $dept = $_POST["dept"];
  $room = $_POST["room"];
  $researcher = $_POST["researcher"];
  $supervisor = $_POST["supervisor"];
  $contact = $_POST["contact"];
  $extn = $_POST["extn"];

  // injection prevention
  $ref_no = mysqli_real_escape_string($db, $ref_no);
  $receive_date = mysqli_real_escape_string($db, $receive_date);
  $due_date = mysqli_real_escape_string($db, $due_date);
  $proj_title = mysqli_real_escape_string($db, $proj_title);
  $dept = mysqli_real_escape_string($db, $dept);
  $room = mysqli_real_escape_string($db, $room);
  $researcher = mysqli_real_escape_string($db, $researcher);
  $supervisor = mysqli_real_escape_string($db, $supervisor);
  $contact = mysqli_real_escape_string($db, $contact);
  $extn = mysqli_real_escape_string($db, $extn);

  // start of query declration
  $insert_query = "INSERT INTO proj_details (ref_no, proj_title";   // first half of query (INSERT)
  $values_query = "VALUES ('$ref_no', '$proj_title'";   // second half of query (VALUES)

  if(!empty($receive_date)){  // if receive_date exists
    $insert_query .= ", receive_date";
    $values_query .= ", '$receive_date'";
  }
  if(!empty($due_date)){    // if due_date exists
    $insert_query .= ", due_date";
    $values_query .= ", '$due_date'";
  }
  if(!empty($dept)){  // if dept exists
    $insert_query .= ", dept";
    $values_query .= ", '$dept'";
  }
  if(!empty($room)){  // if room exists
    $insert_query .= ", room";
    $values_query .= ", '$room'";
  }
  if(!empty($researcher)){  // if researcher exists
    $insert_query .= ", researcher";
    $values_query .= ", '$researcher'";
  }
  if(!empty($supervisor)){  // if supervisor exists
    $insert_query .= ", supervisor";
    $values_query .= ", '$supervisor'";
  }
  if(!empty($contact)){  // if supervisor exists
    $insert_query .= ", contact";
    $values_query .= ", '$contact'";
  }
  if(!empty($extn)){  // if extn exists
    $insert_query .= ", extn";
    $values_query .= ", $extn";
  }

  $insert_query .= ") ";    // end of insert part of query
  $values_query .= ");";    // end of whole query

  $details_query = $insert_query . $values_query;   // concat to form full query

  mysqli_query($db, $details_query) or die("Details Query Failed. ");

  $file_size = $_FILES['proj_file']['size'];
  if($file_size > 0) {
    $proposal_link = "documents/proposals/".$ref_no.".pdf";
    if (move_uploaded_file($_FILES["proj_file"]["tmp_name"], $proposal_link)) {
      echo "<p>FILE UPLOADED TO: $proposal_link</p>";
    } else {
      echo "<P>MOVE UPLOADED FILE FAILED!</P>";
      print_r(error_get_last());
    }

    $file_query = "INSERT INTO proj_files (ref_no, file_size, proposal_link)".
                  "VALUES ('$ref_no', $file_size, '$proposal_link')";
    mysqli_query($db, $file_query) or die("File Query Failed. ");
  }

  echo '<html><head><link rel = "stylesheet" type = "text/css" href = "css/universal.css"><link rel = "stylesheet" type = "text/css" href = "../css/new_proj.css"><header>New Project<a href = "index.html"><img src = "img/hkust_logo_white.png"/></a></header></head><body>';
  echo '<a style = "position: absolute; top: 120px; left: 50px;" href = "proj_comment.php?ref_no='.$ref_no.'" id = "next_button">Add Comments</a>';
  echo '</body></html>';

?>
