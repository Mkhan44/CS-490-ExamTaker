<?php
  
$test = $_POST['eID'];
  
  
$out = <<<EOD
     <p hidden id="eID">$test</p>
     submitted exams for <p>$test:</p>
EOD;
?>

<html>
  <head>
    <style>
        .modal {
          display: none;
          position: fixed;
          padding-top: 100px;
          border-radius: 0px;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
          background-color: #CC0000;
          text-align: center;
          border: 1px solid black;
          color: white;
          font-size : 25px;
          width: 300px;
          height: 50px;
        }
        
        .close {
          color: #aaaaaa;
          float: right;
          font-size: 28px;
          font-weight: bold;
          margin-right: 18px;
          margin-top: 9px;
        }
        
        .close:hover {
          color: #000;
          cursor: pointer;
        }
      table, td ,th{
      border: 1px solid black;
      border-collapse: collapse;
      margin: 0px auto;
    }
      button {
          width: 90%;
          height: 10%;
          background-color: #CC0000;
          color: white;
          font-size : 20px;
          border: 1px solid black;
          border-radius: 15px;
          margin: 0px auto;
          cursor: pointer;
        }
        button.back {
      height: 30px;
      width: 30%;
    }
      button:hover {
          background-color: #730000;
        }
        
      div {
          margin: auto;
          width: 95%;
          height: 90%;
          border-radius: 50px;
          background-color: #EFEFEF;
          text-align:center
        }
    </style>
  </head>
  <body>
  <div id="myModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p style="margin-top: 10px;">Exam Released</p>
    </div>
  
  </div>
  
  <div>
  <p><?php echo $out; ?></p>
  
  <button onclick="ReleaseExam()">Release Exam</button>
  <hr>
    <table id="students">
      <tr>
         <th>Student</th>
         <th>Grade</th>
       </tr>
    </table>
    <p id="numOfExams"></p>
    
    <form action="exam_selector_teacher.php" method="post">
      <button class="back" type="submit" value="submit">back</button>
    </form>
  </div>
    
<script language="javascript">
var examName = document.getElementById("eID").innerHTML;
var responseData; 
var size;

function searchExams(){
  
  var formData = new FormData();
  formData.append('ACTION', "SEARCH_EXAMS_BY_EXAM_NAME");
  formData.append('EXAM_NAME', examName);
  
  const url = 'https://web.njit.edu/~md535/beta/frontEnd.php';
  
  const options = {
    method: 'POST'
    ,body: formData
  };
  
  fetch(url, options).then(dataWrappedByPromise => dataWrappedByPromise.json()).then(data => {
    console.log(data)
    responseData = data;
    size = responseData.Grade.length;
    fillTables();
    document.getElementById("numOfExams").innerHTML = "Submissions found: " + size;
});
}
searchExams();

function ReleaseExam()
{
    
  var formData = new FormData();
  formData.append('ACTION', "RELEASE_EXAM");
  formData.append('EXAM_NAME', examName);                                       
  
  const url = 'https://web.njit.edu/~md535/beta/frontEnd.php';
  
  const options = {
    method: 'POST'
    ,body: formData
  };
  
  fetch(url, options).then(dataWrappedByPromise => dataWrappedByPromise.json()).then(data => {
    console.log(data)
    showStatus();
});
}


function fillTables() {
  var table = document.getElementById("students");
  for(var i = 0; i < responseData.Student.length; i++)
  {
    var row = table.insertRow(i+1);
    var name = row.insertCell(0);
    var grade = row.insertCell(1);
    var test = row.insertCell(2);
    
    var form = document.createElement("form");
    form.setAttribute( "action", "exam_reviewer_teacher.php" );
    form.setAttribute( "method", "POST" );
    var btn = document.createElement("button");
    btn.setAttribute( "name", "exam" );
    btn.setAttribute( "value", examName );
    btn.innerHTML = "Review";
    var inpt = document.createElement("input");
    inpt.hidden = true;
    inpt.setAttribute( "name", "sID" );
    inpt.setAttribute( "value", responseData.Student[i] );
    
    form.appendChild(btn);
    form.appendChild(inpt);
    test.appendChild(form);
    
    name.innerHTML = responseData.Student[i];
    grade.innerHTML = responseData.Grade[i];
  }
}

var modal = document.getElementById("myModal");
var span = document.getElementsByClassName("close")[0];

function showStatus() 
{
  modal.style.display = "block";
}

span.onclick = function() {
  modal.style.display = "none";
}

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>


  
  </body>
</html>