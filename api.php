<?php

header('content-type: application/json');
ini_set('max_execution_time', 0);


if(filter_input(INPUT_GET, 'q')){
    require_once './list-module.php';
    requestListData(filter_input(INPUT_GET, 'q'));
}else if(filter_input(INPUT_GET,'controller')&& filter_input(INPUT_GET, 'id')){
    require_once './search-module.php';
    $controller = filter_input(INPUT_GET,'controller');
    $id = filter_input(INPUT_GET, 'id');
    requestData($controller, $id);
}else{
    echo json_encode(array('message'=>'missing parameters'));

}
