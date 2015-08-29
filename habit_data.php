<?php
require_once('habitica/habitica_api.php');

$dir = $_GET['direction'];
$taskName = $_GET['task_name'];
$user_id = $_GET['user_id'];
$api_tok = $_GET['api_tok'];

$habit = new Habitica($user_id, $api_tok);
$task_id = $habit->getTaskId($taskName);

if(($task_id != 'No task found with that name') && ($dir == 'up' || $dir == 'down') ){
    $params = array('taskId'=>$task_id,'direction'=>$dir);
    $rval = $habit->taskScoring($params);
    echo print_r($rval);
}else{
 echo 'ERROR';   
}
?>