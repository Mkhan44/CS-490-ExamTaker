<html>
<head>
<style>
table, td ,th{
  border: 1px solid black;
  border-collapse: collapse;
}
button {
      width: 90%;
      height: 90%;
      background-color: #CC0000;
      color: white;
      font-size : 25px;
      border: 1px solid black;
      border-radius: 15px;
      cursor: pointer;
    }
   button.b {
      height: 50%;
      background-color: #CC0000;
      color: white;
      font-size : 10px;
      border: 1px solid black;
      border-radius: 0px;
      cursor: pointer;
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
  .col {
    table-layout: fixed;
    display: table-cell;
    padding: 16px;
    border: 1px solid black;
    background-color: #EFEFEF;
    width: 500px;
  }
}
</style>
</head>
  <body>
  
<div class="col"> 
  <p>search by</p>
  type:<select id="type">
  <option value="No Preference">No Preference</option>
  <option value="Math">Math</option>
  <option value="Conditionals">Conditionals</option>
  <option value="Strings">Strings</option>
  </select>
  <br>
  difficulty:<select id="difficulty">
  <option value="No Preference">No Preference</option>
  <option value="Easy">Easy</option>
  <option value="Medium">Medium</option>
  <option value="Hard">Hard</option>
  </select>
  <br>
  constraint:<select id="QBconstraint">
  <option value="">None</option>
  <option value="For">For</option>
  <option value="While">While</option>
  <option value="Print">Print</option>
  </select>
  <br>
  Keyword:<input id="Keyword" name="Keyword" size="10">
  <br>
  <button onclick="searchQuestion()">search</button>
  <table id="QuestionBank">
    <tr>
       <th>QID</th>
       <th>Question</th>
       <th>Type</th>
       <th>Difficulty</th>
       <th>constraints</th>
     </tr>
  </table>
  <hr> 
  <p id="numOfQuestions"></p>
  
  <form action="home.php" method="post">
  <button type="submit" value="submit">back</button>
  </form>
</div>

<div class="col">                        <!-- EXAM PORTION -->
Exam Name:<input type="text" id="name"><br>
<hr> 
<table id="ExamQuestions">
    <tr>
       <th>QID</th>
       <th>Question</th>
       <th>Type</th>
       <th>Difficulty</th>
       <th>constraints</th>
       <th>Points</th>
     </tr>
</table>
  <p id="numOfExamsQuestions"></p>
  <button onclick="sendExam()">Submit Exam</button>
  <p id="submitStatus" ></p>
</div>

<script language="javascript">
  var question_content_list = [];
  var question_type_list = [];
  var question_difficulty_list = [];
  var question_constraint_list = [];
  var question_ID_list = [];
  
  var point_list = [];
  
  var responseData;                                   //to be filled with fetch
  
  var size = 0;
  var examSize = 0;
  

function searchQuestion(){
  var type = document.getElementById("type").value;
  var difficulty = document.getElementById("difficulty").value;
  var Keyword = document.getElementById("Keyword").value;
  var Cons = document.getElementById("QBconstraint").value;
  
  var formData = new FormData();
  formData.append('ACTION', "SEARCH_QUESTIONS");
  formData.append('Type', type);
  formData.append('Difficulty', difficulty);
  formData.append('Keyword', Keyword);
  formData.append('Cons', Cons);
  
  const url = 'https://web.njit.edu/~md535/beta/frontEnd.php';
  
  const options = {
    method: 'POST'
    ,body: formData
  };
  
  fetch(url, options).then(dataWrappedByPromise => dataWrappedByPromise.json()).then(data => {
    console.log(data)
    responseData = data;
    if(responseData == "No Results!")
    {
      size = 0;
      emptyTables();
    }
    else
    {
      size = responseData.length;
      fillTables(size);
      document.getElementById("numOfQuestions").innerHTML = "Questions found: " + size;
    }

});
}

searchQuestion();

///////////////////////////////////////////////////////

function addq(i)
{

  var table = document.getElementById("QuestionBank");
    var rows = table.children[0];
    
    question_ID_list.push(rows.children[i].children[0].innerHTML);
    question_content_list.push(rows.children[i].children[1].innerHTML);
    question_type_list.push(rows.children[i].children[2].innerHTML);
    question_difficulty_list.push(rows.children[i].children[3].innerHTML);
    question_constraint_list.push(rows.children[i].children[4].innerHTML);
    examSize++;
    
    var table = document.getElementById("ExamQuestions");
    var row = table.insertRow(examSize);
    var QID = row.insertCell(0);
    var Question = row.insertCell(1);
    var Type = row.insertCell(2);
    var Difficulty = row.insertCell(3);
    var cons =  row.insertCell(4);
    var test = row.insertCell(5);
    var pts = document.createElement("input");
    pts.id= "pointInput" + examSize-1;
    var name = "pointInput" + (examSize-1);
    pts.setAttribute( "id", name );
    pts.size="10"
    test.appendChild(pts);
    
    QID.innerHTML = question_ID_list[examSize-1];
    Question.innerHTML = question_content_list[examSize-1];
    Type.innerHTML = question_type_list[examSize-1];
    Difficulty.innerHTML = question_difficulty_list[examSize-1];
    if(question_constraint_list[examSize-1] == "")
    {
      cons.innerHTML = "None";
    }
    else
    {
      cons.innerHTML = question_constraint_list[examSize-1];
    }
    
}


function sendExam(){
  for(var i = 0; i < examSize; i++)
  {
    point_list.push(document.getElementById("pointInput" + i).value);
  }
  
  var formData = new FormData();
  formData.append('ACTION', "ADD_EXAM");
  formData.append('EXAM_NAME', document.getElementById("name").value);
  formData.append('QUESTION_IDS', question_ID_list);
  formData.append('POINTS', point_list);
  
  const url = 'https://web.njit.edu/~md535/beta/frontEnd.php';
  
  const options = {
    method: 'POST'
    ,body: formData
  };
  
  fetch(url, options).then(dataWrappedByPromise => dataWrappedByPromise.json()).then(data => {
    console.log(data)
    document.getElementById("submitStatus").innerHTML = "";
    document.getElementById("submitStatus").innerHTML = "Exam Created";
});
}


function fillTables(j) {
  emptyTables();
  var table = document.getElementById("QuestionBank");
  var inv = j-1;
  for(var i = 0; i < j; i++)
  {
    var row = table.insertRow(1);
    var QID = row.insertCell(0);
    var Question = row.insertCell(1);
    var Type = row.insertCell(2);
    var Difficulty = row.insertCell(3);
    var cons =  row.insertCell(4);
    var test = row.insertCell(5);
    var btn = document.createElement("BUTTON");
    //btn.class="b";
    btn.setAttribute( "class", "b" );
    btn.setAttribute( "onClick", "addq("+(inv+1)+")" );
    inv--;
    btn.innerHTML="Add";
    test.appendChild(btn);
    
    QID.innerHTML = responseData[i].QuestionID;
    Question.innerHTML = responseData[i].QuestionText;
    Type.innerHTML = responseData[i].QuestionType;
    Difficulty.innerHTML = responseData[i].QuestionDifficulty;
    if(responseData[i].QuestionConstraint == "")
    {
      cons.innerHTML = "None";
    }
    else
    {
      cons.innerHTML = responseData[i].QuestionConstraint;
    }
  }
}

function emptyTables() {
  
    var table = document.getElementById("QuestionBank");
    var rows = table.children[0];
    var length = rows.children.length;
    for(var j = 1; j < length; j++)
    {
      rows.removeChild(rows.children[1]);
    }
}

</script>


  
  </body>
</html>