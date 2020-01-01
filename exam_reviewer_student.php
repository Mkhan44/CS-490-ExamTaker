<?php 
    session_start();
/////////////////////////////////////////////////////student version
  $test = $_POST['eID'];
?>


  
<html>
<head>
  <title>
  Exam Reviewer
  </title>
  <style>
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
          height: 800px;
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
      mark.red {
        color:red;
        background: none;
      }

      mark.green {
          color:green;
          background: none;
      }
    </style>
</head>
<body>
  <div class="side"> 
      <p>Questions</p>
      <div id="sideList" class="btn-group">
      </div>
  </div> 
  <div class="col"> 
  <p hidden id="ucid" ><?php echo $_SESSION["user"]; ?></p>
  <p id="examName" ><?php echo $test; ?></p>
  <p id="total" ></p>
  <p id="q1"></p>
  answer:<textarea readonly rows="20" cols="100" name="answer" id="answer"/></textarea>
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
<br>
  Teacher Comments:<p id="TC5" ></p>
  <button onclick="decQ()"><<</button>
  <button onclick="incQ()">>></button> 
  <p id="numOfQuestions"></p>
  <form action="home.php" method="post">
  <button type="submit" value="submit">back</button>
  </form>
  </div> 
<script language="javascript">

var examName = document.getElementById("examName").innerHTML;
var ucid = document.getElementById("ucid").innerHTML;
var question_content_list = [];
var answer_content_list = [];
var TotP = [];
var FuncP = [];
var ConsP = [];
var ParamP = [];
var ColonP = [];
var TestP = [];
var question_ID_list = [];


var responseData;                                   //to be filled with fetch

var size;
var examSize = 0;

var incrementQ = 0;

var page = 1;
var examPage = 1;

function getQuestions(){
  
  var formData = new FormData();
  formData.append('ACTION', "GET_EXAM_ANSWERS");
  formData.append('EXAM_NAME', examName);
  formData.append('UCID', ucid);
  
  const url = 'https://web.njit.edu/~md535/beta/frontEnd.php';
  
  const options = {
    method: 'POST'
    ,body: formData
  };
  fetch(url, options).then(dataWrappedByPromise => dataWrappedByPromise.json()).then(data => {
    console.log(data)
    responseData = data;
    size = responseData.AnswerText.length;
    for(var i = 0 ; i < size; i++)
    {
      FuncP[i] = responseData.FuncP[i];
      ConsP[i] = responseData.ConsP[i];
      ParamP[i] = responseData.ParamP[i];
      ColonP[i] = responseData.ColonP[i];
      TestP.push(responseData.TestP[i].split('~'));
    }
    fillquestion();
    fillList();
    getTotal();
    document.getElementById("numOfQuestions").innerHTML = "question " + page + " of " + size;
    
});
}
getQuestions();

function fillquestion()
{
  if(incrementQ < size)
  {
      document.getElementById("q1").innerHTML = "question: " + responseData.QuestionText[incrementQ];
      document.getElementById("numOfQuestions").innerHTML = "question " + page + " of " + size;
      document.getElementById("answer").value = responseData.AnswerText[incrementQ];
      document.getElementById("TC5").innerHTML = "<b>" + responseData.TeacherComments[incrementQ] + "</b>";
      fillTables(incrementQ);
  }
}

//////////////////////////////////////////////////////////////////////////////////////

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
        // code block
    }
    pts.setAttribute( "readonly", true );
    
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
      var row = table.insertRow(-1);
      var cell1 = row.insertCell(0);
      var cell2 = row.insertCell(1);
      var cell3 = row.insertCell(2);
      var ind = testCases[i].indexOf(':')+1;
      var end = testCases[i].length;
      cell1.innerHTML = testCases[i];
      
      var ind = testCases[i+1].indexOf(':')+1;
      var end = testCases[i+1].length;
      if(testCases[i+1].includes("Actual"))
      {
        var actind = testCases[i+1].indexOf("Actual");
        var ind2 = testCases[i+1].lastIndexOf(':')+1;
        if(testCases[i+1].includes("runtime error"))
        {
          cell2.innerHTML = testCases[i+1].substr(0,actind);
        }
        else
        {
          cell2.innerHTML = testCases[i+1].substr(0,ind) + 
          "<mark class="+"green"+">" + testCases[i+1].substr(ind,actind - ind)+"</mark>"+
          testCases[i+1].substr(actind,ind2-actind) +
          "<mark class="+"red"+">" + testCases[i+1].substr(ind2,end -ind2)+"</mark>";
        }
      }
      else
      {
        cell2.innerHTML = testCases[i+1].substr(0,ind) + "<mark class="+"green"+">" + testCases[i+1].substr(ind,end)+"</mark>";
      }
      
      /*
      var str = "Hello planet earth, you are a great planet.";
      var n = str.lastIndexOf("planet"); 
      */
      
      var pts = document.createElement("input");
      pts.setAttribute( "value", TestP[j][i/2] );
      pts.setAttribute( "readonly", true );
      var name = "pointInput" + count;
      pts.setAttribute( "id", name );
      pts.size="10"
      cell3.appendChild(pts);
      count++;
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
  for(var i = 0; i < 4+(testCases.length-1)/2; i++)
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

function incQ()
{
  if(incrementQ< size-1)
  {
    incrementQ++;
    page++;
    emptyTables();
    fillquestion();
    
  }
}

function decQ()
{
  if(incrementQ > 0)
  {
    incrementQ--;
    page--;
    emptyTables();
    fillquestion();
  }
}

function changeQ(i)
{
    incrementQ = i;
    page = i+1;
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
function getTotal()
{
  var ptsEarned = 0;
  for(var i = 0; i< size; i++)
  {
    ptsEarned += parseInt(FuncP[i]);
    ptsEarned += parseInt(ConsP[i]);
    ptsEarned += parseInt(ColonP[i]);
    ptsEarned += parseInt(ParamP[i]);
    for(var j = 0; j< TestP[i].length; j++)
    {
      ptsEarned += parseInt(TestP[i][j]);
    }
  }
  
  total = 0;
  for(var i = 0; i< size; i++)
  {
    total += parseInt(responseData.TPoints[i]);
  }
  document.getElementById("total").innerHTML = "Total Points: "+ptsEarned+"/" + total;
}
</script>
</body>
</html>



