<?php
$FtoM = json_decode(file_get_contents('php://input'), true);//Front end to Middle end
$ACTION = $FtoM['ACTION'];
$post_data->ACTION = $ACTION;

switch ($ACTION) {
    case "LOGIN":
        $post_data->UCID = $FtoM['ucid'];
        $post_data->Password = $FtoM['pw'];
        break;
    case "SEARCH_EXAMS_BY_EXAM_NAME":
        $post_data->Name = $FtoM['EXAM_NAME'];
        break;
    case "SEARCH_EXAMS_BY_USER":
        $post_data->Ucid = $FtoM['UCID'];
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
        $post_data->Name = $FtoM['EXAM_NAME'];
        $post_data->IDs = $FtoM['QUESTION_IDS'];
        $post_data->Points = $FtoM['POINTS'];
        break;
    case "SEARCH_EXAM":
        $post_data->Name = $FtoM['EXAM_NAME'];
        break;
    case "GET_EXAM_QUESTIONS":
        $post_data->Name = $FtoM['EXAM_NAME'];
        break;
    case "GET_EXAM_ANSWERS":
        $post_data->Name = $FtoM['EXAM_NAME'];
        $post_data->Ucid = $FtoM['UCID'];
        break;
    case "SUBMIT_EXAM":
        $post_data->Name = $FtoM['EXAM_NAME'];
        $post_data->Ucid = $FtoM['UCID'];
        $post_data->Answers = $FtoM['ANSWERS'];
        $post_data->IDs = $FtoM['IDs'];
        break;
    case "TEACHER_SUBMIT_EXAM":
        $post_data->Name = $FtoM['EXAM_NAME'];
        $post_data->Ucid = $FtoM['UCID'];
        $post_data->Points = $FtoM['POINTS'];
        $post_data->Comments = $FtoM['COMMENTS'];
        break;
    case "RELEASE_EXAM":
        $post_data->Name = $FtoM['EXAM_NAME'];
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
        break;
    case "TEACHER_SUBMIT_EXAM":
        $responseJSON = json_decode($result,true);
        break;
    default:
}







?>