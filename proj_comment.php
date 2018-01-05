<?php
include "db_connect.php";?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel = "stylesheet" type = "text/css" href = "css/universal.css">
<link rel = "stylesheet" type = "text/css" href = "css/proj_comment.css">

<title>Project Review</title>

<script src="jquery-3.2.1.js"></script>
<script>
	function saveComments(ref) {
		// To get form elements and pass to save_comments.php (to be stored in dbms)
		// Triggered by clicking "Save Comments" Button

		// Array of input values
		var input_values = new Array();

		// Get all form elements (#comment_form)
		for(var i = 0; i < 10; i++) {
			// array index corresponds to physical position on webpage (i.e: [0]=occ_hygiene; [1]=occ_hygiene_pic, etc)
			input_values[i] = document.getElementsByClassName("comment_form_elements")[i].value;			// Get the value of form elements
			console.log("i = " + i + ": value is " + input_values[i]);
		}

		var save_request;		// ajax object used to connect to save_comments.php

		if(save_request) {save_request.abort(); }

		save_request = $.ajax({
	      url: "save_comments.php",		// php link = save_comments.php
	      type: "post",
	      data: {
					ref_no: ref,										// ref_no as parameter passed into js earlier (ref)
	        occ_hygiene: input_values[0],		// occ_hygiene corresponses to first element (index 0) of comment_form
					occ_hygiene_pic: input_values[1],		// u.s.
					safety_eng: input_values[2],
					safety_eng_pic: input_values[3],
					envr_protect: input_values[4],
					envr_protect_pic: input_values[5],
					health_phys: input_values[6],
					health_phys_pic: input_values[7],
					peer_review: input_values[8],
					peer_review_pic: input_values[9]
	      }
	  });

		save_request.done(function(response, status, jqXHR) {
			console.log("done parameters: " + response + " " + status + " " + jqXHR);
			alert("Comments saved.");
		});

		save_request.fail(function(jqXHR, status, error){
			console.log("fail parameters: " + jqXHR + " " + status + " " + error);
		});
	}

</script>

</head>

<body>
<header>
	Project Review
  <a href = "index.html"><img src = "img/hkust_logo_white.png"/></a>
</header>

<?php
  $ref_no = $_GET["ref_no"];
	$get_file_query = "SELECT * FROM proj_files WHERE ref_no = '";
	$get_file_query .= $ref_no;
	$get_file_query .= "';";

	if(mysqli_real_query($db, $get_file_query)){
		$result = mysqli_store_result($db);
		$row = mysqli_fetch_array($result);
	  echo "<iframe src = \"".$row['proposal_link']."\"></iframe>";
	}
?>

<?php
	$get_comment_query = "SELECT * FROM proj_comments WHERE ref_no = '";
	$get_comment_query .= $ref_no;
	$get_comment_query .= "';";

	$result_exist = mysqli_real_query($db, $get_comment_query);
	$result = mysqli_store_result($db);
	$row = mysqli_fetch_array($result);
?>

<div id = "comments" >
	Occupational Hygiene:<br/>
	<textarea name = "occ_hygiene" form = "comment_form" class="comment_form_elements"><?php
		if(!empty($row['occ_hygiene'])) {	// exist result from query
			// retrieve existing comment as prefilled text in textarea
			echo $row['occ_hygiene'];
		}
	 ?></textarea>
	<br/>
	Name: <input type = "text" name = "occ_hygiene_pic" form = "comment_form" class = "comment_form_elements" value = "<?php echo $row["occ_hygiene_pic"]; ?>">
	<br/>

	Safety Engineering:<br/>
	<textarea name = "safety_eng" form = "comment_form" class = "comment_form_elements"><?php
		if(!empty($row['safety_eng'])) {	// exist result from query
			// retrieve existing comment as prefilled text in textarea
			echo $row['safety_eng'];
		}
	 ?></textarea>
	<br/>
	Name: <input type = "text" name = "safety_eng_pic" form = "comment_form" class = "comment_form_elements" value = "<?php echo $row["safety_eng_pic"]; ?>">
	<br/>

	Environmental Protection:<br/>
	<textarea name = "envr_protect" form = "comment_form" class = "comment_form_elements"><?php
		if(!empty($row['envr_protect'])) {	// exist result from query
			// retrieve existing comment as prefilled text in textarea
			echo $row['envr_protect'];
		}
	 ?></textarea>
	<br/>
	Name: <input type = "text" name = "envr_protect_pic" form = "comment_form" class = "comment_form_elements" value = "<?php echo $row["envr_protect_pic"]; ?>">
	<br/>

	Health Physics: <br/>
	<textarea name = "health_phys" form = "comment_form" class = "comment_form_elements"><?php
		if(!empty($row['health_phys'])) {	// exist result from query
			// retrieve existing comment as prefilled text in textarea
			echo $row['health_phys'];
		}
	 ?></textarea>
	<br/>
	Name: <input type = "text" name = "health_phys_pic" form = "comment_form" class = "comment_form_elements" value = "<?php echo $row["health_phys_pic"]; ?>">
	<br/>

	Peer Review:<br/>
	<textarea name = "peer_review" form = "comment_form" class = "comment_form_elements"><?php
		if(!empty($row['peer_review'])) {	// exist result from query
			// retrieve existing comment as prefilled text in textarea
			echo $row['peer_review'];
		}
	 ?></textarea>
	 <br/>
 	Name: <input type = "text" name = "peer_review_pic" form = "comment_form" class = "comment_form_elements" value = "<?php echo $row["peer_review_pic"]; ?>">
 	<br/><br/>
	<form action = "" method = "post" id = "comment_form">
	<!--use CSS to put submit at the bottom-->
		<input type = "button" value = "Save comments" name = "save_comments" id = "save_comments" onclick="saveComments('<?php echo $ref_no; ?>')">
	</form>
	<form action = "print_comments.php" method = "get" target = "_blank">
		<button type = "submit" value = <?php echo $ref_no; ?> name = "ref_no" id = "complete_review">Complete Review</button>
	</form>

</div>
</body>
</html>
