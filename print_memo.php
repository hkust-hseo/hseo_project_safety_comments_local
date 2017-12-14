<?php
  require "db_connect.php";
  require("fpdf.php");

  // Constants
  define("director", "Joseph Kwan");    // director of HSEO
  define("director_email", "sepopcip@ust.hk");  // Email of director of HSEO (for sending notification email)
  define("cbe_via", "Mrs. Pauline Leung");
  define("bien_via", "Miss Inez Tsui");

  class MemoPDF extends fpdf
  {
    function __construct($memo_no) {
      parent::__construct();
      $this -> SetMargins(25, 25);
			$this -> SetTitle($memo_no);			              //document title
			$this -> SetAuthor("HSEO HKUST");								//pdf author
			$this -> AddPage();
    }

    function printHeader() {
      $this -> SetFont('Times','B',15);		// Font style
			$this -> SetXY(45, 20);		// HKUST position
			$this -> Cell(130, 0, "The Hong Kong University of Science and Technology", 0, 1, 'C'); //HKUST Cell
      $this -> SetFont('Times');
			$this -> SetXY(55, 30);	// HSEO
			$this -> Cell(100, 0, "Health, Safety and Environment Office", 0, 1, 'C');
	 }

    function printDetails($ref_array, $ref_count, $prof_names, $dept) {
      $current_date = getdate();

      $to = "";

      for($i = 0; $i < count($prof_names) ; $i++) {
        $to .= $prof_names[$i];
        if(!empty($dept)) {
          $to .= ", " . $dept;
        }
        $to .= "\n";
      }
      if($dept == "CBME" || $dept == "CBE") {
        $to .= "(via ". cbe_via .")";
      }
      else if ($dept == "BIEN") {
        $to .= "(via ". bien_via .")";
      }

      $this -> SetFont('Times', '', 12);
      $this -> SetY(40);

      $this -> Cell(20, 10, "From: ");
      $this -> MultiCell(0, 10, "Director, HSEO", 0, 1);
      $this -> SetXY(100, 40);
      $this -> Cell(20, 10, "Tel. No.: ");
      $this -> MultiCell(0, 10, "Ext 6451", 0, 1);
      $this -> Cell(20, 10, "To: ");
      $this -> MultiCell(0, 10, $to, 0, 1); // TODO: prof name , dept
      $this -> Cell(20, 10, "Date: ");
      $this -> MultiCell(0, 10, $current_date['mday'] . " " . $current_date['month'] . " " . $current_date['year'], 0, 1);

      if(!empty($dept)) {
        $this -> Line(20, 75+((count($prof_names))*10), 190, 75+((count($prof_names))*10));
      }
      else {
        $this -> Line(20, 75+((count($prof_names)-1)*10), 190, 75+((count($prof_names)-1)*10));
      }
    }

    function printTitle($ref_array, $ref_count) {
      // Generate title
      $letter_title = "Project Safety Review - Ref: ";
      for($i = 0; $i < $ref_count-1; $i++) {
        $letter_title .= $ref_array[$i] . ", ";
      }
      $letter_title .= $ref_array[$ref_count-1];

      $this -> Ln(10);
      $this -> SetFont('Times', 'BU', 12);
      $this -> MultiCell(0, 10, $letter_title, 0, "L");
    }

    function printContent($ref_count) {
      $content = "Attached please find ";
      if($ref_count == 1) {
        $content .= "a review form ";
      }
      else {
        $content .= $ref_count . " review forms ";
      }
      $content .= "containing comments from HSEO relevant to hazards assessment and controls applicable to your research proposal. Please review these comments and take action to implement them prior to starting work. In the case you have further questions, please feel free to contact the responsible HSEO personnel or indicate your comments on the Department Response column of the review form and return to us.";
      $content .= "\n\n";
      $content .= "If we do not hear from you within a week, we will assume that the comments provided will be implemented.";

      $this -> SetFont('Times');
      $this -> MultiCell(0, 5, $content, 0, "J");
    }

    function printEnd() {
      $this -> SetY(200);
      $this -> MultiCell(0, 5, director . "\nDirector of Health, Safety and Environment\n\nJK/sh\n\nEnc", 0, "L");
    }
  }

  // Get varaibles (ref_no array)
  $ref_pass = $_POST["ref_array"];
  $ref_array = json_decode($ref_pass, true);
  $ref_count = 0;
  for($ref_count = 0; !empty($ref_array[$ref_count]); $ref_count++); // count number of ref_no passed into php

  // Get name of professors
  $get_details_query = "SELECT * FROM proj_details WHERE ref_no = '";
  $get_details_query .= $ref_array[0];
  $get_details_query .= "';";

  mysqli_real_query($db, $get_details_query);
  $result = mysqli_store_result($db);
  $details_row = mysqli_fetch_array($result);
  $prof_string = $details_row['supervisor'];
  $dept = $details_row['dept'];

  $prof_names = explode(",", $prof_string);

  // Generate memo_no
  $datetime = getdate();
  $memo_no = "memo" . $datetime['year'];    // yyyy
  if($datetime['mon'] < 10) {               // mm
    $memo_no .= "0" . $datetime['mon'];
  }
  else {
    $memo_no .= $datetime['mon'];
  }
  if($datetime['mday'] < 10) {              // dd
    $memo_no .= "0" . $datetime['mday'];
  }
  else {
    $memo_no .= $datetime['mday'];
  }

  $count_result = mysqli_query($db, "SELECT * from memo_details WHERE DATE(create_date) = CURDATE();");
  $memo_count = $count_result->num_rows;
  $memo_no .= $memo_count;        // memo_no format: yyyymmddn

  // Create memo
  $out_file = new MemoPDF($memo_no);

  $out_file -> printHeader();
  $out_file -> printDetails($ref_array, $ref_count, $prof_names, $dept);
  $out_file -> printTitle($ref_array, $ref_count);
  $out_file -> Ln(5);
  $out_file -> printContent($ref_count);
  $out_file -> printEnd();

  // Print memo
	$memo_link = "documents/memos/".$memo_no.".pdf";
	if(file_exists($memo_link)){
		unlink($memo_link);
	}
	$out_file -> Output('F', $memo_link);

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

  // Send email with send_mail.php
  $mode = "pending_memo";

  $receiver_email = director_email;
  include("send_mail.php");

  echo $memo_link;
?>
