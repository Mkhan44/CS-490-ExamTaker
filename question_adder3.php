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
  button.a {
      width: 25%;
      height: 90%;
      background-color: #CC0000;
      color: white;
      font-size : 25px;
      border: 1px solid black;
      border-radius: 15px;
      cursor: pointer;
    }
    button.b {
      width: 184px;
      height: 32px;
      background-color: #CC0000;
      color: white;
      font-size : 15px;
      border: 1px solid black;
      border-radius: 15px;
      cursor: pointer;
      display: inline-block;
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
</style>
</head>
  <body>
  
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p style="margin-top: 10px;">Question Added</p>
  </div>

</div>
<div id="question" class="col"> 
  <p>Question:</p>
  <textarea rows="4" cols="50" name="prompt" id="prompt"/></textarea><br>
  Function Name: <input id="fucntionName" name="fucntionName"><br>
  type:<select id="type">
  <option value="No Preference">No Preference</option>
  <option value="Math">Math</option>
  <option value="Conditionals">Conditionals</option>
  <option value="Strings">Strings</option>
  </select>
  <br>
  difficulty:<select id="difficulty">
  <option value="Easy">Easy</option>
  <option value="Medium">Medium</option>
  <option value="Hard">Hard</option>
  </select>
  <br>
  constraint:<select id="constraint">
  <option value="">None</option>
  <option value="For">For</option>
  <option value="While">While</option>
  <option value="Print">Print</option>
  </select>
  <br>
  Parameters:<input id="fucntionParameters" name="fucntionParameters"><br>
  <button class="b" onclick="addTestCase()">Add Test Case</button> 
  <button class="b" onclick="removeTestCase()">Remove Test Case</button> 
  <table id="testCases" align="center">
  <tr>
    <td>
    Test Case 1:
    </td>
    <td>
      input:<input id="input0" name="input0" size="10"> 
    </td>
    <td>
      output:<input id="output0" name="output0" size="10">
    </td>
  </tr>
  <tr>
    <td>
    Test Case 2:
    </td>
    <td>
    input:<input id="input1" name="input1" size="10">
    </td>
    <td>
    output:<input id="output1" name="output1" size="10">
    </td>
  </tr>
</table>
  <br><br><br>
  
  <button onclick="sendQuestion()">submit question</button>
  <br><br>
  <form action="home.php" method="post">
  <button type="submit" value="submit">back</button>
  </form>
</div>

<div class="col"> 
  <p>search by</p>
  type:<select id="QBtype">
  <option value="No Preference">No Preference</option>
  <option value="Math">Math</option>
  <option value="Conditionals">Conditionals</option>
  <option value="Strings">Strings</option>
  </select>
  <br>
  difficulty:<select id="QBdifficulty">
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
       <th>Constraints</th>
     </tr>
  </table>
  <p id="numOfQuestions"></p>
</div>
  
<script language="javascript">
var question_content_list = [];
var question_type_list = [];
var question_difficulty_list = [];
var testCaseNum = 2;
var question_ID_list = [];
var responseData;                                   //to be filled with fetch
var size = 0;

var test;
var test2;
function sendQuestion(){
  var prompt = document.getElementById("prompt").value; 
  var type = document.getElementById("type").value;
  var difficulty = document.getElementById("difficulty").value;
  var Func = document.getElementById("fucntionName").value;
  var Parameters = document.getElementById("fucntionParameters").value;
  var Cons = document.getElementById("constraint").value;

  var Cases = [];
  var Inputs = [];
  var Outputs = [];
  
  for(var i = 0; i<testCaseNum; i++)
  {
    var input = "input" + i;
    var output = "output" + i;
    Cases.push([document.getElementById(input).value, document.getElementById(output).value]);
    Inputs.push(document.getElementById(input).value.replace(/,/g, "~"));
    Outputs.push(document.getElementById(output).value.replace(/,/g, "~"));
  }
  
  for(var i = 0; i< Cases.length; i++)
  {
    Cases[i][0] = Cases[i][0].replace(/,/g, "~");
    Cases[i][1] = Cases[i][1].replace(/,/g, "~");
  }
  
  for(var i = 0; i< Inputs.length; i++)
  {
    Inputs[i] = Inputs[i].replace(/,/g, "~");
    Outputs[i] = Outputs[i].replace(/,/g, "~");
  }
  
  var questionData = new FormData();
  questionData.append('ACTION', "ADD_QUESTION");
  questionData.append('Question', prompt);
  questionData.append('Func', Func);
  questionData.append('Type', type);
  questionData.append('Difficulty', difficulty);
  questionData.append('Parameters', Parameters);
  questionData.append('Cases', Cases);
  questionData.append('Outputs', Outputs);
  questionData.append('Inputs', Inputs);
  questionData.append('Cons', Cons);
  test = questionData;
  test2 = Cases;
  const url = 'https://web.njit.edu/~md535/beta/frontEnd.php';
  
  const options = {
    method: 'POST'
    ,body: questionData
  };
  
  fetch(url, options).then(dataWrappedByPromise => dataWrappedByPromise.json()).then(data => {
    console.log(data)
    searchQuestion();
    showStatus();
});
}
searchQuestion();
function searchQuestion(){
  var type = document.getElementById("QBtype").value;
  var difficulty = document.getElementById("QBdifficulty").value;
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



function addTestCase() {
  if(testCaseNum <6)
  {
    var table = document.getElementById("testCases");
    var row = table.insertRow(testCaseNum);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    cell1.innerHTML = "Test Case " + (testCaseNum+1) + ":";
    
    var test = document.createElement("input");
    test.id = "input" + testCaseNum;
    test.name = "input" + testCaseNum;
    test.size = 10;
    cell2.innerHTML = "input:";
    cell2.appendChild(test);
    
    var out = document.createElement("input");
    out.id = "output" + testCaseNum;
    out.name = "output" + testCaseNum;
    out.size = 10;
    cell3.innerHTML = "output:"; 
    cell3.appendChild(out);
    testCaseNum++;
  }
}

function removeTestCase(){
  if(testCaseNum > 2)
  {
    var table = document.getElementById("testCases").children[0];
    table.removeChild(table.lastElementChild);
    testCaseNum--;
  }
}

function fillTables(j) {
  emptyTables();
  var table = document.getElementById("QuestionBank");
  for(var i = 0; i < j; i++)
  {
    var table = document.getElementById("QuestionBank");
    var row = table.insertRow(1);
    var QID = row.insertCell(0);
    var Question = row.insertCell(1);
    var Type = row.insertCell(2);
    var Difficulty = row.insertCell(3);
    var cons =  row.insertCell(4);
    
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
