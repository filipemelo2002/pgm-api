<?php

header('content-type: application/json');
ini_set('max_execution_time', 0);
require './list-module.php';

if(filter_input(INPUT_GET, 'q')){
    requestListData(filter_input(INPUT_GET, 'q'));
}else{
    echo json_encode(array('message'=>'missing parameters'));

}
