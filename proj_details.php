<?php
  require "db_connect.php";

  // get details from database
  $ref_no = $_GET["ref_no"];
	$get_details_query = "SELECT * FROM proj_details WHERE ref_no = '";
	$get_details_query .= $ref_no;
	$get_details_query .= "';";

  mysqli_real_query($db, $get_details_query);
	$details_result = mysqli_store_result($db);
	$details_row = mysqli_fetch_array($details_result);

  // get files from database
	$get_files_query = "SELECT * FROM proj_files WHERE ref_no = '";
	$get_files_query .= $ref_no;
	$get_files_query .= "';";

  mysqli_real_query($db, $get_files_query);
	$files_result = mysqli_store_result($db);
	$files_row = mysqli_fetch_array($files_result);
?>

<html>
<head>
<meta charset="utf-8">
<link rel = "stylesheet" type = "text/css" href = "css/universal.css">
<link rel = "stylesheet" type = "text/css" href = "css/proj_details.css">
<title>Project Details</title>
<script src="jquery-3.2.1.js"></script>
<script>
  $(document).ready( function setStyle() {
      document.getElementById("proposal").setAttribute("style",
      "position: relative; top: 400px; left: 30px;");
      document.getElementById("proposal_file").setAttribute("style",
      "height:75%; width:90%");

      document.getElementById("review").setAttribute("style",
      "position: relative; top: 430px; left: 30px;");
      document.getElementById("review_file").setAttribute("style",
      "height:75%; width:90%");
  }
);
</script>

</head>

<body>
	<header>
	  Project Details
	  <a href = "index.html"><img src = "img/hkust_logo_white.png"/></a>
	</header>

  <table id="proj_details">
    <tr >
      <td><h4>Details</h4></td>
    </tr>
    <tr>
      <td class="details_header">Reference Number: </td>
      <td><?php echo $details_row['ref_no']; ?></td>
    </tr>
    <tr>
      <td class="details_header">Project Title: </td>
      <td><?php echo $details_row['proj_title']; ?></td>
    </tr>
    <tr>
      <td class="details_header">Receive Date: </td>
      <td><?php echo $details_row['receive_date']; ?></td>
    </tr>
    <tr>
      <td class="details_header">Due Date: </td>
      <td><?php echo $details_row['due_date']; ?></td>
    </tr>
    <tr>
      <td class="details_header">Department: </td>
      <td><?php echo $details_row['dept']; ?></td>
    </tr>
    <tr>
      <td class="details_header">Location: </td>
      <td><?php echo $details_row['room']; ?></td>
    </tr>
    <tr>
      <td class="details_header">Researcher(s): </td>
      <td><?php echo $details_row['researcher']; ?></td>
    </tr>
    <tr>
      <td class="details_header">Supervisor(s): </td>
      <td><?php echo $details_row['supervisor']; ?></td>
    </tr>
    <tr>
      <td class="details_header">Extension: </td>
      <td><?php echo $details_row['extn']; ?></td>
    </tr>
    <tr>
      <td><a href="<?php echo "mod_proj.php?ref_no=".$ref_no; ?>"><button>Modify Project Details</button></a></td>
      <td><a href="<?php echo "proj_comment.php?ref_no=".$ref_no; ?>"><button>Project Review</button></a></td>
    </tr>
  </table>

  <div id="proposal">
    <h4>Proposal</h4>
	  <?php
      if(empty($files_row['proposal_link'])) {
        echo "<iframe id='proposal_file' srcdoc=\"
        <p style='align:center;'>No proposal found.</p>
        <a style='align:center;' target='_blank' href='mod_proj.php?ref_no=$ref_no'>Link to a proposal</a>
        \">
        </iframe>
        ";
      }
      else {
        echo "<iframe id='proposal_file' src = \"".$files_row['proposal_link']."\"></iframe>";
      }
    ?>
  </div>

  <div id="review">
    <h4>Review</h4>
	  <?php
      if(empty($files_row['review_link'])) {
        echo "<iframe id='review_file' srcdoc=\"
        <p style='align:center;'>No review found.</p>
        <a style='align:center;' target='_blank' href='proj_comment.php?ref_no=$ref_no'>Add review</a>
        \">
        </iframe>
        ";
      }
      else {
        echo "<iframe id='review_file' src = \"".$files_row['review_link']."\"></iframe>";
      }

    ?>
  </div>

</body>
</html>
