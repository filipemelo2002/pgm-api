<?php


class UfrjSearchModule{



    function requestListData($query){

        $curlSetup = curl_init();
        curl_setopt($curlSetup, CURLOPT_URL, "https://pantheon.ufrj.br/simple-search?query=".urlencode($query)."&sort_by=score&order=desc&rpp=30&etal=0&start=0");
        cURL_Setup($curlSetup);
    
        $response = curl_exec($curlSetup);
        $fullTable = get_string_between($response, '<table align="center" class="table" summary="This table browses all dspace content">','</div>');
        $rowsOnly = get_string_between($fullTable, 'Tipo</th></tr>', '</table>');
        $finalResponse = $this->getDataAndReturnJson($rowsOnly);
        
        return $finalResponse; 
    }

    function getDataAndReturnJson($contentOriginal){
        $expression = "/(?=>)(.*?)(?=<)/";
        $content = array();
        preg_match_all($expression,$contentOriginal, $content);
        $links = $this->getAllLinksAndFetchArray($contentOriginal);
        $response = $this->getSanitizedResponse($content[0],$links);
        return $response;
    }


    function  getAllLinksAndFetchArray($content){
        $links = getContents($content, 'href="/handle/','"');
        return $links;
    }


    function getSanitizedResponse($content, $links){
        
        $contentWithoutEspecialChars = $this->replaceEspecialChars($content);
        $contentWithtoutBlankLines = removeBlankLines($contentWithoutEspecialChars);
        $contentWithAuthorsSet = $this->matchAllAuthorsIntoOneString($contentWithtoutBlankLines);
        $response = $this->fetchAllDataIntoAJsonArray($contentWithAuthorsSet, $links);
        
        return $response;
    
    }
    
    function replaceEspecialChars($string){
        return str_replace(array('&#x20', '>', ';'), array(' ', '', ''), $string);
    }
    
    function matchAllAuthorsIntoOneString($content){
        $contentAuthorsSet = array();
        $controller = 0;
    
        for($index=0; $index<count($content); $index++){
            $line = $content[$index];
    
            if(strlen($line)==1){
                $previousIndexContent = $index -1 ;
                $nextIndexContent = $index +1 ;
    
                $previousIndexAuthor = $controller -1 ;
                $nextIndexAuthor = $controller+1;
    
                $fixed = $contentAuthorsSet[$previousIndexAuthor].'; '.$content[$nextIndexContent];
                $contentAuthorsSet[$previousIndexAuthor] = $fixed;
                array_splice($content, $nextIndexContent,1);
    
                
            }else{
                $contentAuthorsSet[$controller] = $line;
                $controller++;
            }
        }
        return $contentAuthorsSet;
    }
    
    function fetchAllDataIntoAJsonArray($content, $links){
        $jsonResponse = array();
        $controller =0;
        for($index=0; $index< intval(count($content)/4); $index++){
            $authorIndex = $controller;
            $titleIndex = $controller +1;
            $dateIndex = $controller+2;
            $typeIndex = $controller+3;
            
            $linkControllerAndId = explode('/',$links[$index]);
            $jsonResponse[] = array('author'=>$content[$authorIndex],
                                    'title'=>$content[$titleIndex],
                                    'type'=>$content[$typeIndex],
                                    'date'=>$content[$dateIndex],
                                    'link'=>'https://pantheon.ufrj.br/handle/'.$links[$index],
                                    'linkController'=>$linkControllerAndId[0],
                                    'linkId'=>$linkControllerAndId[1]);
            $controller+=4;
        }
        return $jsonResponse;
    }

}



