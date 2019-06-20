<?php


function requestListData($query){

    $curlSetup = curl_init();
    curl_setopt($curlSetup, CURLOPT_URL, "https://pantheon.ufrj.br/simple-search?query=".urlencode($query)."&sort_by=score&order=desc&rpp=30&etal=0&start=0");
    cURL_Setup($curlSetup);

    $response = curl_exec($curlSetup);
    $fullTable = get_string_between($response, '<table align="center" class="table" summary="This table browses all dspace content">','</div>');
    $rowsOnly = get_string_between($fullTable, 'Tipo</th></tr>', '</table>');
    $finalResponse = getDataAndReturnJson($rowsOnly);
    
    echo json_encode($finalResponse); 
}
function getDataAndReturnJson($contentOriginal){
    $expression = "/(?=>)(.*?)(?=<)/";
    $content = array();
    preg_match_all($expression,$contentOriginal, $content);
    $links = getAllLinksAndFetchArray($contentOriginal);
    $response = getSanitizedResponse($content[1],$links);
    return $response;
}
function  getAllLinksAndFetchArray($content){
    $links = getContents($content, 'href="/handle/','"');
    return $links;
}
function getSanitizedResponse($content, $links){
    $importantLines = array();
    $control = 0;
    for($i=0; $i< count($content); $i++){
        $line = replaceEspecialChars($content[$i]);
        if(strlen($line)>0){
            if(strlen($line)==1){
                $importantLinesPreviousIndex = $control -1;
                $nextLine = (isset($content[$i+1])===TRUE? $content[$i+1]: '');
                $previousLine = (isset($importantLines[$importantLinesPreviousIndex])===TRUE? $importantLines[$importantLinesPreviousIndex]: 'UNdefined');
                $importantLines[$importantLinesPreviousIndex] = $previousLine.'; '. replaceEspecialChars($nextLine);
                $content[$i+1] = ''; 
                $control++;
            }else{
                $importantLines[$control] = $line;
                $control++;
            }
            
            
        }   
    }
    

    $controller =0;
    $jsonResponse = array();

    $linesResponse = array();

    foreach($importantLines as $line){
        $linesResponse[] = $line; 
    }
   
    for($index=0; $index<intval(count($importantLines)/4); $index++){
        
        $linkControllerAndId = explode('/', $links[$index]);



        $jsonResponse[] = array('author'=>$linesResponse[$controller],
                                'title'=>$linesResponse[$controller+1],
                                'date'=> $linesResponse[$controller+2],
                                'link'=>'https://pantheon.ufrj.br/handle/'.$links[$index],
                                'controller'=> $linkControllerAndId[0],
                                'id'=> $linkControllerAndId[1],
                                'type'=>$linesResponse[$controller+3]);
        $controller += 4;
    }
    
    return $jsonResponse;

}
function replaceEspecialChars($string){
    return str_replace(array('&#x20', '>', ';'), array(' ', '', ''), $string);
}
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


function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function getContents($str, $startDelimiter, $endDelimiter) {
    $contents = array();
    $startDelimiterLength = strlen($startDelimiter);
    $endDelimiterLength = strlen($endDelimiter);
    $startFrom = $contentStart = $contentEnd = 0;
    while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
      $contentStart += $startDelimiterLength;
      $contentEnd = strpos($str, $endDelimiter, $contentStart);
      if (false === $contentEnd) {
        break;
      }
      $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
      $startFrom = $contentEnd + $endDelimiterLength;
    }
  
    return $contents;
  }