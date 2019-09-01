<?php

header('content-type: application/json');
header("Access-Control-Allow-Origin: *");
ini_set('max_execution_time', 0);

require_once './fix-required.php';
if(filter_input(INPUT_GET, 'q')){


    $ufrj = startUFRJ(1);
    $ufpe = startUfpe();

    echo json_encode(array_merge($ufrj, $ufpe));
    
}else if(filter_input(INPUT_GET,'controller')&& filter_input(INPUT_GET, 'id')){
   
    startUFRJ(2);

}else{
    echo json_encode(array('message'=>'missing parameters'));

}
