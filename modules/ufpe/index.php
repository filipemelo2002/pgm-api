<?php

require_once 'list-module.php';
function startUfpe(){
    $ufpeModule = new UfpeSearchModule();
    $response  = $ufpeModule->requestListData(filter_input(INPUT_GET, 'q'));
    
    return $response;
}

