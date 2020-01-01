<?PHP
	//Mohamed Khan
  //CS 490 1:00 - 2:20 PM Tues/Thurs
  //BACKEND TABLE MANIPULATION
	$server = 'sql1.njit.edu';
  $DBuser = 'mrk38';
  $DBPass = '9JPesRPs';
  //Connect to the database (DB).
	$db_hand = mysqli_connect($server , $DBuser , $DBPass);
	
	$database = "mrk38";
	
	$db_found = mysqli_select_db($db_hand, $database);
 
	
	//Grabbing info from the middle-end.

  $JsonVar = json_decode(file_get_contents('php://input'), true);
  
  $ACTION = $JsonVar["ACTION"];
  
  
  //Different commands passed from the middle (originating from front) are handled here.
    switch($ACTION) {
  
    case "ADD_QUESTION":
    
      //Add the actual question and diff + type into the QuestionBank table.
      $Question = $JsonVar["Question"];

      $Difficulty = $JsonVar["Difficulty"];
      $Type = $JsonVar["Type"];          
      $Testcases = $JsonVar["Cases"];
      $tst = explode(',' , $Testcases);
      var_dump($tst);
      $tsth = str_replace("~" , ",", $tst);
      $FunctionName = $JsonVar["Func"];
      $Params = $JsonVar["Parameters"];        
      $Question = $db_hand->real_escape_string($Question);
      $Params = $db_hand->real_escape_string($Params);
      
      $InsertQuery = "INSERT INTO QuestionBank(Question, Difficulty, Type, ExamID, Points) VALUES ('$Question' , '$Difficulty' , '$Type', 0, 0) ";
	
    	$result = mysqli_query($db_hand, $InsertQuery);
 
     //Add the paramaters and stuff into the QVars Table.
        
     $SQuery = "SELECT ID from QuestionBank where Question = '$Question' ";
     $result2 = mysqli_query($db_hand , $SQuery);
     
     $theRow = mysqli_fetch_row($result2);
     $qid = $theRow[0];
     $test1 = $db_hand->real_escape_string($tsth[0]);
     $output1 = $db_hand->real_escape_string($tsth[1]);
     $test2 = $db_hand->real_escape_string($tsth[2]);
     $output2 = $db_hand->real_escape_string($tsth[3]);
     
     $InsertQuery = "INSERT INTO QVars(QuestionID, TestCase1, TestCase2, FuncName, Parameters, Output1, Output2)  VALUES ('$qid' , '$test1', '$test2', '$FunctionName', '$Params', '$output1', '$output2') ";
     
     mysqli_query($db_hand, $InsertQuery);               
     
     
     
          break;
      
      
      //Login system, shows whether or not you are a student or professor.
    case "LOGIN":
      
    	$UCID = $JsonVar["UCID"];
    	$Password = $JsonVar["Password"];
      
    	//Querying the DB so we can check validity of the login.
    	$SearchQuery = "SELECT Password from `CS100` where `UCID` = '$UCID' ";
    	
    	$result = mysqli_query($db_hand, $SearchQuery);
    	$row = mysqli_fetch_row($result);
      $PassDB = $row[0];
     
       //Hashing pass + testing if it matches with what's in the DB.
     	$hashPass = password_hash($Password, PASSWORD_DEFAULT);
      
     	if(password_verify($PassDB, $hashPass)) {
        $resp->LOCAL = 1;
        $message = '1';
    	}
       //If the hashed Password doesn't match what's in the DB, we give a failed login response.
    	else {
        $resp->LOCAL = 0;   
    	}
    
      //If an invalid UCID was input, the database will not have any password to give to PassDB, so we 
      //Also treat this case as a failed login.
    	if ($PassDB == ""){
         $resp->LOCAL = 0;   
    	}
     if ($resp-> LOCAL == 1) {
       $RQ = "SELECT role from `CS100` where `UCID` = '$UCID' ";
       $Res = mysqli_query($db_hand, $RQ);
       $rRow = mysqli_fetch_row($Res);
       $whoAreYou = $rRow[0];
       
       //'p' means the role is the professor! Send 1!
       if($whoAreYou == 'p')
       {
         $resp-> ROLE = 1;
         $TheResp = json_encode($resp);
         echo $TheResp;
       }
       else 
       {
         $resp-> ROLE = 0;
         $TheResp = json_encode($resp);
         echo $TheResp;
       }
     } else {
        $response = json_encode($resp);
        echo $response;
     }
       break;
     
    //Adds exam to the database and has questions corresponding with said exam based on question ID.
    case "ADD_EXAM":
   
  	 $ExamName = $JsonVar["Name"];
     $QIDs = $JsonVar["IDs"];
     $ids = explode(',', $QIDs);
     var_dump($ids);
     
     $theExamName = $db_hand->real_escape_string($ExamName);
     $Points = $JsonVar["Points"];
     $pts = explode(',', $Points);
     var_dump($pts);
     
     $EQuery = "INSERT INTO Exams(ExamName , Released) VALUES ('$theExamName', 0) ";
     
   	 $result = mysqli_query($db_hand, $EQuery);
     
     
     $EQuery = "SELECT ExamID from Exams where ExamName = '$theExamName'";
     $result = mysqli_query($db_hand, $EQuery);
     
     $row = mysqli_fetch_row($result);
     $ExID = $row[0];
     
     
     for($i = 0; $i <= 10; $i++ ) {
         $theID = $ids[$i];
         $CPoints = $pts[$i];
         if(isset($theID)) {
         $EQuery = "UPDATE QuestionBank SET ExamID = '$ExID' where ID = '$theID'";
         mysqli_query($db_hand, $EQuery);
         $PQuery = "UPDATE QuestionBank SET Points = '$CPoints' where ID = '$theID'";
         mysqli_query($db_hand, $PQuery);
         }
         else {
         break;
         }
         
     }   
        break;
        
        //Answers are stored into the DB. Also, passing info to Middle for grading and storing comments.
     case "SUBMIT_EXAM":
       $Answers = $JsonVar["Answers"];
       $ExamName = $JsonVar["Name"];
       $UCID = $JsonVar["Ucid"];
       $QIDs = $JsonVar["IDs"];
       //Find the examID.
       $FindQuery = "SELECT ExamID from Exams where ExamName = '$ExamName'";
       $Fresult = mysqli_query($db_hand, $FindQuery);
       $Frow = mysqli_fetch_row($Fresult);
       $ex = $Frow[0];
       $ans = explode(',' , $Answers);
       $ansH = str_replace("~" , ",", $ans);
       $IDEx = explode(',' , $QIDs);
       $counter = count($ans);
       
       //STUFF TO SEND TO MIDDLEEND FOR GRADING!!!!
     $MiddleStuff->Answer = array();
     $MiddleStuff->Input1 = array();
     $MiddleStuff->Input2 = array();
     $MiddleStuff->Output1 = array();
     $MiddleStuff->Output2 = array();
     $MiddleStuff->Parameters = array();
     $MiddleStuff->FuncName = array();
     $MiddleStuff->Points = array();
    
     
       for($i = 0; $i < $counter ; $i++) {
         $CAns = $ansH[$i];
         $TAns = $db_hand->real_escape_string($CAns);
         $QID = $IDEx[$i];
         array_push($QuestionIDStore->QID, $QID);
         
         $insertQuery = "INSERT INTO Stu_Answers (ExamID , QuestionID , UCID , Answer) VALUES ('$ex' , '$QID' , '$UCID' , '$TAns')";
         mysqli_query($db_hand, $insertQuery);
         //Test case 1 info.
         $testQ1 = "SELECT TestCase1 from QVars where QuestionID = '$QID'";
         $Q1Result = mysqli_query($db_hand, $testQ1);
         $Q1Row = mysqli_fetch_row($Q1Result);
         $Q1 = $Q1Row[0];
         //Test case 2 info.
         $testQ2 = "SELECT TestCase2 from QVars where QuestionID = '$QID'";
         $Q2Result = mysqli_query($db_hand, $testQ2);
         $Q2Row = mysqli_fetch_row($Q2Result);
         $Q2 = $Q2Row[0];
         //Output1 Info.
         $Out1 = "SELECT Output1 from QVars where QuestionID = '$QID'";
         $Out1Result = mysqli_query($db_hand, $Out1);
         $Out1Row = mysqli_fetch_row($Out1Result);
         $O1 = $Out1Row[0];
         //Output2 Info.
         $Out2 = "SELECT Output2 from QVars where QuestionID = '$QID'";
         $Out2Result = mysqli_query($db_hand, $Out2);
         $Out2Row = mysqli_fetch_row($Out2Result);
         $O2 = $Out2Row[0];
         //FuncName Info.
         $FuncNameQ = "SELECT FuncName from QVars where QuestionID = '$QID'";
         $FuncResult = mysqli_query($db_hand, $FuncNameQ);
         $FuncRow = mysqli_fetch_row($FuncResult);
         $FuncName = $FuncRow[0];
         //Parameter info.
         $ParamsQ = "SELECT Parameters from QVars where QuestionID = '$QID'";
         $ParamResult = mysqli_query($db_hand, $ParamsQ);
         $ParamRow = mysqli_fetch_row($ParamResult);
         $Params = $ParamRow[0];
         //Points info.
         $PointsQuery = "SELECT Points from QuestionBank where QuestionID = '$QID'";
         $PointsRes = mysqli_query($db_hand, $PointsQuery);
         $PointsRow = mysqli_fetch_row($PointsRes);
         $PointsM = $PointsRow[0]; 
         //Store stuff into Array for middle.
         array_push($MiddleStuff->Input1, $Q1);
         array_push($MiddleStuff->Input2, $Q2);
         array_push($MiddleStuff->Output1, $O1);
         array_push($MiddleStuff->Output2, $O2);
         array_push($MiddleStuff->FuncName, $FuncName);
         array_push($MiddleStuff->Parameters, $Params);
         array_push($MiddleStuff->Points, $PointsM);
         array_push($MiddleStuff->Answer, $CAns);
               
       }
       
      /* 
     $removeNull = array_pop($MiddleStuff->Input1);
     $removeNull = array_pop($MiddleStuff->Input2);
     $removeNull = array_pop($MiddleStuff->Output1);
     $removeNull = array_pop($MiddleStuff->Output2);
     $removeNull = array_pop($MiddleStuff->FuncName);
     $removeNull = array_pop($MiddleStuff->Parameters);
     $removeNull = array_pop($MiddleStuff->Points);
     $removeNull = array_pop($MiddleStuff->Answer);
         */
         /*
       $file = fopen('hello.txt', 'w');
       //fputcsv($file, $MiddleStuff->Answer);

       fwrite($file, $MiddleStuff->Answer[0]);
       //fputcsv($file, $responseJSON["QuestionText"]);
       fclose($file);
     */
     $MiddleResp = json_encode($MiddleStuff);
     echo $MiddleResp;
     
     //Receive from Middle.
     $middleDecode = json_decode(file_get_contents('php://input'), true);
     $defCom = $middleDecode['defComment'];
     $funcCom = $middleDecode['FuncNameComment'];
     $tstCase1 = $middleDecode['TestCase1Comment'];
     $tstCase2 = $middleDecode['TeastCase2Comment'];
     $pointsRec = $middleDecode['PointsObtained'];
     
     //Gotta get response from the middle here. To be added.
     //Need points , comments for def, funcname, params, each input/output.
     //For loop to store data for each.
     
     for($j = 0; $j < $counter ; $j++) {
       $QID = $IDEx[$j];
       $storeComQuery = "INSERT INTO Comments(QuestionID, UCID, Reasoning, PointsEarned, ProfComments, ExamID) VALUES ('$QID' , '$UCID' , 0, 0, 0, '$ex') ";
       $storeRes = mysqli_query($db_hand, $storeComQuery);
     }
     
       break; 
      
      //Returns a boolean to the middle to say whether or not the Exam can be viewed by students.
     case "RELEASE_EXAM":
     
        $ExamName = $JsonVar["Name"];
        $EQuery = "UPDATE Exams SET Released = 1 where ExamName = '$ExamName'";
        $EResult = mysqli_query($db_hand, $EQuery);
     
       break;  
   
     //Searches through QuestionBank based on filters provided by the user.
     case "SEARCH_QUESTIONS":
     
      $Difficulty = $JsonVar["Difficulty"];
      $Type = $JsonVar["Type"];
      
       if($Difficulty == 'No Preference' && $Type == 'No Preference')
        {
          $sQuery = "SELECT * from QuestionBank";
        }
        elseif($Difficulty == 'No Preference' || $Type == 'No Preference') 
        {
        $sQuery = "SELECT * from QuestionBank where Difficulty = '$Difficulty' || Type = '$Type'";
        }
       else 
       {
        $sQuery = "SELECT * from QuestionBank where Difficulty = '$Difficulty' AND Type = '$Type'";
       }
        $result = mysqli_query($db_hand, $sQuery);
        $numR = mysqli_num_rows($result);
        if($numR == 0) {
          $Msg = "No Results!";
          echo json_encode($Msg);
        }
        else {
       while($row = mysqli_fetch_array($result)) {
          $QID = $row[0];
          $QText = $row[1];
          $QDiff = $row[2];
          $QType = $row[3];
          
          $questionData[] = array ("QuestionID" => $QID, "QuestionText" => $QText, "QuestionDifficulty" =>  $QDiff, "QuestionType" => $QType);
        }   
         echo json_encode($questionData); 
        }               
                              
        break;
   
   //Shows all students, may have to change this to be able to filter.
     case "SEARCH_USERNAME":
         $SQuery = "SELECT * from `CS100` where `role` = 's' ";
         $SResult = mysqli_query($db_hand , $SQuery);
         
         while($row = mysqli_fetch_array($SResult)) {
             $student = $row[0];
             $grd = $row[3];
             $stuData[] = array ("UCID" => $student , "grade" => $grd);
         }
         echo json_encode($stuData);
         break;
         
     //Searching exams, Currently no filter here.
     case "SEARCH_EXAM":
     
         $SQuery = "SELECT * from Exams";
         $SResult = mysqli_query($db_hand , $SQuery);
         
         while($row = mysqli_fetch_array($SResult)) {
             $ExamID = $row[0];
             $ExamName = $row[1];
             $ExData[] = array ("ExamID" => $ExamID , "ExamName" => $ExamName);
         }
         echo json_encode($ExData);
      
         break;
         
     //For when we pull up the questions for the student to see while taking the exam.
     case "GET_EXAM_QUESTIONS":
        
        $ExamName = $JsonVar["Name"];
        $FindQuery = "SELECT ExamID from Exams where ExamName = '$ExamName'";
        $Fresult = mysqli_query($db_hand, $FindQuery);
        $Frow = mysqli_fetch_row($Fresult);
        $ex = $Frow[0];     
         $QResp->QuestionText = array();
         $QResp->QuestionID = array();
         $Quest = 0;
         $QID = 0;
         for($i = 0; $i <= 10; $i++) {
           if(isset($Quest)) {
             $QuestQuery = "SELECT Question FROM QuestionBank WHERE ExamID = '$ex' LIMIT $i,1";
             $Qresult = mysqli_query($db_hand, $QuestQuery);
             $Qrow = mysqli_fetch_row($Qresult);
             $Quest = $Qrow[0];
             array_push($QResp->QuestionText, $Quest);
             $QIDQuery = "SELECT ID FROM QuestionBank WHERE ExamID = '$ex' LIMIT $i , 1";
             $IDresult = mysqli_query($db_hand, $QIDQuery);
             $IDrow = mysqli_fetch_row($IDresult);
             $QID = $IDrow[0];
             array_push($QResp->QuestionID, $QID);
           }
           else {
             break;
           }
         }
         //Take off the last element of the Array that's extra and NULL.
         $removeNull = array_pop($QResp->QuestionText);
         $removeNull2 = array_pop($QResp->QuestionID);
         $theResponse = json_encode($QResp);
         echo $theResponse;
         
         break;
         
         //Returns the exam questions and answers for the Professor/Student to see.
      case "GET_EXAM_ANSWERS":
     
        $ExamName = $JsonVar["Name"];
        $UCID = $JsonVar["Ucid"];
        
        //Also return amount of points + Daquan's comments from the middle end for each question.
        $FindQuery = "SELECT ExamID from Exams where ExamName = '$ExamName'";
        $Fresult = mysqli_query($db_hand, $FindQuery);
        $Frow = mysqli_fetch_row($Fresult);
        $ex = $Frow[0];     
        $QResp->QuestionText = array();
        $QResp->AnswerText = array();
        $QResp->Points = array();
        $QResp->TestCase1 = array();
        $QResp->TestCase2 = array();
        $QResp->MiddleComments = array();
        $QResp->TeacherComments = array();
        $Quest = 0;
        $ans = 0;
        $EQuery = "SELECT ExamID from Exams where ExamName = '$ExamName'";
        $result = mysqli_query($db_hand, $EQuery);
        $IDrow = mysqli_fetch_row($result);
        $ExID = $IDrow[0];
         for($i = 0; $i <= 10; $i++) {
           if(isset($Quest)) {
             $QuestQuery = "SELECT Question FROM QuestionBank WHERE ExamID = '$ex' LIMIT $i,1";
             $Qresult = mysqli_query($db_hand, $QuestQuery);
             $Qrow = mysqli_fetch_row($Qresult);
             $Quest = $Qrow[0];
             array_push($QResp->QuestionText, $Quest);
             $QIDQuery = "SELECT ID FROM QuestionBank WHERE ExamID = '$ex' LIMIT $i, 1";
             $QIDRes = mysqli_query($db_hand, $QIDQuery);
             $QIDRow = mysqli_fetch_row($QIDRes);
             $QID = $QIDRow[0];
             
             //Grabbing test cases to pass back to the front.
             $tst1Query = "SELECT TestCase1 FROM QVars WHERE QuestionID = '$QID'";
             $tst1Res = mysqli_query($db_hand, $tst1Query);
             $tst1Row = mysqli_fetch_row($tst1Res);
             $testCase1 = $tst1Row[0];
             array_push($QResp->TestCase1, $testCase1);
             $tst2Query = "SELECT TestCase2 FROM QVars WHERE QuestionID = '$QID'";
             $tst2Res = mysqli_query($db_hand, $tst2Query);
             $tst2Row = mysqli_fetch_row($tst2Res);
             $testCase2 = $tst2Row[0];
             array_push($QResp->TestCase2, $testCase2);
             
             //Grabbing points
             $pointsQuery = "SELECT PointsEarned from Comments where UCID = '$UCID' AND QuestionID = '$QID'";
             $pointsRes = mysqli_query($db_hand, $pointsQuery);
             $pointsRow = mysqli_fetch_row($pointsRes);
             $pointsRec = $pointsRow[0];
             array_push($QResp->Points, $pointsRec);
             
             //Grab teacher comments.
             $tComQuery = "SELECT ProfComments from Comments where UCID = '$UCID' AND QuestionID = '$QID'";
             $tComRes = mysqli_query($db_hand, $tComQuery);
             $tComRow = mysqli_fetch_row($tComRes);
             $tComs = $tComRow[0];
             array_push($QResp-> TeacherComments, $tComs);
             
             
             //Need to grab Middle Comments here.
             $mQuery = "SELECT Reasoning from Comments where UCID = '$UCID' AND QuestionID = '$QID'";
             $mRes = mysqli_query($db_hand, $mQuery);
             $mRow = mysqli_fetch_row($mRes);
             $middleCom = $mRow[0];
             array_push($QResp-> MiddleComments, $middleCom);
             
             //Grabbing answers
             $AnsQuery = "SELECT Answer FROM Stu_Answers WHERE UCID = '$UCID' AND ExamID = '$ExID'  LIMIT $i, 1";
             $Ansresult = mysqli_query($db_hand, $AnsQuery);
             $Arow = mysqli_fetch_row($Ansresult);
             $ans = $Arow[0];
             array_push($QResp->AnswerText, $ans);
                   
           }
           else {
             break;
           }
         }
         //Take off the last element of the Array that's extra and NULL.
         $removeNull = array_pop($QResp->QuestionText);
         $removeNull2 = array_pop($QResp->AnswerText);
         $removeNull3 = array_pop($QResp->TestCase1);
         $removeNull4 = array_pop($QResp->TestCase2);
         $removeNull5 = array_pop($QResp->Points);
         $removeNull6 = array_pop($QResp->TeacherComments);
         $removeNull7 = array_pop($QResp->MiddleComments);
         $theResponse = json_encode($QResp);
         echo $theResponse;
         
         break;
         
         
     case "SEARCH_EXAMS_BY_EXAM_NAME":
     
        $ExamName = $JsonVar["Name"];  
        $FQuery = "SELECT ExamID from Exams where ExamName = '$ExamName'";
        $FResult = mysqli_query($db_hand, $FQuery);
        $FRow = mysqli_fetch_row($FResult);
        $exID = $FRow[0];
        
        $dupeArray = array();
        $UCID = '0';
        $EResp-> Student = array();
        $EResp-> Grade = array();
        
        for($i = 0; $i <= 30; $i++) {
          if(isset($UCID)) {
          //Use ID to find the student's that have taken it in stu_answers.
          $StuQuery = "SELECT UCID from Stu_Answers where ExamID = '$exID' LIMIT $i, 1";
          $StuResults = mysqli_query($db_hand, $StuQuery);
          $StuRow = mysqli_fetch_row($StuResults);
          $UCID = $StuRow[0];        
          //Check if the UCID is already in the array. If it is, we don't wanna send duplicate UCIDs back. 
          
          if(in_array($UCID, $dupeArray))
          {
           // echo 'UCID is in the array already!';
          }
          else {
            //Filling array with UCID and Grades corresponding to each student who took the exam.
            array_push($EResp-> Student, $UCID);
            $GQuery = "SELECT Grade from CS100 where UCID = '$UCID'";
            $Gresult = mysqli_query($db_hand, $GQuery);
            $GRow = mysqli_fetch_row($Gresult);
            $grade = $GRow[0];
            array_push($EResp-> Grade, $grade); 
            array_push($dupeArray, $UCID); 
          }
          
           }
           
           else {
             break; 
             }
             
         }
        $removeNull = array_pop($EResp->Student);
        $removeNull2 = array_pop($EResp->Grade);
        $exResp = json_encode($EResp);
        echo $exResp;     
      
         break;
      
     case "SEARCH_EXAMS_BY_USER":
      
       $UCID = $JsonVar["Ucid"];
       $released = 0;
       $EResp-> ExamName = array();
       $EResp->Released = array();
       $dupeArray = array();
       $ExID = '0';
       //Find all ExamNames and store into an array, eliminating duplicates. Probably a faster way to do this.
       for($i = 0; $i <= 30; $i++) {
         if(isset($ExID)) {
         $ExIDQ = "SELECT ExamID from Stu_Answers where UCID = '$UCID' LIMIT $i, 1"; 
         $ExIDRes = mysqli_query($db_hand, $ExIDQ);
         $ExIDRow = mysqli_fetch_row($ExIDRes);
         $ExID = $ExIDRow[0];
         $ExNameQ = "SELECT ExamName from Exams where ExamID = '$ExID'";
         $ExNameRes = mysqli_query($db_hand, $ExNameQ);
         $ExNameRow = mysqli_fetch_row($ExNameRes);
         $ExamName = $ExNameRow[0];
         $releaseQ = "SELECT Released from Exams where ExamID = '$ExID'";
         $releaseRes = mysqli_query($db_hand, $releaseQ);
         $releasedRow = mysqli_fetch_row($releaseRes);
         $released = $releasedRow[0];
         
         //Check if the ExamName is in the array. If it is, we don't wanna send duplicate names.
           if(in_array($ExamName , $dupeArray))
           {
             // echo 'ExamName is already in the array!'; 
           }
           else { 
             array_push($EResp->ExamName, $ExamName);
             array_push($EResp->Released, $released);
             array_push($dupeArray, $ExamName);
           }
         }
         else {
           break;
         }  
       }
          
           $removeNull = array_pop($EResp->ExamName);
           $removeNull2 = array_pop($EResp->Released);
           $exResp = json_encode($EResp);
           echo $exResp;
    
         break;  
      
     case "TEACHER_SUBMIT_EXAM":
         $UCID = $JsonVar["Ucid"];
         $ExamName = $JsonVar["Name"];
         $Points = $JsonVar["Points"];
         $pts = explode(',' , $Points);
         $Comments = $JsonVar["Comments"];
         $com = explode(',' , $Comments);
         $comH = str_replace("~" , ",", $com);
         
         
         $findEXID = "SELECT ExamID from Exams where ExamName = '$ExamName'";
         $EXIDresult = mysqli_query($db_hand , $findEXID);
         $EXIDRow = mysqli_fetch_row($EXIDresult);
         $EXID = $EXIDRow[0];
         $QID = 0;
         
         $grdQuery = "SELECT Grade from CS100 where UCID = '$UCID'";
         $grdRes = mysqli_query($db_hand, $grdQuery);
         $grdRow = mysqli_fetch_row($grdRes);
         $grade = $grdRow[0];
         //Found the Exam ID, now we need to find the Question IDs that correspond with that ID.
         for($i = 0; $i<20; $i++) {
           if(isset($QID)) {
             $Rcom = $comH[$i];
             $finalCom = $db_hand->real_escape_string($Rcom);
             $findQID = "SELECT ID from QuestionBank where ExamID = '$EXID' LIMIT $i,1";
             $QIDResult = mysqli_query($db_hand , $findQID);
             $QIDRow = mysqli_fetch_row($QIDResult);
             $QID = $QIDRow[0];
             $UQuery = "UPDATE Comments SET PointsEarned = '$pts[$i]' , ProfComments = '$finalCom' WHERE UCID = '$UCID' AND QuestionID = '$QID'";
             mysqli_query($db_hand, $UQuery);
             $grade = ($grade + $pts[$i]);
           }
           else {
             break;
           }
         }
         
        $grdUpdateQuery = "UPDATE CS100 SET Grade = '$grade' where UCID = '$UCID'";
        $grdResult = mysqli_query($db_hand, $grdUpdateQuery);
             
         break;

     
    default:
       
         $message = 'invalid command !';
         $Disp = json_encode($message);
         echo $Disp;
         
     //LAST LINE OF THE SWITCH STATEMENT, DON'T EDIT!!!
     } 
  
    //Free up some resources.
   mysqli_close($db_hand);
?>