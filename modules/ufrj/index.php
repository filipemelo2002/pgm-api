<?php

require_once 'list-module.php';
require_once 'search-module.php';
function startUFRJ($option){
    $retorno= array();
    if($option==1){
        $ufrjSearch = new UfrjSearchModule();

        $response = $ufrjSearch->requestListData(filter_input(INPUT_GET, 'q'));

        $retorno = $response;

    }else if($option==2){
        $ufrjDetails = new UfrjDetailsModule();

        $response = $ufrjDetails->requestData(filter_input(INPUT_GET,'controller'),filter_input(INPUT_GET, 'id'));
        $retorno = $response;
    }

    return $retorno;
}