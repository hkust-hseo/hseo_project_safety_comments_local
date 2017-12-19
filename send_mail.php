<?php
  require "ref/PHPMailer-master/PHPMailerAutoload.php";

  // email content definitions
  define("from_address", "srapproval@ust.hk");
  define("body_ending", "Yours,<br/>System Admin");

  // contact definitions
  define("director_email", "sepopcip@ust.hk");
  define("cbe_email", "sepopcip@ust.hk");
  define("bien_email", "sepopcip@ust.hk");

  // TODO: remove this by finding a way to GET from print_memo.php
  if(!isset($mode)){
    $mode = $_GET['mode'];
  }

  function initMail($mail, $receiver_email) {
    $mail->IsSMTP();
    $mail->Host = "smtp.ust.hk";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = "srapproval@ust.hk";
    $mail->Password = "srhseosr";

    $mail->setFrom(from_address, "System Admin");
    $mail->addAddress($receiver_email);

    $mail->isHTML(true);
  }

  // Send to HSEO Director about pending memos
  if($mode == "pending_memo") {
    // Create mail container and header
    $mail = new PHPMailer;
    initMail($mail, director_email);

    // variables
    $memo_url = "143.89.148.116/hseo_project_safety_comments_local/pending_memo.php";    // URL of pending memo page

    $mail->Subject = "Pending Memos";

    // HTML mail body
    $mail->Body = "Dear Sir/Madam,<br/><br/>";  // Email content
    $mail->Body .= "The following ";
    if($ref_count <= 1) {
      $mail->Body .= "workplan is ";
    }
    else {
      $mail->Body .= "workplans are ";
    }
    $mail->Body .= "pending for your approval.<br/>";
    for($i = 0; $i < $ref_count; $i++) {
      $mail->Body .= $ref_array[$i]."<br/>";
    }

    $mail->Body .= "<br/>Please head to:<br/>";
    $mail->Body .= "<a href='".$memo_url."'>".$memo_url."</a>";   // HTML mail version (link)
    $mail->Body .= "<br/>for further actions.<br/><br/>";

    $mail->Body .= body_ending;

    // Alternative body in plain text (in case HTML mail not supported)
    $mail->AltBody = "Dear Sir/Madam,\n\n";  // Email content
    $mail->AltBody .= "The following ";
    if($ref_count <= 1) {
      $mail->AltBody .= "workplan is ";
  }
    else {
      $mail->AltBody .= "workplans are ";
    }
    $mail->AltBody .= "pending for your approval.\n";
    for($i = 0; $i < $ref_count; $i++) {
      $mail->AltBody .= $ref_array[$i]."\n";
    }

    $mail->AltBody .= "\nPlease head to:\n";
    $mail->AltBody .= $memo_url;        // Plain text version: non-clickable
    $mail->AltBody .= "\nfor further actions.\n\n";
    $alt_ending = str_replace("<br/>","\n", body_ending);
    $mail->AltBody .= $alt_ending;
  }

  if($mode == "send_memo") {
    $mail = new PHPMailer;
    $memo_no = $_GET['memo_no'];

    require("db_connect.php");

    // Get department to send email to
    $identify_dept_query = "SELECT dept FROM proj_details WHERE memo = '$memo_no' LIMIT 1;";
    if (mysqli_real_query($db, $identify_dept_query)) {
      $result = mysqli_store_result($db);
      $row = mysqli_fetch_row($result);
      $dept = $row[0];
    } else {
      echo "Error accessing database. Error code: " . $mysqli->error;
    }

    // Put in corresponding receiver details
    //  if ($dept == "CBE") {}
    // TODO: sub out own test email
    initMail($mail, cbe_email);


    // SQL to fetch all related file links
    // memo, individual comment form
    $fetch_memo_file_query = "SELECT file_link, memo_no FROM memo_details WHERE memo_no = '$memo_no';";
    $fetch_proj_files_query = "SELECT review_link, ref_no FROM proj_files WHERE ref_no IN (SELECT ref_no FROM proj_details WHERE memo = '$memo_no');";

    $mail->Subject = "Review Completed: ". $memo_no;

    $files_count = 0;
    $files = array();

    // fetch and attach memo file
    if(mysqli_real_query($db, $fetch_memo_file_query)) {
      $result = mysqli_store_result($db);
      $row = mysqli_fetch_row($result);
      $files[$files_count]['path'] = $row[0];
      $files[$files_count]['name'] = $row[1];
      $files_count++;
  }

    // fetch and attach the list of related comment forms
    mysqli_real_query($db, $fetch_proj_files_query);
    // Obtain results
    if($result = mysqli_store_result($db)) {
      while($row = mysqli_fetch_row($result)) {
        $files[$files_count]['path'] = $row[0];
        $files[$files_count]['name'] = $row[1];
        $files_count++;
      }
    }

    // HTML email body
    $mail->Body = "Dear Sir/Madam,<br/><br/>";  // Email content
    $mail->Body .= "The project safety review for the following ";
    if($files_count-1 <= 1) {
      $mail->Body .= "project is ";
    }
    else {
      $mail->Body .= "projects are ";
    }
    $mail->Body .= "completed.<br/>";
    for($i = 1; $i < $files_count; $i++) {
      $mail->Body .= $files[$i]['name']."<br/>";
    }

    $mail->Body .= "<br/>Attached please find the corresponding review forms.<br/>";
    $mail->Body .= "Please forward them to the parties concerned.<br/><br/>";
    $mail->Body .= body_ending;

    // Plain text email body
    // Does this work?
    $mail->AltBody = str_replace("<br/>", "\n", $mail->Body);

    for($i = 0; $i < $files_count; $i++) {
      $mail->addAttachment($files[$i]['path'], $files[$i]['name']);
    }
  }

  // Send email
  if(!$mail -> send()) {
    echo "Email not send<br/>";
    echo "Mailer Error: " . $mail->ErrorInfo;
  }
?>
