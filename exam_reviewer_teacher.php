<?php 
    session_start();
/////////////////////////////////////////////////////teacher version
    $exam = $_POST['exam'];
    $user = $_POST['sID'];
?>


  
<html>
<head>
  <title>
    Exam Reviewer
  </title>
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
      }
      .col-container {
        display: table;
        width: 100%;
      }
      .col {
        table-layout: fixed;
        display: table-cell;
        padding: 16px;
        border: 1px solid black;
        width: 500px;
        
      }
      button {
      width: 20%;
      height: 5%;
      background-color: #CC0000;
      color: white;
      font-size : 20px;
      border: 1px solid black;
      border-radius: 15px;
      margin: 0px auto;
      cursor: pointer;
    }
    button.inc {
      width: 10%;
      height: 30px;
      background-color: #CC0000;
      color: white;
      font-size : 20px;
      border: 1px solid black;
      border-radius: 15px;
      margin: 0px auto;
      cursor: pointer;
    }
    button.listed {
      width: 90%;
      height: 5%;
      background-color: #CC0000;
      color: white;
      font-size : 20px;
      border: 1px solid black;
      border-radius: 15px;
      margin: 0px auto;
      cursor: pointer;
    }
      button:hover {
          background-color: #730000;
        }
        
      div {
          margin: auto;
          height: 1200px;
          border-radius: 15px;
          background-color: #EFEFEF;
        }
      div.side {
        width: 200px;
        table-layout: fixed;
        display: table-cell;
        padding: 16px;
        border: 1px solid black;
        background-color: #EFEFEF;
        text-align:center
      }
      div.col {
        width: 80%;
        table-layout: fixed;
        display: table-cell;
        padding: 16px;
        border: 1px solid black;
        background-color: #EFEFEF;
      }
      div.btn-group{
        display: block;
      }
  </style>
</head>
<body>
  <div id="myModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p style="margin-top: 10px;">Revision Submitted</p>
    </div>
  
  </div>
  <div class="side"> 
      <p>Questions</p>
      <div id="sideList" class="btn-group">
      </div>
  </div> 
  <div class="col"> 
  Exam: <p style="display: inline" id="examName" ><?php echo $exam; ?></p><br>
  Student: <p style="display: inline"  id="ucid" ><?php echo $user; ?></p><br>
  <p id="q1"></p>
  answer:<br><textarea readonly rows="20" cols="100" name="answer" id="answer"/></textarea>
  <p hidden id="qID1" ></p>
  <br>
  <p id="points" >points: </p>
<table id="results">
    <tr>
       <th>input</th>
       <th>output</th>
       <th>Points Earned</th>
     </tr>
</table>
  Teacher Comments:<br><textarea rows="10" cols="100" name="comment" id="comment"/></textarea>
  <br>
  <button onclick="decQ()"><<</button>
  <button onclick="incQ()">>></button> 
  <p id="numOfQuestions"></p>
  <button onclick="submitExam()">Submit Exam</button>
  <p id="submitStatus" ></p>
  <form action="home.php" method="post">
  <button type="submit" value="submit">back</button>
  </form>
</div> 
<script language="javascript">


var question_content_list = [];
var comment_content_list = [];
var point_content_list = [];
var TotP = [];
var FuncP = [];
var ConsP = [];
var ParamP = [];
var ColonP = [];
var TestP = [];
var question_ID_list = [];
var sendData = new FormData();

var responseData;                                   //to be filled with fetch

var size;
var examSize = 0;

var incrementQ = 0;
var formData = new FormData();
var page = 1;
var examPage = 1;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getQuestions(){
  var examName = document.getElementById("examName").innerHTML;
  var userName = document.getElementById("ucid").innerHTML; 
  
  var formData = new FormData();
  formData.append('ACTION', "GET_EXAM_ANSWERS");
  formData.append('EXAM_NAME', examName);         
  formData.append('UCID', userName);      
  
  const url = 'https://web.njit.edu/~md535/beta/frontEnd.php';
  
  const options = {
    method: 'POST'
    ,body: formData
  };
  
  fetch(url, options).then(dataWrappedByPromise => dataWrappedByPromise.json()).then(data => {
    console.log(data)
    responseData = data;
    size = responseData.QuestionText.length;
    for(var i = 0 ; i < size; i++)
    {
      comment_content_list[i] = responseData.TeacherComments[i];;
      FuncP[i] = responseData.FuncP[i];
      ConsP[i] = responseData.ConsP[i];
      ParamP[i] = responseData.ParamP[i];
      ColonP[i] = responseData.ColonP[i];
      TestP.push(responseData.TestP[i].split('~'));
    }
    fillquestion();
    fillList();
    document.getElementById("numOfQuestions").innerHTML = "question " + page + " of " + size;
    
});
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
getQuestions();


function fillquestion()
{
  if(incrementQ < size)
  {
      document.getElementById("q1").innerHTML = "question: " + responseData.QuestionText[incrementQ];
      document.getElementById("numOfQuestions").innerHTML = "question " + page + " of " + size;
      document.getElementById("answer").innerHTML = responseData.AnswerText[incrementQ];
      document.getElementById("comment").innerHTML = responseData.TeacherComments[incrementQ];
      fillTables(incrementQ);
  }
}

//////////////////////////////////////////////////////////////////////////////////////
function incQ()
{
  if(incrementQ< size-1)
  {
    saveContent(incrementQ);
    incrementQ++;
    document.getElementById("comment").value = comment_content_list[incrementQ];
    document.getElementById("points").value = point_content_list[incrementQ];
    page++;
    emptyTables();
    fillquestion();
  }
}

function decQ()
{
  if(incrementQ > 0)
  {
    saveContent(incrementQ);
    incrementQ--;
    document.getElementById("comment").value = comment_content_list[incrementQ];
    document.getElementById("points").value = point_content_list[incrementQ];
    page--;
    emptyTables();
    fillquestion();
  }
}

function saveContent(i)
{
  comment_content_list[incrementQ] = document.getElementById("comment").value;
  FuncP[incrementQ] = document.getElementById("pointInput0").value;
  ParamP[incrementQ] = document.getElementById("pointInput1").value;
  ConsP[incrementQ] = document.getElementById("pointInput2").value;
  ColonP[incrementQ] = document.getElementById("pointInput3").value;
  for(var j = 0; j < TestP[incrementQ].length; j++)
  {
    TestP[incrementQ][j] = document.getElementById("pointInput" + (j+4)).value;
  }
}

function submitExam()
{
  saveContent(incrementQ);
  var userName = document.getElementById("ucid").innerHTML; 
  var examName = document.getElementById("examName").innerHTML;
  
  var sendComments = [];
  
  for(var i = 0; i< comment_content_list.length; i++)
  {
    sendComments.push(comment_content_list[i].replace(/,/g, "~"));
  }
  
  for(var i = 0; i< size; i++)
  {
    TotP[i] = 0;
    TotP[i] += parseInt(FuncP[i]);
    TotP[i] += parseInt(ConsP[i]);
    TotP[i] += parseInt(ColonP[i]);
    TotP[i] += parseInt(ParamP[i]);
    for(var j = 0; j< TestP[i].length; j++)
    {
      TotP[i] += parseInt(TestP[i][j]);
    }
  }
  var TestStr = "";
  for(var i = 0; i< size; i++)
  {
    for(var j = 0; j< TestP[i].length; j++)
    {
      TestStr += TestP[i][j];
      if(j != TestP[i].length-1)
      {
        TestStr += "~";
      }
    }
    if(i != size-1)
    {
      TestStr += ",";
    }
  }
  
  sendData.append('ACTION', 'TEACHER_SUBMIT_EXAM');
  sendData.append('EXAM_NAME', examName);
  sendData.append('UCID', userName);
  sendData.append('TestP', TestStr);
  sendData.append('TotP', TotP);
  sendData.append('FuncP', FuncP);
  sendData.append('ConsP', ConsP);
  sendData.append('ParamP', ParamP);
  sendData.append('ColonP', ColonP);
  sendData.append('COMMENTS', sendComments);
  
  const url = 'https://web.njit.edu/~md535/beta/frontEnd.php';
  
  const options = {
    method: 'POST'
    ,body: sendData
  };
  
  fetch(url, options).then(dataWrappedByPromise => dataWrappedByPromise.json()).then(data => {
    console.log(data)
    showStatus();
  });
}

function fillTables(j) {
  emptyTables();
  var count = 0;
  var table = document.getElementById("results");
  
  
  comments = responseData["MiddleComments"][j].split('~');//change 0 with j
  if(comments[0] != "")
  {
    var row = table.insertRow(-1);
    var cell1 = row.insertCell(0);
    cell1.innerHTML = "<b>" + comments[0] + "</b>";
    cell1.colSpan  = "3";
  }
  
  var row = table.insertRow(-1);
  var cell1 = row.insertCell(0);
  cell1.colSpan  = "2";
  var cell3 = row.insertCell(1);
  cell1.innerHTML = "<b>Answer Comments</b>";
  cell3.innerHTML = "<b>Points Earned</b>";
  
  for(var i = 1; i < comments.length-1; i++)//general comments
  {
    var row = table.insertRow(-1);
    var cell1 = row.insertCell(0);
    cell1.innerHTML = comments[i];
    cell1.colSpan  = "2";
    var cell2 = row.insertCell(1);
    var pts = document.createElement("input");
    var name = "pointInput" + count;
    pts.setAttribute( "id", name );
    pts.size="10"
      
    switch(i) {
     case 1:
       pts.setAttribute( "value", FuncP[j] );
       break;
     case 2:
       pts.setAttribute( "value", ParamP[j] );
       break;
     case 3:
       pts.setAttribute( "value", ConsP[j] );
       break;
     case 4:
       pts.setAttribute( "value", ColonP[j] );
       break;
     default:
      }
      cell2.appendChild(pts);
      count++; 
    }
  
  var row = table.insertRow(-1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    cell1.innerHTML = "<b>input</b>";
    cell2.innerHTML = "<b>output</b>";
    cell3.innerHTML = "<b>Points Earned</b>";
    testCases = comments[comments.length-1].split('@');
    testCasesPoints = responseData.TestP[j].split('~');
  if(comments[0] == "")
  {
     for(var i = 0; i < testCases.length-1; i+=2)//testcaseLength
    {
      if(testCases[i] != "")
      {
        var row = table.insertRow(-1);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        cell1.innerHTML = testCases[i];
        if(testCases[i+1].includes("runtime error"))
        {
          var actind = testCases[i+1].indexOf("Actual");
          cell2.innerHTML = testCases[i+1].substr(0,actind);
        }
        else
        {
          cell2.innerHTML = testCases[i+1];
        }
        var pts = document.createElement("input");
        pts.setAttribute( "value", TestP[j][i/2] );
        var name = "pointInput" + count;
        pts.setAttribute( "id", name );
        pts.size="10"
        cell3.appendChild(pts);
        count++;
      }
    }
  }
  else
  {
      for(var i = 0; i < testCases.length; i++)//testcaseLength
      {
        if(testCases[i] != "")
        {
          var row = table.insertRow(-1);
          var cell1 = row.insertCell(0);
          cell1.colSpan  = "2";
          cell1.innerHTML = testCases[i];
          var cell3 = row.insertCell(1);
          var pts = document.createElement("input");
          pts.setAttribute( "value", TestP[j][i] );
          var name = "pointInput" + count;
          pts.setAttribute( "id", name );
          pts.size="10"
          cell3.appendChild(pts);
          count++;
        }
      }
  }
  
    var sum = 0;
    for(var i = 0; i < count; i++)
    {
      sum += parseInt(document.getElementById("pointInput" + i).value);
    }
  document.getElementById("points").innerHTML = "Points: "+sum+"/" + responseData.TPoints[incrementQ];
}

function emptyTables() {
  
    var table = document.getElementById("results");
    var rows = table.children[0];
    var x =rows.children.length;
    for(var i = 0; i < x;i++)
    {
      rows.removeChild(rows.lastElementChild);
    }
}

function changeQ(i)
{
    saveContent(incrementQ);
    incrementQ = i;
    document.getElementById("comment").value = comment_content_list[incrementQ];
    document.getElementById("points").value = point_content_list[incrementQ];
    page = i+1;
    emptyTables();
    fillquestion();
}

function fillList()
{
  var questionList = document.getElementById("sideList");
  for(var i = 0; i < responseData.AnswerText.length; i++)
  {
    var node = document.createElement("button");
    var action = "changeQ(" + i +")";
    node.setAttribute( "onclick", action );
    node.setAttribute( "class", "listed" );
    node.innerHTML = "Question" + (i+1);
    questionList.appendChild(node);
    var br = document.createElement('br');
    questionList.appendChild(br);
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



