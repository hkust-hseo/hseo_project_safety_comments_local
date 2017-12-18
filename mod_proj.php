<?php if (!isset($_POST["update_project"])):
?>
  <html>
  <head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" type = "text/css" href = "css/universal.css">
    <link rel = "stylesheet" type = "text/css" href = "css/new_proj.css">
    <title>Project Modification</title>

    <script type="text/javascript">

    	function CheckFileEmpty()
    	{
    		if(document.getElementById("upload_file").files.length != 0){
    			document.getElementById("upload_file").style.color = "black";
    		}
    		return 0;
    	}
    </script>

  </head>

  <body>
  <header>
  	Modify Project
  	<a href = "index.html"><img src = "img/hkust_logo_white.png"/></a>
  </header>

<?php

  $ref_no = $_GET["ref_no"];

  include "db_connect.php";

  $get_details_query = "SELECT * FROM proj_details WHERE ref_no = '";
  $get_details_query .= $ref_no;
  $get_details_query .= "';";

  $result_exist = mysqli_real_query($db, $get_details_query);
  $result = mysqli_store_result($db);
  $row = mysqli_fetch_array($result);

  $exist = false;
  for($i = 0; $i<7; $i++)
    if(!empty($row[$i]))
      $exist = true;

  if(!$exist)
    $ref_no = "No record found";
?>

  <form action = "" method = "post" enctype = "multipart/form-data">
  	Reference Number: <?php echo $ref_no; ?>
  	<br/>

  	Project Title:<br/>
  	<input type = "text" name = "proj_title" value = "<?php echo $row['proj_title']; ?>" default = "<?php echo $row['proj_title']; ?>" required>
  	<br/>

  	Date Received from Department:<br/>
  	<input id = 'receive_date' type = "date" value = "<?php echo $row['receive_date']; ?>" name = "receive_date">
  <!--	<script type = "text/javascript">
  		document.getElementById('receive_date').value = Date();
  	</script>
  -->
  	<br/>

  	Department:<br/>
  	<input type = "text" name = "dept" value = "<?php echo $row['dept']; ?>">
  	<br/>

  	Location (Room number): <br/>
  	<input type = "text" name = "room" value = "<?php echo $row['room']; ?>">
  	<br/>

  	Name of Researcher(s): (Separated with comma) <br/>
    <input type = "text" name = "researcher" value = "<?php echo $row['researcher']; ?>">
  	<br/>

  	Name of Supervisor(s): (Separated with comma) <br/>
    <input type = "text" name = "supervisor" value = "<?php echo $row['supervisor']; ?>">
  	<br/>

  	Extension:<br/>
  	<input type = "number" name = "extn" value = "<?php echo $row['extn']; ?>">
  	<br/>

  	Due Date:<br/>
  	<input id = 'due_date' type = "date" name = "due_date" value = "<?php echo $row['due_date']; ?>">
  <!--	<script type = 'text/javascript'>
  		var new_date = new Date(this.valueOf());
  		new_date.setDate(new_date.getDate() + 14);
  		document.getElementById('due_date').value = new_date;
  	</script>
  -->
  	<br/>

  	Replace Current Proposal:<br/>
    <input type = "file" name = "proj_file" id = "upload_file" onchange = "CheckFileEmpty()" accept = "application/pdf">
    <br/><br/>

  	<input type = "submit" value = "Update Project" name = "update_project">
  </form>
  </body>
  </html>

<?php else:

  include "db_connect.php";

  // Get new data from form
  $ref_no = $_GET['ref_no'];
  $receive_date = $_POST["receive_date"];
  $due_date = $_POST["due_date"];
  $proj_title = $_POST["proj_title"];
  $dept = $_POST["dept"];
  $room = $_POST["room"];
  $researcher = $_POST["researcher"];
  $supervisor = $_POST["supervisor"];
  $extn = $_POST["extn"];

  // injection prevention
  $receive_date = mysqli_real_escape_string($db, $receive_date);
  $due_date = mysqli_real_escape_string($db, $due_date);
  $proj_title = mysqli_real_escape_string($db, $proj_title);
  $dept = mysqli_real_escape_string($db, $dept);
  $room = mysqli_real_escape_string($db, $room);
  $researcher = mysqli_real_escape_string($db, $researcher);
  $supervisor = mysqli_real_escape_string($db, $supervisor);
  $extn = mysqli_real_escape_string($db, $extn);

  $update_query = "UPDATE proj_details ";
  $details_query = "SET proj_title = '$proj_title'";
  $where_query = " WHERE ref_no = '";
  $where_query .= $ref_no;
  $where_query .= "';";

  if(!empty($receive_date)){
    $details_query .= ", receive_date = '$receive_date'";
  }
  if(!empty($due_date)){
    $details_query .= ", due_date = '$due_date'";
  }
  if(!empty($dept)){
    $details_query .= ", dept = '$dept'";
  }
  if(!empty($room)){
    $details_query .= ", room = '$room'";
  }
  if(!empty($extn)){
    $details_query .= ", extn = '$extn'";
  }
  if(!empty($researcher)){
    $details_query .= ", researcher = '$researcher'";
  }
  if(!empty($supervisor)){
    $details_query .= ", supervisor = '$supervisor'";
  }

  $update_proj_query = $update_query . $details_query . $where_query;

  mysqli_query($db, $update_proj_query) or die("Update query failed\n".mysqli_error($db));

  /* Replace PDF */

  $file_size = $_FILES['proj_file']['size'];
  if($file_size > 0) {
    $proposal_link = "documents/proposals/".$ref_no.".pdf";
    if (move_uploaded_file($_FILES["proj_file"]["tmp_name"], $proposal_link)) {
      // echo "<p>FILE UPLOADED TO: $proposal_link</p>";
    }
    else {
      echo "<P>MOVE UPLOADED FILE FAILED!</P>";
      print_r(error_get_last());
    }
  }

/*    $file_query = "INSERT INTO proj_files (ref_no, file_size, proposal_link)".
                  "VALUES ('$ref_no', $file_size, '$proposal_link')";
    mysqli_query($db, $file_query) or die("File Query Failed. ");
*/
  echo '<html><head><link rel = "stylesheet" type = "text/css" href = "css/universal.css"><link rel = "stylesheet" type = "text/css" href = "../css/new_proj.css"></head><body>';
  echo '<header>Modify Project<a href = "index.html"><img src = "img/hkust_logo_white.png"/></a></header>';
  echo '<a style = "position: absolute; top: 120px; left: 50px;" href = "proj_comment.php?ref_no='.$ref_no.'" id = "next_button">Add Comments</a>';
  echo '</body></html>';

?>
<?php endif; ?>
