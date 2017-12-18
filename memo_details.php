<?php
  require("db_connect.php");

  $memo_no = $_GET['memo_no'];    // memo number passed via URL

  $ref_array = array();   // Array to store all ref_no of linked projects
  $ref_count = 0;

  $get_ref_query = "SELECT ref_no FROM proj_details WHERE memo = '$memo_no';";    // To get reference number of all projects linked to this memo
  mysqli_real_query($db, $get_ref_query);
  $result = mysqli_store_result($db);       // Getting results from database
  while($row = mysqli_fetch_array($result)) {
    $ref_array[$ref_count++] = $row['ref_no'];    // for each row of result (ref_no), put it in array
  }
?>

<html>
<head>
  <meta charset="utf-8">
  <link rel = "stylesheet" type = "text/css" href = "css/universal.css">
  <link rel = "stylesheet" type = "text/css" href = "css/memo_details.css">

  <title>Memo Details</title>

  <script src="jquery-3.2.1.js"></script>
  <script>
    function sendMemo(memo_no){
      var send_memo;

      send_memo = $.ajax({
        url: "send_mail.php",
        type: "get",
        data: {
          mode: "send_memo",
          memo_no: memo_no
        }
      });

      var confirm_sent;    // to be ajax object

      confirm_sent = $.ajax({
          url: "memo_sent.php",
          type: "post",
          data: {
            memo_no: memo_no    // passing memo_no to mark column 'sent' = true in database
          }
      });
      confirm_sent.done(function(){
        alert("Memo approved.");
      });
    }
  </script>

</head>

<body><header>
Memo Details
<a href = "index.html"><img src = "img/hkust_logo_white.png"/></a>
</header>

<div id="memo_file">
<h3>Memo</h3>
  <?php
    $get_file_query = "SELECT * FROM memo_details WHERE memo_no = '$memo_no';";

  	if(mysqli_real_query($db, $get_file_query)){
  		$result = mysqli_store_result($db);
  		$row = mysqli_fetch_array($result);
  	  echo "<iframe src = \"".$row['file_link']."\"></iframe>";
  	}
  ?>
</div>

<div id="projects">
<h3>Linked Projects</h3>
<?php
  for($i = 0; $i < $ref_count; $i++) {
    echo "<a target='_blank()' href=\"proj_details.php?ref_no=". $ref_array[$i] ."\">". $ref_array[$i] ."</a><br/>";
  }
?>
</div>

<button onclick="sendMemo('<?php echo $memo_no; ?>')">Approve and Send Memo</button>
</body>
</html>
