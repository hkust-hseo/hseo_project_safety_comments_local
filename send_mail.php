<?php
  require "ref/PHPMailer-master/PHPMailerAutoload.php";

  define("from_address", "srapproval@ust.hk");
  define("to_address", $receiver_email);
  define("body_ending", "Yours,<br/>System Admin");

  $mail = new PHPMailer;

  $mail->IsSMTP();
  $mail->Host = "smtp.ust.hk";
  $mail->Port = 587;
  $mail->SMTPAuth = true;
  $mail->Username = "srapproval@ust.hk";
  $mail->Password = "srhseosr";

  $mail->setFrom(from_address, "System Admin");
  $mail->addAddress(to_address);

  $mail->isHTML(true);

  // Send to HSEO Director about pending memos
  if($mode == "pending_memo") {
    // variables
    $memo_url = "143.89.195.131/hseo_project_safety_comments/pending_memo.php";    // URL of pending memo page

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
    $files = array();

    // SQL to fetch all related file links
    //  $fetch_memo_file = "SELECT file_link FROM memo_details WHERE memo_no = '$memo_no';";
    //  $fetch_proj_files = "SELECT review_link FROM proj_files WHERE ref_no IN (SELECT ref_no FROM proj_details WHERE memo = '$memo_no');";

    $mail->Subject = "Send memo to prof test";

    $mail->Body = "Dear Prof.,\n\nAttached please find some files";

    // TODO: Add attachments
    // $mail->addAttachment("documents/reviews/17035_review.pdf", "test_file_name");
  }

  // Send email
  if(!$mail -> send()) {
    echo "Email not send<br/>";
    echo "Mailer Error: " . $mail->ErrorInfo;
  }

?>
