<?php 
    session_start();
  $test = $_POST['eID'];
?>


  
<html>
  <head>
    <title>
    Exam Taker
    </title>
    <style>
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
    <p id="q1"></p>
    <p id="points"></p>
    answer:<br><textarea rows="20" cols="100" name="answer" id="answer" onkeydown="insertTab(this, event);"/></textarea>
    <p hidden id="qID1" ></p>
    <br>
    <button class="inc" onclick="decQ()"><<</button>
    <button class="inc" onclick="incQ()">>></button> 
    <p id="numOfQuestions"></p>
    <button onclick="submitExam()">Submit Exam</button> 
    </div> 

<script language="javascript">


var question_content_list = [];
var answer_content_list = [];

var question_ID_list = [];


var responseData;                                   //to be filled with fetch

var size;
var examSize = 0;

var incrementQ = 0;

var page = 1;
var examPage = 1;

function getQuestions(){
  var examName = document.getElementById("examName").innerHTML;
  
  var formData = new FormData();
  formData.append('ACTION', "GET_EXAM_QUESTIONS");
    formData.append('EXAM_NAME', examName); 
  
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
      answer_content_list[i] = "";
    }
    fillquestion();
    fillList();
    document.getElementById("numOfQuestions").innerHTML = "question " + page + " of " + size;
    
});
}
getQuestions();

function submitExam()
{
  saveAnswerContent();
  var userName = document.getElementById("ucid").innerHTML; 
  var examName = document.getElementById("examName").innerHTML;
  
  var answers = answer_content_list;
  
  for(var i = 0; i< answers.length; i++)
  {
    answers[i] = answers[i].replace(/,/g, "~");
  }
  var formData = new FormData();
  formData.append('ACTION', 'SUBMIT_EXAM');
  formData.append('UCID', userName);
  formData.append('EXAM_NAME', examName);
  formData.append('ANSWERS', answers);
  formData.append('IDs', responseData.QuestionID);
  
  const url = 'https://web.njit.edu/~md535/beta/frontEnd.php';
  
  const options = {
    method: 'POST'
    ,body: formData
  };
  
  fetch(url, options).then(dataWrappedByPromise => dataWrappedByPromise.json()).then(data => {
    console.log(data)
    location.replace("https://web.njit.edu/~md535/beta/home.php");
  });
}

function fillquestion()
{
  if(incrementQ < size)
  {
      document.getElementById("q1").innerHTML = "question: " + responseData.QuestionText[incrementQ];
      document.getElementById("points").innerHTML = "points: " + responseData.Points[incrementQ];
      document.getElementById("numOfQuestions").innerHTML = "question " + page + " of " + size;
  }
}

//////////////////////////////////////////////////////////////////////////////////////
function incQ()
{
  if(incrementQ< size-1)
  {
    saveAnswerContent();
    incrementQ++;
    document.getElementById("answer").value = answer_content_list[incrementQ];
    page++;
    fillquestion();
  }
}

function decQ()
{
  if(incrementQ > 0)
  {
    saveAnswerContent();
    incrementQ--;
    document.getElementById("answer").value = answer_content_list[incrementQ];
    page--;
    fillquestion();
  }
}

function changeQ(i)
{
    saveAnswerContent();
    incrementQ = i;
    document.getElementById("answer").value = answer_content_list[incrementQ];
    page = i+1;
    fillquestion();
}

function saveAnswerContent()
{
  answer_content_list[incrementQ] = document.getElementById("answer").value;
}

function fillList()
{
  var questionList = document.getElementById("sideList");
  for(var i = 0; i < responseData.QuestionID.length; i++)
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

var textareas = document.getElementsByTagName('answer');
var count = textareas.length;
for(var i=0;i<count;i++){
    textareas[i].onkeydown = function(e){
        if(e.keyCode==9 || e.which==9){
            e.preventDefault();
            var s = this.selectionStart;
            this.value = this.value.substring(0,this.selectionStart) + "\t" + this.value.substring(this.selectionEnd);
            this.selectionEnd = s+1; 
        }
    }
}

function insertTab(object, event)
{		
	var keyCode = event.keyCode ? event.keyCode : event.charCode ? event.charCode : event.which;
	if (keyCode == 9 && !event.shiftKey && !event.ctrlKey && !event.altKey)
	{
		var os = object.scrollTop;
		if (object.setSelectionRange)
		{
			var ss = object.selectionStart;	
			var se = object.selectionEnd;
			object.value = object.value.substring(0, ss) + "\t" + object.value.substr(se);
			object.setSelectionRange(ss + 1, ss + 1);
			object.focus();
		}
		else if (object.createTextRange)
		{
			document.selection.createRange().text = "\t";
			event.returnValue = false;
		}
		object.scrollTop = os;
		if (event.preventDefault)
		{
			event.preventDefault();
		}
		return false;
	}
	return true;
}

</script>
</body>
</html>



