<?php
	//Mohamed Khan/Mike D.
  //CS 490 1:00 - 2:20 PM Tues/Thurs
  //MIDDLEEND AUTO GRADER
$FtoM = json_decode(file_get_contents('php://input'), true);//Front end to Middle end
$ACTION = $FtoM['ACTION'];
$post_data->ACTION = $ACTION;

switch ($ACTION) {
    case "LOGIN":
        $post_data->UCID = $FtoM['UCID'];
        $post_data->Password = $FtoM['Password'];
        break;
    case "SEARCH_EXAMS_BY_EXAM_NAME":
        $post_data->Name = $FtoM['Name'];
        break;
    case "SEARCH_EXAMS_BY_USER":
        $post_data->Ucid = $FtoM['Ucid'];
        break;
    case "ADD_QUESTION":
        $post_data->Question = $FtoM['Question'];
        $post_data->Func = $FtoM['Func'];
        $post_data->Type = $FtoM['Type'];
        $post_data->Difficulty = $FtoM['Difficulty'];
        $post_data->Parameters = $FtoM['Parameters'];
        $post_data->Cases = $FtoM['Cases'];
        $post_data->Inputs = $FtoM['Inputs'];
        $post_data->Outputs = $FtoM['Outputs'];
        $post_data->Cons = $FtoM['Cons'];
        break;
    case "SEARCH_QUESTIONS":
        $post_data->Type = $FtoM['Type'];
        $post_data->Difficulty = $FtoM['Difficulty'];
        $post_data->Keyword = $FtoM['Keyword'];
        $post_data->Cons = $FtoM['Cons'];
        break;
    case "ADD_EXAM":
        $post_data->Name = $FtoM['Name'];
        $post_data->IDs = $FtoM['IDs'];
        $post_data->Points = $FtoM['Points'];
        break;
    case "SEARCH_EXAM":
        $post_data->Name = $FtoM['Name'];
        break;
    case "GET_EXAM_QUESTIONS":
        $post_data->Name = $FtoM['Name'];
        break;
    case "GET_EXAM_ANSWERS":
        $post_data->Name = $FtoM['Name'];
        $post_data->Ucid = $FtoM['Ucid'];
        
        break;
    case "SUBMIT_EXAM":
        $post_data->Name = $FtoM['Name'];
        $post_data->Ucid = $FtoM['Ucid'];
        $post_data->Answers = $FtoM['Answers'];
        $post_data->IDs = $FtoM['IDs'];
        break;
    case "TEACHER_SUBMIT_EXAM":
        $post_data->Name = $FtoM['Name'];
        $post_data->Ucid = $FtoM['Ucid'];
        $post_data->TotP = $FtoM['TotP'];
        $post_data->Comments = $FtoM['Comments'];
        $post_data->FuncP = $FtoM['FuncP'];
        $post_data->ParamP = $FtoM['ParamP'];
        $post_data->ConsP = $FtoM['ConsP'];
        $post_data->ColonP = $FtoM['ColonP'];
        $post_data->TestP = $FtoM['TestP'];
        break;
    case "RELEASE_EXAM":
        $post_data->Name = $FtoM['Name'];
        break;
    default:
        echo "ACTION NOT LISTED";
}
$myJSON = json_encode($post_data);

//Set options for curl
$curl = curl_init('https://web.njit.edu/~mrk38/BackendDB.php');
curl_setopt($curl, CURLOPT_SLL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLINFO_HEADER_OUT, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);

//save result of curl execution to a variable
$result = curl_exec($curl);

curl_close($curl);

echo $result;

switch ($ACTION) {
    case "SUBMIT_EXAM":

        $responseJSON = json_decode($result,true);
        $answer = $responseJSON['Answer'];
        $Inputs = $responseJSON['Inputs'];
        $Outputs = $responseJSON['Outputs'];
        $Parameters = $responseJSON['Parameters'];
        $FuncName = $responseJSON['FuncName'];
        $Cons = $responseJSON['Cons'];
        $Points = $responseJSON['Points'];
        $UCID = $responseJSON['UCID'];
        $QIDs = $responseJSON['QIDs'];
        $ExamID = $responseJSON['ExamID'];
        
        
         // comments for backend to receive
        $SendToBack->defComment = array();
        $SendToBack->FuncNameComment = array();
        $SendToBack->ParametersComment = array();
        $SendToBack->TestCaseComments = array();
        $SendToBack->ConsComment = array();
        $SendToBack->PointsObtained = array();
        $SendToBack->ColonComments = array();
        $SendToBack->UCID = $UCID;
        $SendToBack->QIDs = $QIDs;
        $SendToBack->FuncPoints = array();
        $SendToBack->ColonPoints = array();
        $SendToBack->ConsPoints = array();
        $SendToBack->ParamPoints = array();
        $SendToBack->TestCasePoints = array();
        $SendToBack->ACTION = 'STORE_GRADES';
       
        
        //START THE FOR LOOP FOR ALL QUESTION GRADING HERE!
        $numQuest = count($QIDs);
     
     for($k = 0; $k<$numQuest; $k++) {
        $funcP = 5;
        $colonP = 3;
        $ConsP = 1;
        $ParamP = 1;
        
        //Testing for Def here.  
        $verify_def = 'def';
        
        $ans = ltrim($answer[$k]);
        $theWord = preg_split("/\s+|\(|:/", $ans);
        $def = $theWord[0];
        $defCom = "";
        $funcCom = "";
        $colCom = "";
        $consCom = "";
        $paramCom = "";
        $testCom = "";
        
        if (strcmp($def, $verify_def) !== 0)
        {
          $defCom = "Function does not start with def. Question submitted in incorrect format. No points.";
          array_push($SendToBack->defComment, $defCom);
          
          $funcP = 0;
          $funcCom = "Def not present. Functionname invalid.";
          array_push($SendToBack->FuncNameComment, $funcCom);
          array_push($SendToBack->FuncPoints, $funcP);
          
          $colonP = 0;
          $colCom = "Def not present. Colons invalid.";
          array_push($SendToBack->ColonComments, $colCom);
          array_push($SendToBack->ColonPoints, $colonP);
          
          $ConsP = 0;
          $consCom = "Def not present. Constraints invalid.";
          array_push($SendToBack->ConsComment, $consCom);
          array_push($SendToBack->ConsPoints, $ConsP);
          
          $ParamP = 0;
          $paramCom = "Def not present. Parameters invalid.";
          array_push($SendToBack->ParametersComment, $paramCom);
          array_push($SendToBack->ParamPoints, $ParamP);
          
          
          $testingIns = $Inputs[$k];
          $tempIn = explode("|", $testingIns);
          $numTests = count($tempIn);
          $testCasePoints = 0;
          $testCaseTotals = array();
          
          for($j = 0; $j < $numTests; $j++) {
            $testCom .= "Def not present. Testcase $j invalid. @";
            array_push($testCaseTotals, $testCasePoints);
          }
            array_push($SendToBack->TestCaseComments, $testCom);
            array_push($SendToBack->TestCasePoints, $testCaseTotals);
          
        }
        else
        {
          $defCom = "";
          array_push($SendToBack->defComment, $defCom);   
          
           //Testing for FunctionName.
           $ansFuncName = $theWord[1];
           $funcCom = "";
           
           if(strcmp($ansFuncName , $FuncName[$k]) !== 0) 
           {
             $funcCom = "Function name is incorrect. Correct name: $FuncName[$k]. You provided $ansFuncName.";
             $funcP = 0;
             $corrFuncAns = str_replace($ansFuncName, $FuncName[$k], $answer[$k]);
             $answer[$k] = $corrFuncAns;
           }
           else
           {
             $funcCom = "Function name is correct.";
             
           }
            array_push($SendToBack->FuncNameComment, $funcCom);
            array_push($SendToBack->FuncPoints, $funcP);
           
           //Testing for param names.  
           
           $splitter = explode(")", $answer[$k]);
           $tempP = $splitter[0];
           $splitter2 = explode("(", $tempP);
           $ansParam = $splitter2[1]; //Param that the student entered.
           
           $ansParam =  preg_replace("/\s/","", $ansParam);
           $actualParam = preg_replace("/\s/","", $Parameters[0]);
           $paramCom = "";
           
           
           if(strcmp($Parameters[$k], $ansParam) == 0) 
           {
             $paramCom = "Parameters are correct.";
           }
           else 
           {
             $paramCom = "Parameters are incorrect. Correct Parameters: $Parameters[0]. You provided $ansParam.";
             $ParamP = 0;
           }
            array_push($SendToBack->ParametersComment, $paramCom);
            array_push($SendToBack->ParamPoints, $ParamP);
          
          //Check for constraints.
          $consCom = "";
          switch($Cons[$k]) {
          case "For":
            if(strpos($answer[$k] , "for") == 0)
            {
              $consCom = "Constraint incorrect. Question requires a for loop.";
              $ConsP = 0;
            }
            else
            {
              $consCom = "Constraint is correct.";
            }
            
          break;
          
          case "While":
          
            if(strpos($answer[$k] , "while") == 0)
              {
                $consCom = "Constraint incorrect. Question requires a while loop.";
                $ConsP = 0;
              }
              else
              {
                $consCom = "Constraint is correct.";
              }
          
          break;
          
          case "Print":
          
           if(strpos($answer[$k] , "print") == 0)
            {
              $consCom = "Constraint incorrect. Question requires the answer to be printed";
              $ConsP = 0;
            }
            else
            {
              $consCom = "Constraint is correct.";
            }
          
          break;
          
          default:
            $consCom = "This question does not have any Constraints.";
          }
          array_push($SendToBack->ConsComment, $consCom);
          array_push($SendToBack->ConsPoints, $ConsP);
          
          //Check for the colon. Replace it if it's not there.
          $colCom = "";
          if(preg_match("/\bdef\b|\bfor\b|\bif\b|\belse\b|\bwhile\b/", $answer[$k])){
          $sep = "\r\n";
          $lineEnd = strtok($answer[$k], $sep);
          $line1 = "";
          while($lineEnd !== false) {
            if(preg_match("/\bdef\b|\bfor\b|\bif\b|\belse\b|\bwhile\b|\belif\b/", $lineEnd)) {
            
                if(preg_match('/:$/', $lineEnd)) {
                  $line1 .= $lineEnd . $sep;
                  $colCom = "Colon is present in the answer.";
                  
                  
                } else { //Here is where we add the colon if it's missing!
                    $line1 .= $lineEnd . ":" . $sep;
                    $colCom = "Missing a colon at the end of the line in your answer.";
                    $colonP = 0;
                  }     
                } 
                
            else  {
                
                  $line1 .= $lineEnd . $sep;
                }
                $lineEnd = strtok($sep);
                
              }
              //Now the student answer has appropriate semi colons and can be written to the file for grading.
              $answer[$k] = $line1;
            }
          array_push($SendToBack->ColonComments, $colCom);
          array_push($SendToBack->ColonPoints, $colonP);
          
          //Do testCase checks here.
          
          $testingIns = $Inputs[$k];
          $tempIn = explode("|", $testingIns);
          $testingOuts = $Outputs[$k];
          $tempOut = explode("|", $testingOuts);
          
          $fileName = 'stuAnswer.py';
          $testCom = "";
          $numTests = count($tempIn);
          $beforeTestCases = $Points[$k] - 10;
          $testCasePoints = round(($beforeTestCases/$numTests)); //Amount that each testCase will be worth for this problem.
          $testCaseTotals = array();
      
          $totalForRound = 0;
          for($j = 0; $j < $numTests; $j++) {
            $currTestCaseNum = ($j+1);
            $testCasePoints = round(($beforeTestCases/$numTests));
            $currentTestCase = $tempIn[$j];
            $currentOutput = $tempOut[$j];
            
            $file = fopen($fileName, 'w');
             if($Cons[$k] == "Print") {
               fwrite($file, $answer[$k] . "\n" . "$FuncName[$k]($currentTestCase)");
             }
             else {
            fwrite($file, $answer[$k] . "\n" . "print($FuncName[$k]($currentTestCase))");
            }
            $progRun = exec("python stuAnswer.py");
            if($progRun == $currentOutput) {
              $testCom .= "Testcase $currTestCaseNum is correct! Input tested was: $currentTestCase @ Output was: $progRun @";
              $totalForRound += $testCasePoints;            
            }
            else
            {
              if($progRun == "") 
              {
                $testCom .= "Testcase $currTestCaseNum is incorrect. Input tested was: $currentTestCase @ expected Output was: $currentOutput there was a runtime error. Actual Output was: $progRun @";
                $testCasePoints = 0;
                $totalForRound += $testCasePoints;
              }
              else
                {
                  $testCom .= "Testcase $currTestCaseNum is incorrect. Input tested was: $currentTestCase @ expected Output was: $currentOutput Actual Output was: $progRun @";
                  $testCasePoints = 0;
                  $totalForRound += $testCasePoints;                
                }
              
            }
            
            
            if($totalForRound > $beforeTestCases)
            {
              $findLeftOver = ($totalForRound - $beforeTestCases);
              $testCasePoints -= $findLeftOver; //This will make it so the final test case is worth just enough to get to the total.                      
            }
            elseif($numTests - $j == 1 && $testCasePoints!= 0)
            {
              $makeSure = round(($beforeTestCases/$numTests));
              $findLeftOver = ($beforeTestCases - $totalForRound);
              if($findLeftOver < $makeSure) {
              $testCasePoints += $findLeftOver; //This will make it so the final test case is bumped up an extra point to give the correct total.
              }
              else{
              $testCasePoints = $makeSure;
	      }
              
            }
            
                                          
            array_push($testCaseTotals, $testCasePoints);
            fclose($file);
            
          }
          
          
          array_push($SendToBack->TestCaseComments, $testCom);
          array_push($SendToBack->TestCasePoints, $testCaseTotals);
            
          }
     //END OF FOR LOOP DON'T REMOVE THIS BRACKET.   
     }
        
        $backJson = json_encode($SendToBack);
        
        $Bcurl = curl_init('https://web.njit.edu/~mrk38/BackendDB.php');
        curl_setopt($Bcurl, CURLOPT_SLL_VERIFYPEER, 0);
        curl_setopt($Bcurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($Bcurl, CURLINFO_HEADER_OUT, true);
        curl_setopt($Bcurl, CURLOPT_POSTFIELDS, $backJson);
        
        
        $result = curl_exec($Bcurl);

        curl_close($Bcurl);
        break;
    case "TEACHER_SUBMIT_EXAM":
        $responseJSON = json_decode($result,true);
        break;
    default:
}

?>