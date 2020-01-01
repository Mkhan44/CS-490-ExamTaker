<html>
<head>
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
      height: 90%;
      border-radius: 50px;
      background-color: #EFEFEF;
      text-align:center;
      align-items:center;
    }
</style>
</head>
<body>
  <div id="box">
    <p>available exams:</p>
    <table id="exams">
    </table>
    <p id="numOfExams"></p>
    <form action="home.php" method="post">
      <button class="back" type="submit" value="submit">back</button>
    </form>
  </div>
    
    
<script language="javascript">
var responseData; 
var size;
    
function searchExams(){
  
  var formData = new FormData();
  formData.append('ACTION', "SEARCH_EXAM");
  formData.append('NAME', "ALL");          
  
  const url = 'https://web.njit.edu/~md535/beta/frontEnd.php';
  
  const options = {
    method: 'POST'
    ,body: formData
  };
  
  fetch(url, options).then(dataWrappedByPromise => dataWrappedByPromise.json()).then(data => {
    console.log(data)
    responseData = data;
    size = responseData.length;
    fillTables();
});
}
searchExams();

function fillTables() {
  var table = document.getElementById("exams");
  for(var i = 0; i < responseData.length; i++)
  {
    var row = table.insertRow(i);
    var name = row.insertCell(0);
    var test = row.insertCell(1);
    var form = document.createElement("form");
    form.setAttribute( "action", "student_selector_teacher.php" );
    form.setAttribute( "method", "POST" );
    var btn = document.createElement("button");
    btn.setAttribute( "name", "eID" );
    btn.setAttribute( "value", responseData[i].ExamName );
    btn.innerHTML = "View Submissions";
    form.appendChild(btn);
    test.appendChild(form);
    
    name.innerHTML = responseData[i].ExamName;
    
  }
  var box = document.getElementById("box");
    var size = 80*responseData.length + 90;
    box.setAttribute( "style", "height: "+size+"px;" );
}


</script>


  
</body>
</html>