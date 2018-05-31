Array.prototype.sameValues = function() {
  if(this.length == 1) {
    return true;
  }
  for(var i = 1; i < this.length; i++) {
    if (this[i] != this[0]) {
      return false;
    }
    return true;
  }
}

function search() {
  // remove all rows in table: 'results'
  while(document.getElementById("result_display").firstChild) {
    document.getElementById("result_display").removeChild(document.getElementById("result_display").firstChild);
  }

  // get all inputs from search form
  var ref_no = document.getElementById("ref_no_input").value;
  var supervisor = document.getElementById("supervisor_input").value;
  var title = document.getElementById("title_input").value;
  var room = document.getElementById("room_input").value;
  var reviewer = document.getElementById("reviewer_input").value;

  var start_date = document.getElementById("start_date").value;
  var end_date = document.getElementById("end_date").value;

  var completed = document.getElementById("completed").checked;
  var incomplete = document.getElementById("incomplete").checked;
  var sent = document.getElementById("sent").checked;

  var request;
  var results = new Array();    // array of results (one array element = one array of data)
  var result = new Array();     // array of data = one row of result

  if(request) {request.abort(); }       // clear any previous requests

  // ajax connection to proj_directory.php (retrieve data from db)
  request = $.ajax({
      url: "proj_directory.php",
      type: "post",
      data: {
        completed: completed,
        incomplete: incomplete,
        sent: sent,

        ref_no: ref_no,
        supervisor: supervisor,
        title: title,
        room: room,
        reviewer: reviewer,
        start_date: start_date,
        end_date: end_date
      }
  });

  // after data is retrieved
  request.done(function(response, textStatus, jqXHR) {
    if(response == "null") {
      console.log("No response");
    }
    else {
      console.log(response);
      var results = JSON.parse(response);
      console.log("size of result = " + results.length);
      displayResults(results);
    }
  });
}

function displayResults(results) {
  // call display function for each row of actual data

  // put message if there is no record
  if(results.length == 0) {
    var display_row = document.createElement("tr");
    var message = document.createElement("td");
    var text = document.createTextNode("There is no record");

    message.appendChild(text);
    message.setAttribute("colspan", "8");
    message.setAttribute("align", "center");
    display_row.appendChild(message);
    document.getElementById("result_display").appendChild(display_row);
  }

  // print the records from php
  for(var i = 0; i < results.length; i++) {
    if(i%2 == 0) {
      displayRow(results[i], "#EEEEEE");
    }
    else {
      displayRow(results[i], "#DDDDDD");
    }
  }
}

function displayRow(output_row, background_color) {
  var display_row = document.createElement("tr");

  // Checkbox for bulk actions
  var box_td = document.createElement("td");
  var select_box = document.createElement("input");
  select_box.setAttribute("type", "checkbox");
  select_box.setAttribute("name", "project");
  select_box.setAttribute("value", output_row[0]);
  box_td.appendChild(select_box);
  box_td.setAttribute("style", "width:20px;")
  display_row.appendChild(box_td);

  // Basic identification information
  for(var i=0; i<output_row.length; i++) {
    var display_node = document.createElement("td");
    if(i < 4) {
      var inner_text = document.createTextNode(output_row[i]);
    }
    else {
      if(output_row[i] && output_row[i] != "0") {
        var inner_text = document.createTextNode("✔");
      }
      else {
        var inner_text = document.createTextNode("");
      }
    }
    display_node.appendChild(inner_text);

    // set style for each td
    switch(i) {
      case 0:
        display_node.setAttribute("style", "width:200px;");
        break;
      case 1:
        display_node.setAttribute("style", "width:550px;");
        break;
      case 2:
        display_node.setAttribute("style", "width:200px;");
        break;
      case 3:
        display_node.setAttribute("style", "width:300px;");
        break;
      case 4:
        display_node.setAttribute("style", "width:80px;");
        break;
      case 5:
        display_node.setAttribute("style", "width:80px;");
        break;
      case 6:
        display_node.setAttribute("style", "width:80px;");
        break;
    }
    display_row.appendChild(display_node);
  }
  // Button to access project details (and more!)
  var details_td = document.createElement("td");    // <td/>
  var details_link = document.createElement("a");   // <a> for href that links to details page
  var details_button = document.createElement("button");    // <button/> for style
  var details_text = document.createTextNode("Details");    // text inside <button/>
  details_button.appendChild(details_text);
  details_button.setAttribute("style", "width:60px;");
  details_link.setAttribute("href", "proj_details.php?ref_no="+output_row[0]);    // href to details page for more information
  details_link.setAttribute("target", "_blank");
  details_link.appendChild(details_button);
  details_td.appendChild(details_link);
  display_row.appendChild(details_td);

  display_row.setAttribute("style", "height:35px; background-color:"+background_color+";");

  document.getElementById("result_display").appendChild(display_row);

  return;
}

function genMemo()
{
  // Array stores all checked memo boxes
  var ref_array = new Array();
  var supervisor_array = new Array();

  // Get current <tr>
  var result_ptr = document.getElementById("result_display").firstChild;

  do {
    var current_box = result_ptr.firstChild.firstChild;       // get current checkbox
    if(current_box.checked) {
      // Only store value (ref_no) of current box if it is checked
      ref_array[ref_array.length] = current_box.value;
      supervisor_array[supervisor_array.length] = result_ptr.getElementsByTagName("td")[3].innerHTML;
    }
  } while(result_ptr = result_ptr.nextSibling);   // do while there is still not null element

  // data verification
  // No project selected
  if(ref_array.length <= 0) {
    alert("Please select at least one checkbox to proceed.");
    return 0;
  }
  // Multiple supervisor names
  if(!supervisor_array.sameValues()) {
    cont = confirm("Selected projects have different supervisors, proceed anyway?");
    if(!cont) {
      return 0;
    }
  }

  var php_array = JSON.stringify(ref_array);    // from JSON to string (to be parsed to print_memo.php)

  var memo_request;
  if(memo_request) {memo_request.abort(); }       // clear any previous requests

  // ajax connection to print_memo.php (passing array of ref_no to be included in memo)
  memo_request = $.ajax({
      url: "print_memo.php",
      type: "post",
      data: {
        ref_array: php_array
      }
  });
  memo_request.done(function(memo_no){
    memo_link = "documents/memos/" + memo_no + ".pdf";
    memo_pdf = window.open(memo_link);
    // Confirmation pop-up
    // discuss to use wait time or scroll detection

    var memo_interval = setInterval(function() {
      if(memo_pdf.closed !== false) {
        // stop the checking closed interval
        clearInterval(memo_interval);

        if (window.confirm("Confirm memo generation?") == true) {
          // Update memo details in database with a new php
          var update_memo_db;

          update_memo_db = $.ajax({
            url: "memo_generated.php",
            type: "post",
            data: {
              memo_no: memo_no,
              ref_array: php_array
            }
          });

          // calling send_mail here
          var send_pending_memo;

          send_pending_memo = $.ajax({
            url: "send_mail.php",
            type: "post",
            data: {
              mode: "pending_memo",
              ref_array: php_array
            }
          });
        }
      }
    }, 1000);

  });

  return 0;
}
