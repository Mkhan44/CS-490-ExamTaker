<?php
session_start();
$ACTION = $_POST['ACTION'];
$post_data->ACTION = $ACTION;

switch ($ACTION) {
    case "LOGIN":
        $_SESSION["user"] = $_POST['ucid'];
        $post_data->UCID = $_POST['ucid'];
        $post_data->Password = $_POST['pw'];
        break;
    case "SEARCH_EXAMS_BY_EXAM_NAME":
        $post_data->Name = $_POST['EXAM_NAME'];
        break;
    case "SEARCH_EXAMS_BY_USER":
        $post_data->Ucid = $_POST['UCID'];
        break;
    case "ADD_QUESTION":
        $post_data->Question = $_POST['Question'];
        $post_data->Func = $_POST['Func'];
        $post_data->Type = $_POST['Type'];
        $post_data->Difficulty = $_POST['Difficulty'];
        $post_data->Parameters = $_POST['Parameters'];
        $post_data->Cases = $_POST['Cases'];
        $post_data->Inputs = $_POST['Inputs'];
        $post_data->Outputs = $_POST['Outputs'];
        $post_data->Cons = $_POST['Cons'];
        break;
    case "SEARCH_QUESTIONS":
        $post_data->Type = $_POST['Type'];
        $post_data->Difficulty = $_POST['Difficulty'];
        $post_data->Keyword = $_POST['Keyword'];
        $post_data->Cons = $_POST['Cons'];
        break;
    case "ADD_EXAM":
        $post_data->Name = $_POST['EXAM_NAME'];
        $post_data->IDs = $_POST['QUESTION_IDS'];
        $post_data->Points = $_POST['POINTS'];
        break;
    case "SEARCH_EXAM":
        $post_data->Name = $_POST['EXAM_NAME'];
        break;
    case "GET_EXAM_QUESTIONS":
        $post_data->Name = $_POST['EXAM_NAME'];
        break;
    case "GET_EXAM_ANSWERS":
        $post_data->Name = $_POST['EXAM_NAME'];
        $post_data->Ucid = $_POST['UCID'];
        break;
    case "SUBMIT_EXAM":
        $post_data->Name = $_POST['EXAM_NAME'];
        $post_data->Ucid = $_POST['UCID'];
        $post_data->Answers = $_POST['ANSWERS'];
        $post_data->IDs = $_POST['IDs'];
        break;
    case "TEACHER_SUBMIT_EXAM":
        $post_data->Name = $_POST['EXAM_NAME'];
        $post_data->Ucid = $_POST['UCID'];
        $post_data->Comments = $_POST['COMMENTS'];
        $post_data->TestP = $_POST['TestP'];
        $post_data->TotP = $_POST['TotP'];
        $post_data->FuncP = $_POST['FuncP'];
        $post_data->ConsP = $_POST['ConsP'];
        $post_data->ParamP = $_POST['ParamP'];
        $post_data->ColonP = $_POST['ColonP'];
        break;
        
    case "RELEASE_EXAM":
        $post_data->Name = $_POST['EXAM_NAME'];
        break;
    default:
        echo "ACTION NOT LISTED";
}
$myJSON = json_encode($post_data);

//Set options for curl
$curl = curl_init('https://web.njit.edu/~mrk38/middleEnd.php');
//$curl = curl_init('https://web.njit.edu/~mrk38/BackendDB.php');
curl_setopt($curl, CURLOPT_SLL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLINFO_HEADER_OUT, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);

//save result of curl execution to a variable
$result = curl_exec($curl);

curl_close($curl);

$responseJSON = json_decode($result,true);
echo $result;

switch ($ACTION) {
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    case "LOGIN":
        if ($responseJSON['LOCAL'])
        { 
          if($responseJSON["ROLE"])
          {
            $_SESSION["ROLE"] = 1;
          }
          else
          {
            $_SESSION["ROLE"] = 0;
          }
          header('Location: https://web.njit.edu/~md535/beta/home.php');
          
        }
        else
        { 
          header('Location: https://web.njit.edu/~md535/beta/login.html');
        }
        break;

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    default:
}


?>