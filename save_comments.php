<?php
  include "db_connect.php";

  $occ_hygiene = $_POST["occ_hygiene"];
  $safety_eng = $_POST["safety_eng"];
  $envr_protect = $_POST["envr_protect"];
  $health_phys = $_POST["health_phys"];
  $peer_review = $_POST["peer_review"];
	$occ_hygiene_pic = $_POST["occ_hygiene_pic"];
  $safety_eng_pic = $_POST["safety_eng_pic"];
	$envr_protect_pic = $_POST["envr_protect_pic"];
	$health_phys_pic = $_POST["health_phys_pic"];
	$peer_review_pic = $_POST["peer_review_pic"];

	$ref_no = $_POST["ref_no"];

  // escape special characters
  $occ_hygiene = mysqli_real_escape_string($db, $occ_hygiene);
  $safety_eng = mysqli_real_escape_string($db, $safety_eng);
  $envr_protect = mysqli_real_escape_string($db, $envr_protect);
  $health_phys = mysqli_real_escape_string($db, $health_phys);
  $peer_review = mysqli_real_escape_string($db, $peer_review);
	$occ_hygiene_pic = mysqli_real_escape_string($db, $occ_hygiene_pic);
	$safety_eng_pic = mysqli_real_escape_string($db, $safety_eng_pic);
	$envr_protect_pic = mysqli_real_escape_string($db, $envr_protect_pic);
	$health_phys_pic = mysqli_real_escape_string($db, $health_phys_pic);
	$peer_review_pic = mysqli_real_escape_string($db, $peer_review_pic);

	/* comment query declaration */
	// INSERT INTO
	$insert_query = "INSERT INTO proj_comments (ref_no";
	$values_query = "VALUES ('$ref_no'";
	$update_query = "ON DUPLICATE KEY UPDATE ";

	// conditional add comments
	if(!empty($occ_hygiene)){
		$insert_query .= ", occ_hygiene";
		$values_query .= ", '$occ_hygiene'";
	}
	if(!empty($occ_hygiene)){
		$update_query .= "occ_hygiene = '$occ_hygiene'";
	}
	else {
		$update_query .= "occ_hygiene = NULL";
	}

	if(!empty($safety_eng)){
		$insert_query .= ", safety_eng";
		$values_query .= ", '$safety_eng'";
	}
	$update_query .= ", ";
	if(!empty($safety_eng)){
		$update_query .= "safety_eng = '$safety_eng'";
	}
	else {
		$update_query .= "safety_eng = NULL";
	}

	if(!empty($envr_protect)){
		$insert_query .= ", envr_protect";
		$values_query .= ", '$envr_protect'";
	}
	$update_query .= ", ";
	if(!empty($envr_protect)){
		$update_query .= "envr_protect = '$envr_protect'";
	}
	else {
		$update_query .= "envr_protect = NULL";
	}

	if(!empty($health_phys)){
		$insert_query .= ", health_phys";
		$values_query .= ", '$health_phys'";
	}
	$update_query .= ", ";
	if(!empty($health_phys)){
		$update_query .= "health_phys = '$health_phys'";
	}
	else {
		$update_query .= "health_phys = NULL";
	}

	if(!empty($peer_review)){
		$insert_query .= ", peer_review";
		$values_query .= ", '$peer_review'";
	}
	$update_query .= ", ";
	if(!empty($peer_review)){
		$update_query .= "peer_review = '$peer_review'";
	}
	else {
		$update_query .= "peer_review = NULL";
	}

	if(!empty($occ_hygiene_pic)){
		$insert_query .= ", occ_hygiene_pic";
		$values_query .= ", '$occ_hygiene_pic'";
	}
	$update_query .= ", ";
	if(!empty($occ_hygiene_pic)){
		$update_query .= "occ_hygiene_pic = '$occ_hygiene_pic'";
	}
	else {
		$update_query .= "occ_hygiene_pic = NULL";
	}

	if(!empty($safety_eng_pic)){
		$insert_query .= ", safety_eng_pic";
		$values_query .= ", '$safety_eng_pic'";
	}
	$update_query .= ", ";
	if(!empty($safety_eng_pic)){
		$update_query .= "safety_eng_pic = '$safety_eng_pic'";
	}
	else {
		$update_query .= "safety_eng_pic = NULL";
	}

	if(!empty($envr_protect_pic)){
		$insert_query .= ", envr_protect_pic";
		$values_query .= ", '$envr_protect_pic'";
	}
	$update_query .= ", ";
	if(!empty($envr_protect_pic)){
		$update_query .= "envr_protect_pic = '$envr_protect_pic'";
	}
	else {
		$update_query .= "envr_protect_pic = NULL";
	}

	if(!empty($health_phys_pic)){
		$insert_query .= ", health_phys_pic";
		$values_query .= ", '$health_phys_pic'";
	}
	$update_query .= ", ";
	if(!empty($health_phys_pic)){
		$update_query .= "health_phys_pic = '$health_phys_pic'";
	}
	else {
		$update_query .= "health_phys_pic = NULL";
	}

	if(!empty($peer_review_pic)){
		$insert_query .= ", peer_review_pic";
		$values_query .= ", '$peer_review_pic'";
	}
	$update_query .= ", ";
	if(!empty($peer_review_pic)){
		$update_query .= "peer_review_pic = '$peer_review_pic'";
	}
	else {
		$update_query .= "peer_review_pic = NULL";
	}

	$insert_query .= ") ";
	$values_query .= ") ";
	$update_query .= ";";
	$comment_query = $insert_query . $values_query . $update_query;

  mysqli_query($db, $comment_query) or die("Comment query failed\n".mysqli_error($db));
?>
