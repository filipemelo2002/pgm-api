<?php


class UfpeSearchModule{

    function requestListData($query){

        $curlSetup = curl_init();
        curl_setopt($curlSetup, CURLOPT_URL, "https://repositorio.ufpe.br/simple-search?location=%2F&query=".urlencode($query)."&rpp=30&sort_by=score&order=desc");
        cURL_Setup($curlSetup);
    
        $response = curl_exec($curlSetup);
        $tableOnly = get_string_between($response, '<table align="center" class="table" summary="This table browses all dspace content">','</table>');

        $userResponse = $this->sanitizeContent($tableOnly);
        return $userResponse;
    }

    function sanitizeContent($table){
        

        $dates = getContents($table, 'align="right">','</td>');

        $titleLine = getContents($table,'<td headers="t2" class="oddRowOddCol" >','</td>');
        $authorLine = getContents($table,'<td headers="t3" class="oddRowEvenCol" ><em>','</em></td>');

        $titles = $this->getSanitizedTitle($titleLine);
        $authors = $this->getSanitizedAuthor($authorLine);
        $links = $this->getLinks($titleLine);

        $response = $this->turnDataIntoAnArray($dates,$titles,$authors,$links);

        return $response;
    }

    function getSanitizedTitle($titleLine){
        $title = array();

        foreach($titleLine as $line){
            $title[] = $this->replaceEspecialChars(get_string_between($line, ">", "<"));
        }

        return $title;
    }
    function getSanitizedAuthor($authorLine){
        $author = array();

        foreach($authorLine as $line){
            $author[] = $this->replaceEspecialChars(get_string_between($line, ">", "<"));
        }

        return $author;
    }
    function getLinks($titleLine){
        $links = array();

        foreach($titleLine as $line){

            $links[] = get_string_between($line, 'href="/handle/','"');

        }

        return $links;
    }
    function replaceEspecialChars($string){
        return str_replace(array('&#x20', '>', ';'), array(' ', '', ''), $string);
    }
    function turnDataIntoAnArray($dates,$titles,$authors, $links){
        $response = array();

        for($i=0; $i<count($titles); $i++){

            $controllerAndId = explode('/', $links[$i]);
            $response[] = array(
                'author'=>$authors[$i],
                'title'=>$titles[$i],
                'type'=>'',
                'date'=>$dates[$i],
                'link'=>'https://repositorio.ufpe.br/handle/'.$links[$i],
                'linkController'=>$controllerAndId[0],
                'linkId'=>$controllerAndId[1]
            );
        }   

        return $response;
    }
}