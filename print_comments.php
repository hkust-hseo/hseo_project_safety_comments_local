<?php
//	$ref_no = $_GET["ref_no"];
	/* print comments */
	require "db_connect.php";
	require("fpdf.php");

	/* Extra functions for table creation */
	class CommentPDF extends fpdf
	{
		function __construct($ref_no)
		{
			parent::__construct();
			$this -> SetTitle($ref_no." Review");			//document title
			$this -> SetAuthor("HSEO HKUST");								//pdf author
			$this -> AddPage();
		}

		function CreateHeader()
		{
			$this -> Image('img/sym_UST_black.jpg', 25, 15, 12);	// UST logo
			$this -> SetFont('Times','B',15);		// Font style
			$this -> SetXY(55, 20);		// HKUST position
			$this -> Cell(130, 0, "The Hong Kong University of Science and Technology", 0, 1, 'C'); //HKUST Cell
			$this -> SetXY(65, 30);	// HSEO
			$this -> Cell(100, 0, "Health, Safety and Environment Office", 0, 1, 'C');
			$this -> SetXY(80, 40); // Form
			$this -> Cell(60, 0, "Project Safety Review Form", 0, 1,'C');
		}

		function ProjectDetails($ref_no, $row)
		{
			$this -> SetY(50);
			// ref_no
			$this -> SetFont('Times', 'B', 12);
			$this -> Cell(40, 10, "Reference Number: ", 0, 0);
			$this -> SetFont('Times', 'U', 12);
			$this -> Cell(35, 10, $ref_no, 0, 1);

			// receive_date
			$this -> SetFont('Times', 'B', 12);
			$this -> Cell(62, 10, "Date Received from Department: ", 0, 0);
			$this -> SetFont('Times', 'U', 12);
			if (!empty($row["receive_date"]))
				$this -> MultiCell(35, 10, $row["receive_date"], 0, 1);
			else
				$this -> MultiCell(35, 10, "N/A", 0, 1);

			// due_date
			$this -> SetFont('Times', 'B', 12);
			$this -> Cell(62, 10, "Target Review Completion Date: ", 0, 0);
			$this -> SetFont('Times', 'U', 12);
			if (!empty($row["due_date"]))
				$this -> MultiCell(35, 10, $row["due_date"], 0, 1);
			else
				$this -> MultiCell(35, 10, "N/A", 0, 1);

			// dept
			$this -> SetFont('Times', 'B', 12);
			$this -> Cell(25, 10, "Department: ", 0, 0);
			$this -> SetFont('Times', 'U', 12);
			if (!empty($row["dept"]))
				$this -> MultiCell(20, 10, $row["dept"], 0, 1);
			else
				$this -> MultiCell(20, 10, "N/A", 0, 1);

			// room
			$this -> SetFont('Times', 'B', 12);
			$this -> Cell(73, 10, "Anticipated Location (Room Numbers): ", 0, 0);
			$this -> SetFont('Times', 'U', 12);
			if (!empty($row["room"]))
				$this -> MultiCell(60, 10, $row["room"], 0, 1);
			else
				$this -> MultiCell(30, 10, "N/A", 0, 1);

			// extn
			$this -> SetFont('Times', 'B', 12);
			$this -> Cell(21, 10, "Extension: ", 0, 0);
			$this -> SetFont('Times', 'U', 12);
			if (!empty($row["extn"]))
				$this -> MultiCell(0, 10, $row["extn"], 0, 1);
			else
				$this -> MultiCell(0, 10, "N/A", 0, 1);

			// researcher
			$this -> SetFont('Times', 'B', 12);
			$this -> Cell(40, 10, "Name of Researcher: ", 0, 0);
			$this -> SetFont('Times', 'U', 12);
			if (!empty($row["researcher"]))
				$this -> MultiCell(0, 10, $row["researcher"], 0, 1);
			else
				$this -> Cell(0, 10, "N/A", 0, 1);

			// supervisor
			$this -> SetFont('Times', 'B', 12);
			$this -> Cell(40, 10, "Name of Supervisor: ", 0, 0);
			$this -> SetFont('Times', 'U', 12);
			if (!empty($row["supervisor"]))
				$this -> MultiCell(0, 10, $row["supervisor"], 0, 1);
			else
				$this -> Cell(0, 10, "N/A", 0, 1);

			// proj_title
			$this -> SetFont('Times', 'B', 12);
			$this -> Cell(25, 10, "Project Title: ", 0, 0);
			$this -> SetFont('Times', 'U', 12);
			$this -> MultiCell(0, 10, $row["proj_title"], 0, 1);
		}

		function CalLines($width, $text)
		{
			$text = str_replace("\r", ' ', $text);
			$lines = 0;
			$char_ptr = 0;
			//$max_char = $width / 2.5;			// width of one courier char = 2.5mm
			$max_char = 42;
			$cont_char = 0;
			while ($char_ptr < strlen($text))
			{
				$word_length = 0;
				// new line detected
				if ($text[$char_ptr] == "\n") {
					$lines++;
					$cont_char = 0;
					 //echo "is new line at char_ptr = " . $char_ptr ."\n";
				}
				else if ($cont_char > $max_char){
					$lines++;
					$cont_char = 1;
					//echo "max_char new line at char_ptr = " . $char_ptr ."\n";
				}
				else{
					$cont_char++;
				}
				$char_ptr++;
				//echo "cont_char = ".$cont_char."\n";
			}
			// echo "lines = ".$lines."\n";

			return $lines+1;
		}

		function CommentRow($area, $comment, $pic)
		{
			// full text = comment with name of pic and Date
			$full_text = "";

			if (!empty($pic)){
				$full_text .= "Name" . "       " . "Signature" ."        ". "Date";
				$full_text .= "\n\n";
				$full_text .= $pic;
				$full_text .= "\n\n";
			}

			if (!empty($comment))
				$full_text .= $comment;

			// calculate height: in char, in mm, plus misc
			$height = $this -> CalLines(100, $full_text);		// width of comment section = 100 mm
			if ($height > 2){
				for($i=0; $i<=$height-2; $i++)
					$area .= "\n";
			}
			else {
				$height = 2;
			}
			$height = 5 * $height;
			//echo "height = ".$height."\n";

			$currentY = parent::GetY();
			$this -> MultiCell(40, 5, $area, 'B', 'C');
			$this -> SetXY(50, $currentY);
			if (!empty($full_text)){
				$this -> MultiCell(100, 5, $full_text, 'LBR', 'L');
				if(!empty($pic)) {
					$this -> Line(50, $currentY+15, 150, $currentY+15);
				}
			}
			else {
				$this -> MultiCell(100, 5, "--\n\n", 'LBR', 'C');
			}
			$this -> SetXY(150, $currentY);
			$this -> MultiCell(0, $height, " ", 'B');
		}

		function CommentTable($row)
		{
			// Table Header = first row
			$this -> SetFont('Times', 'B', '12');
			$this -> Cell(40, 12, "Area", 'B',  0, 'C');
			$this -> Cell(100, 12, "Comment", 'LBR', 0, 'C');
			$this -> Cell(0, 12, "Department Response", 'B', 1, 'C');

			// Table content: comments
			$this -> SetFont('Courier', '', '11');

			$this -> CommentRow("Occupational\nHygiene", $row["occ_hygiene"], $row["occ_hygiene_pic"]);
			$this -> CommentRow("Safety\nEngineering", $row["safety_eng"], $row["safety_eng_pic"]);
			$this -> CommentRow("Environmental\nProtection", $row["envr_protect"], $row["envr_protect_pic"]);
			$this -> CommentRow("Health\nPhysics", $row["health_phys"], $row["health_phys_pic"]);
			$this -> CommentRow("Peer\nReview", $row["peer_review"], $row["peer_review_pic"]);
			// Occupational Hygiene
	/*		$this -> SetX(50);
			$occ_hygiene_full = $row["occ_hygiene"];
			$occ_hygiene_full .= "\n\n";
			$occ_hygiene_full .= "By " . $row["occ_hygiene_pic"] . " on " . date("Y-m-d");
			$this -> MultiCell(100, 5, $occ_hygiene_full, 'LBR', 'L');
			*/
		}
	}

	// Data retrieval
	$ref_no = $_GET["ref_no"];
	// proj_details query
	$get_details_query = "SELECT * FROM proj_details WHERE ref_no = '";
  $get_details_query .= $ref_no;
  $get_details_query .= "';";

	$result_exist = mysqli_real_query($db, $get_details_query);
	$result = mysqli_store_result($db);
  $details_row = mysqli_fetch_array($result);

	// proj_comments query
	$get_comment_query = "SELECT * FROM proj_comments WHERE ref_no = '";
	$get_comment_query .= $ref_no;
	$get_comment_query .= "';";

	$result_exist = mysqli_real_query($db, $get_comment_query);
	$result = mysqli_store_result($db);
	$comment_row = mysqli_fetch_array($result);

	// Constructor
	$out_file = new CommentPDF($ref_no);

	// Header
	$out_file -> CreateHeader();

	// Print project info
	$out_file -> ProjectDetails($ref_no, $details_row);

	// Comments
	$out_file -> Ln();
	$out_file -> CommentTable($comment_row);

	//Output file to another tab
	$out_file -> Output('I', $ref_no."_review.pdf");

	// Store review file
	$review_link = "documents/reviews/".$ref_no."_review.pdf";
	if(file_exists($reivew_link)){
		unlink($review_link);
	}
	$out_file -> Output('F', $review_link);

	// Add link to database
	$update_query = "UPDATE proj_files SET review_link = '$review_link' WHERE ref_no = '$ref_no';";
	$set_complete = "UPDATE proj_details SET completed = 1 WHERE ref_no = '$ref_no';";

  mysqli_query($db, $update_query) or die("Adding review link to files failed. ");
	mysqli_query($db, $set_complete);
?>
