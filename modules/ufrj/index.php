<?php

require_once 'list-module.php';
require_once 'search-module.php';
function startUFRJ($option){
    if($option==1){
        $ufrjSearch = new UfrjSearchModule();

        $response = $ufrjSearch->requestListData(filter_input(INPUT_GET, 'q'));

        echo json_encode($response);

    }else if($option==2){
        $ufrjDetails = new UfrjDetailsModule();

        $response = $ufrjDetails->requestData(filter_input(INPUT_GET,'controller'),filter_input(INPUT_GET, 'id'));
        echo json_encode($response);
    }
}