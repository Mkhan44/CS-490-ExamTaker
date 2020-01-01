<?php
session_start();
  if (!empty($_POST['es1'])) 
  {
    $test = $_POST['es1'];
  } 
  else if (!empty($_POST['es2'])) 
  {  
      $test = $_POST['es2'];
  }
?>


<html>
<head>
<title>
Exam Selector
</title>
<style>
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
      height: 150px;
      border-radius: 50px;
      background-color: #EFEFEF;
      text-align:center
    }
</style>
</head>
  <div id="box">
    <body>
    <p>available exams:</p>
      <p hidden id="es" ><?php echo $test; ?></p>
      <p hidden id="ucid" ><?php echo $_SESSION["user"]; ?></p>
      <table id="exams">
      </table>
      <p id="numOfExams"></p>
      
      <form action="home.php" method="post">
        <button class="back" type="submit" value="submit">back</button>
      </form>
  </div>
<script language="javascript">
var responseData = []; 
var size;

if(document.getElementById("es").innerHTML)
{
  searchExams();
}
else
{
  searchExamByUser();
}
    
function searchExams(){
  
  var formData = new FormData();
  formData.append('ACTION', "SEARCH_EXAM");
  formData.append('EXAM_NAME', "ALL");                                        //might have to change to something else
  
  const url = 'https://web.njit.edu/~md535/beta/frontEnd.php';

  const options = {
    method: 'POST'
    ,body: formData
  };
  
  fetch(url, options).then(dataWrappedByPromise => dataWrappedByPromise.json()).then(data => {
    console.log(data)
    responseData = data;
    fillTables(0);
});
}

function searchExamByUser(){
  var name = document.getElementById("ucid").innerHTML;
  
  var formData = new FormData();
  formData.append('ACTION', "SEARCH_EXAMS_BY_USER");
  formData.append('UCID', name);
  
  const url = 'https://web.njit.edu/~md535/beta/frontEnd.php';
  
  const options = {
    method: 'POST'
    ,body: formData
  };
  
  fetch(url, options).then(dataWrappedByPromise => dataWrappedByPromise.json()).then(data => {
    console.log(data)
    responseData = data;
    size = responseData.ExamName.length;
    fillTables(1);
});
}

function fillTables(j) {
  var table = document.getElementById("exams");
  if(j)
  {
    var count = 0;
    for(var i = 0; i < responseData.ExamName.length; i++)
    {
      if(responseData.Released[i] == "1")
      {
        var row = table.insertRow(count);
        var name = row.insertCell(0);
        var test = row.insertCell(1);
        var form = document.createElement("form");
        form.setAttribute( "action", "exam_reviewer_student.php" );
        form.setAttribute( "method", "POST" );
        var btn = document.createElement("button");
        btn.setAttribute( "name", "eID" );
        btn.setAttribute( "value", responseData.ExamName[i] );
        btn.innerHTML = "View Submission";
        form.appendChild(btn);
        test.appendChild(form);
        name.innerHTML = responseData.ExamName[i];
        count++;
      }
    }
    var box = document.getElementById("box");
    var size = 80*responseData.ExamName.length + 90;
    box.setAttribute( "style", "height: "+size+"px;" );
  }
  else
  {
    for(var i = 0; i <  responseData.length; i++)
    {
      var row = table.insertRow(i);
      var name = row.insertCell(0);
      var test = row.insertCell(1);
      var form = document.createElement("form");
      form.setAttribute( "action", "exam_taker.php" );
      form.setAttribute( "method", "POST" );
      var btn = document.createElement("button");
      btn.setAttribute( "name", "eID" );
      btn.setAttribute( "value", responseData[i].ExamName );
      btn.innerHTML = "Take Exam";
      form.appendChild(btn);
      test.appendChild(form);
      name.innerHTML = responseData[i].ExamName;
    }
    var box = document.getElementById("box");
    var size = 80*responseData.length + 90;
    box.setAttribute( "style", "height: "+size+"px;" );
  }
    
}

</script>


  
  </body>
</html>