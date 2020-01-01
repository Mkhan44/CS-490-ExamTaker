<html>
<head>
<style>

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
<div id="question" class="col"> 
  <p>Question:</p>
  <textarea rows="4" cols="50" name="prompt" id="prompt"/></textarea><br>
  Function Name: <input id="fucntionName" name="fucntionName"><br>
  type:<select id="type">
  <option value="Arrays">Arrays</option>
  <option value="Conditionals">Conditionals</option>
  <option value="Loops">Loops</option>
  <option value="Recursion">Recursion</option>
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
  <button onclick="addTestCase()">Add Test Case</button> 
  <table id="testCases">
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
  
  
  <button onclick="sendQuestion()">submit question</button>
  <p id="submitStatus"></p>
  <p id="question_status"></p>
  
  <form action="home.php" method="post">
  <button type="submit" value="submit">back</button>
  </form>
</div>

<div class="col"> 
  <p>search by</p>
  type:<select id="QBtype">
  <option value="No Preference">No Preference</option>
  <option value="Arrays">Arrays</option>
  <option value="Conditionals">Conditionals</option>
  <option value="Loops">Loops</option>
  <option value="Recursion">Recursion</option>
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
  
  <p id="q1"></p>
  <p id="qt1"></p>
  <p id="qd1"></p>
  <p hidden id="qID1" ></p>
  <hr> 
  <p id="q2"></p>
  <p id="qt2"></p>
  <p id="qd2"></p>
  <p hidden id="qID2" ></p>
  <hr> 
  <p id="q3"></p>
  <p id="qt3"></p>
  <p id="qd3"></p>
  <p hidden id="qID3" ></p>
  <hr> 
  <br>
  <button class="a" onclick="decQ()"><<</button>
  <button class="a" onclick="incQ()">>></button> 
  <p id="page"></p>
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
var incrementQ = 0;  
var page = 1;

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
    document.getElementById("submitStatus").innerHTML = "Question Submitted";
    searchQuestion();
});
}
searchQuestion();
function searchQuestion(){
  var type = document.getElementById("QBtype").value;
  var difficulty = document.getElementById("QBdifficulty").value;
  var Keyword = document.getElementById("Keyword").value;
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
    }
    else
    {
      size = responseData.length;
    }

    incrementQ =0;
    page = 1;
    fillquestions();
    document.getElementById("page").innerHTML = "page " + page + " of " + Math.ceil(size/3);
});
}

function fillquestions()
{
  if(incrementQ < size)
  {
      document.getElementById("q1").innerHTML = "question: " + responseData[incrementQ].QuestionText;
      document.getElementById("qt1").innerHTML = "tag: " + responseData[incrementQ].QuestionType;
      document.getElementById("qd1").innerHTML = "difficulty: " + responseData[incrementQ].QuestionDifficulty;
      document.getElementById("qID1").innerHTML = responseData[incrementQ].QuestionID;
  }
  else
  {
      document.getElementById("q1").innerHTML = "question: ";
      document.getElementById("qt1").innerHTML = "tag: ";
      document.getElementById("qd1").innerHTML = "difficulty: ";
      document.getElementById("qID1").innerHTML = "";
  }
  
  if(incrementQ + 1 < size)
  {
      document.getElementById("q2").innerHTML = "question: " + responseData[incrementQ+1].QuestionText;
      document.getElementById("qt2").innerHTML = "tag: " + responseData[incrementQ+1].QuestionType;
      document.getElementById("qd2").innerHTML = "difficulty: " + responseData[incrementQ+1].QuestionDifficulty;
      document.getElementById("qID2").innerHTML = responseData[incrementQ+1].QuestionID;
  }
  else
  {
      document.getElementById("q2").innerHTML = "question: ";
      document.getElementById("qt2").innerHTML = "tag: ";
      document.getElementById("qd2").innerHTML = "difficulty: ";
      document.getElementById("qID2").innerHTML = "";
  }
  
  if(incrementQ + 2 < size)
  {
      document.getElementById("q3").innerHTML = "question: " + responseData[incrementQ+2].QuestionText;
      document.getElementById("qt3").innerHTML = "tag: " + responseData[incrementQ+2].QuestionType;
      document.getElementById("qd3").innerHTML = "difficulty: " + responseData[incrementQ+2].QuestionDifficulty;
      document.getElementById("qID3").innerHTML = responseData[incrementQ+2].QuestionID;
  }
  else
  {
      document.getElementById("q3").innerHTML = "question: ";
      document.getElementById("qt3").innerHTML = "tag: ";
      document.getElementById("qd3").innerHTML = "difficulty: ";
      document.getElementById("qID3").innerHTML = "";
  }
  document.getElementById("page").innerHTML = "page " + page + " of " + Math.ceil(size/3);
  document.getElementById("numOfQuestions").innerHTML = "Questions found: " + size;
}

function incQ()
{
  if(incrementQ + 3 < size)
  {
    incrementQ = incrementQ + 3;
    page++;
    fillquestions();
  }
}

function decQ()
{
  if(incrementQ > 0)
  {
    incrementQ = incrementQ - 3;
    page--;
    fillquestions();
  }
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

/*$("#question").on('keydown', '#prompt', function(e) { 
  var keyCode = e.keyCode || e.which; 

  if (keyCode == 9) { 
    e.preventDefault(); 
    // call custom function here
  } 
});*/

</script>
  </body>
</html>
