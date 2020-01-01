<?php
echo "question select";
?>

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
}
</style>
</head>
  <body>
  
<div class="col"> 
  <p>search by</p>
  type:<select id="type">
  <option value="No Preference">No Preference</option>
  <option value="Arrays">Arrays</option>
  <option value="Conditionals">Conditionals</option>
  <option value="Loops">Loops</option>
  <option value="Recursion">Recursion</option>
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
  
  <button onclick="searchQuestion()">search</button>
  <hr> 
  <p id="q1"></p>
  <p id="qt1"></p>
  <p id="qd1"></p>
  <p hidden id="qID1" ></p>
  <button onclick="addq1()">add</button>
  <hr> 
  <p id="q2"></p>
  <p id="qt2"></p>
  <p id="qd2"></p>
  <p hidden id="qID2" ></p>
  <button onclick="addq2()">add</button>
  <hr> 
  <p id="q3"></p>
  <p id="qt3"></p>
  <p id="qd3"></p>
  <p hidden id="qID3" ></p>
  <button onclick="addq3()">add</button>
  <hr> 
  <br>
  <button class="a" onclick="decQ()"><<</button>
  <button class="a" onclick="incQ()">>></button> 
  <p id="page"></p>
  <p id="numOfQuestions"></p>
  
  <form action="home.php" method="post">
  <button type="submit" value="submit">back</button>
  </form>
</div>

<div class="col">                        <!-- EXAM PORTION -->
Exam Name:<input type="text" id="name"><br>
<hr> 
<p id="eq1"></p>
  <p id="eqt1"></p>
  <p id="eqd1"></p>
  points:<input type="text" id="eqp1">
  <hr> 
  <p id="eq2"></p>
  <p id="eqt2"></p>
  <p id="eqd2"></p>
  points:<input type="text" id="eqp2">
  <hr> 
  <p id="eq3"></p>
  <p id="eqt3"></p>
  <p id="eqd3"></p>
  points:<input type="text" id="eqp3">
  <hr> 
  <br>
  <button class="a" onclick="decE()"><<</button>
  <button class="a" onclick="incE()">>></button> 
  <p id="examPage"></p>
  <p id="numOfExamsQuestions"></p>
  <button onclick="sendExam()">Submit Exam</button>
</div>

<script language="javascript">
  var question_content_list = [];
  var question_type_list = [];
  var question_difficulty_list = [];
  
  var question_ID_list = [];
  
  var point_list = [];
  //point_list[0] = "";
  
  var responseData;                                   //to be filled with fetch
  
  var size = 0;
  var examSize = 0;
  
  var incrementQ = 0;
  var incrementE = 0;
  
  var page = 1;
  var examPage = 1;

function searchQuestion(){
  var type = document.getElementById("type").value;
  var difficulty = document.getElementById("difficulty").value;
  
  var formData = new FormData();
  formData.append('ACTION', "SEARCH_QUESTIONS");
  formData.append('Type', type);
  formData.append('Difficulty', difficulty);
  
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

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function fillExamQuestions()
{
  if(incrementE < examSize)
  {
      document.getElementById("eq1").innerHTML = question_content_list[incrementE];
      document.getElementById("eqt1").innerHTML = question_type_list[incrementE];
      document.getElementById("eqd1").innerHTML = question_difficulty_list[incrementE];
      document.getElementById("eqp1").value = point_list[incrementE];
  }
  else
  {
      document.getElementById("eq1").innerHTML = "question: ";
      document.getElementById("eqt1").innerHTML = "tag: ";
      document.getElementById("eqd1").innerHTML = "difficulty: ";
      document.getElementById("eqp1").value = "";
  }
  
  if(incrementE + 1 < examSize)
  {
      document.getElementById("eq2").innerHTML = question_content_list[incrementE+1];
      document.getElementById("eqt2").innerHTML = question_type_list[incrementE+1];
      document.getElementById("eqd2").innerHTML = question_difficulty_list[incrementE+1];
      document.getElementById("eqp2").value = point_list[incrementE+1];
  }
  else
  {
      document.getElementById("eq2").innerHTML = "question: ";
      document.getElementById("eqt2").innerHTML = "tag: ";
      document.getElementById("eqd2").innerHTML = "difficulty: ";
      document.getElementById("eqp2").value = "";
  }
  
  if(incrementE + 2 < examSize)
  {
      document.getElementById("eq3").innerHTML = question_content_list[incrementE+2];
      document.getElementById("eqt3").innerHTML = question_type_list[incrementE+2];
      document.getElementById("eqd3").innerHTML = question_difficulty_list[incrementE+2];
      document.getElementById("eqp3").value = point_list[incrementE+2];
  }
  else
  {
      document.getElementById("eq3").innerHTML = "question: ";
      document.getElementById("eqt3").innerHTML = "tag: ";
      document.getElementById("eqd3").innerHTML = "difficulty: ";
      document.getElementById("eqp3").value = "";
  }
  document.getElementById("examPage").innerHTML = "page " + examPage + " of " + Math.ceil(examSize/3);
  document.getElementById("numOfExamsQuestions").innerHTML = "Number of Questions On Exam: " + examSize;
}
//////////////////////////////////////////////////////////////////////////////////////
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
/////////////////////////////////////////////////////////////////
function incE()
{
  if(incrementE + 3 < examSize)
  {
    savePointContent();
    incrementE = incrementE + 3;
    examPage++;
    fillExamQuestions();
  }
}

function decE()
{
  if(incrementE > 0)
  {
    savePointContent();
    incrementE = incrementE - 3;
    examPage--;
    fillExamQuestions();
  }
}
///////////////////////////////////////////////////////

function addq1()
{
  if(document.getElementById("qID1").innerHTML)
  {
    question_ID_list.push(document.getElementById("qID1").innerHTML);
    question_content_list.push(document.getElementById("q1").innerHTML);
    question_type_list.push(document.getElementById("qt1").innerHTML);
    question_difficulty_list.push(document.getElementById("qd1").innerHTML);
    examSize++;
    point_list[examSize-1] = "";
    savePointContent();
    fillExamQuestions();
  }
  
}

function addq2(){
  if(document.getElementById("qID2").innerHTML)
  {
    question_ID_list.push(document.getElementById("qID2").innerHTML);
    question_content_list.push(document.getElementById("q2").innerHTML);
    question_type_list.push(document.getElementById("qt2").innerHTML);
    question_difficulty_list.push(document.getElementById("qd2").innerHTML);
    examSize++;
    point_list[examSize-1] = "";
    savePointContent();
    fillExamQuestions();
  }
}

function addq3(){
  if(document.getElementById("qID3").innerHTML)
  {
    question_ID_list.push(document.getElementById("qID3").innerHTML);
    question_content_list.push(document.getElementById("q3").innerHTML);
    question_type_list.push(document.getElementById("qt3").innerHTML);
    question_difficulty_list.push(document.getElementById("qd3").innerHTML);
    examSize++;
    point_list[examSize-1] = "";
    savePointContent();
    fillExamQuestions();
  }
}

function sendExam(){
  savePointContent();
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
    //document.getElementById("page").innerHTML = "page " + page + " of " + Math.ceil(size/3);
});
}

function savePointContent()
{
  if(incrementE < examSize)
  {
    point_list[incrementE] = document.getElementById("eqp1").value;
  }
  if(incrementE + 1 < examSize)
  {
    point_list[incrementE+1] = document.getElementById("eqp2").value;
  }
  if(incrementE + 2 < examSize)
  {
    point_list[incrementE+2] = document.getElementById("eqp3").value;
  }
}

</script>


  
  </body>
</html>