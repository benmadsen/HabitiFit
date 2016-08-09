<?php
require_once(__DIR__ . '/habitica/habitica_api.php');

$params = $_GET['data_params'];
$action = $_GET['action'];
$user_id = $params['user_id'];
$api_tok = $params['api_tok'];

$habit = new Habitica($user_id, $api_tok);

if($action == 'change_habit'){
    $dir = $params['direction'];
    $taskName = $params['task_name'];
    $task_id = $habit->getTaskId($taskName);
    
    if(($task_id != 'No task found with that name') && ($dir == 'up' || $dir == 'down')){
        $params = array('taskId'=>$task_id,'direction'=>$dir);
        $rval = $habit->taskScoring($params);
        echo json_encode($rval);
    }else{
        echo 'ERROR';   
    }
}else if($action == 'get_stats'){
    echo json_encode($habit->userStats());
}
?>