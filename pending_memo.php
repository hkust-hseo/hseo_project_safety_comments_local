<?php

  require "db_connect.php";

  class memoDetails {
    public $memo_no;
    public $supervisor;

    public function __construct($no, $name) {
      $this->memo_no = $no;
      $this->supervisor = $name;
    }
  }

  $memos = array();
  $memo_count = 0;

  // Get pending memo_no from proj_details
  $pending_memo_query = "SELECT * FROM proj_details WHERE sent=0 GROUP BY memo;";

  mysqli_real_query($db, $pending_memo_query);
  $result = mysqli_store_result($db);
  while($row = mysqli_fetch_array($result)) {
    if($row['memo'] != "NULL" && !empty($row['memo']) && $row['memo'] != "0"){
      $temp = new memoDetails($row['memo'], $row['supervisor']);
      $memos[$memo_count] = $temp;
      $memo_count++;
    }
  }
?>

<html>
<head>
  <meta charset="utf-8">
  <link rel = "stylesheet" type = "text/css" href = "css/universal.css">
  <link rel = "stylesheet" type = "text/css" href = "css/pending_memo.css">

  <title>Pending Memos</title>

  <script>
    function openMemoDetails(memo_no) {
      var details_link = "memo_details.php?memo_no=" + memo_no;

      var details_page = window.open(details_link);
      var current_page = window.self;

      details_page.onunload = function() {
        current_page.location.reload();
      }
    }
  </script>

</head>

<body><header>
Pending Memos
<a href = "index.html"><img src = "img/hkust_logo_white.png"/></a>
</header>

  <table id="pending_list">
    <tr id="list_header">
      <td style="width:200px;">Memo Number</td>
      <td style="width:300px;">Supervisor</td>
      <td style="width:80px;">Details</td>
    </tr>
    <?php
      for($i = 0; $i < $memo_count; $i++) {
        echo "<tr><td>" . $memos[$i]->memo_no . "</td><td>" . $memos[$i]->supervisor . "</td><td><button onclick='openMemoDetails(\"".$memos[$i]->memo_no."\")'>Details</button></td></tr>";
      }
    ?>
  </table>

</body>
</html>
