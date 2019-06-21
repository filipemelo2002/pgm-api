<?php

function cURL_Setup($set){
    curl_setopt($set, CURLOPT_HTTPHEADER,
        array(
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:58.0) Gecko/20100101 Firefox/58.0",
            "Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3",
            "Content-Type: application/x-www-form-urlencoded",
            "DNT: 1",
            "Connection: keep-alive"
              ));

     
       curl_setopt($set, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($set, CURLOPT_SSL_VERIFYHOST, false);
       curl_setopt($set, CURLOPT_FOLLOWLOCATION, false);
      curl_setopt($set, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($set, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($set, CURLOPT_TIMEOUT, 20);      
}
