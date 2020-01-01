<?php
session_start();
$name = $_SESSION["user"];
if($_SESSION["ROLE"])
{
  $out = <<<EOD
  <p align="center" style="font-size : 20px;">Account Name: $name</p>
  <p align="center" style="font-size : 20px;">Account Type: TEACHER</p>
  <div class="col"> 
    <form action="question_adder3.php" method="post">
      <button class="a" type="submit" value="submit">Create Question<br>
      <img src="pictures/question.png" alt="Create Question" width="144" height="144">
      </button>
    </form>
  </div>
  
  <div class="col"> 
    <form id="examMaker" action="question_selector3.php" method="post">
      <button class="a" type="submit" value="submit">Create Exam<br>
      <img src="pictures/addExam.png" alt="Create Exam" width="144" height="144">
      </button>
    </form>
  </div>
  <div class="col"> 
    <form id="examSelector" action="exam_selector_teacher.php" method="post">
      <button class="a" type="submit" value="submit">Review Exam<br>
      <img src="pictures/review.png" alt="Review Exam" width="144" height="144">
      </button>
    </form>
  </div>
    <form action="frontEnd.php" method="post">
      <button class="b" type="submit" value="submit">Sign Out</button>
    <input type="hidden" id="ACTION" name="ACTION" value="LOGIN">
    </form>
    
EOD;
}
else
{
  $out = <<<EOD
    <p align="center" style="font-size : 20px;">Account Name: $name</p>
    <p align="center" style="font-size : 20px;">Account Type: STUDENT</p>
    <div class="col"> 
      <form action="exam_selector_student.php" method="post">
        <input hidden type="text" id="es1" name="es1" value="1">
      <button class="a" type="submit" value="submit">Take Exam<br>
      <img src="pictures/take exam.png" alt="Review Exam" width="144" height="144">
      </button>
      </form>
    </div>
    <div class="col"> 
      <form action="exam_selector_student.php" method="post">
        <input hidden type="text" id="es2" name="es2" value="0">
      <button class="a" type="submit" value="submit">Review Exam<br>
      <img src="pictures/review.png" alt="Review Exam" width="144" height="144">
      
      </button>
      </form>
    </div>
    
    <form action="frontEnd.php" method="post">
    <button class="b" type="submit" value="submit">Sign Out</button>
    <input type="hidden" id="ACTION" name="ACTION" value="LOGIN">
    </form>
    
EOD;
}

?>

<html>
  <head>
    <title>
    Home
    </title>
    
    <style>
    button.a {
      width: 90%;
      height: 90%;
      background-color: #CC0000;
      color: white;
      font-size : 25px;
      border: 1px solid black;
      border-radius: 15px;
      cursor: pointer;
    }

    button.b{
      width: 50%;
      height: 15%;
      background-color: #CC0000;
      color: white;
      font-size : 25px;
      border: 1px solid black;
      border-radius: 10px;
      cursor: pointer;
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
      background-color: #EFEFEF;
      display: inline-block;
      width: 30%;
      height: 50%;
  }
    </style>
  </head>
  <body>
    <div>
    <br>
    
    <?php echo $out;?>
    
    
    </div>
  </body>
</html>
