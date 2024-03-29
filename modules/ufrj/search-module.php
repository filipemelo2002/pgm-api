<?php


class UfrjDetailsModule{
    function requestData($linkController, $linkId){
        $curlSetup = curl_init();
        curl_setopt($curlSetup, CURLOPT_URL, "https://pantheon.ufrj.br/handle/$linkController/$linkId");
        cURL_Setup($curlSetup);
    
        $response = curl_exec($curlSetup);
        $tableMainData = get_string_between($response, '<table class="table itemDisplayTable">', '</table>');
        $tableWithFile = get_string_between($response, '<table class="table panel-body">', '</a>');
        $urlFile = $this->fetchFileUrl($tableWithFile);
        $response = $this->fetchAllDataAndReturnJson($tableMainData, $urlFile);
        $response['arquivo'] = $urlFile;

        return $response;
    }
    function fetchAllDataAndReturnJson($contentOriginal, $urlFile){
        $expression = "/(?=>)(.*?)(?=<)/";
        $content = array();
        preg_match_all($expression,$contentOriginal, $content);
        $contentWithoutEspecialChars = $this->replaceEspecialChars($content[0]);
        $contentWithoutBlankLines = removeBlankLines($contentWithoutEspecialChars);
        $responseJson = $this->fetchAllDataIntoAJsonArray($contentWithoutBlankLines);
        
        return $responseJson;
    }
    
    function replaceEspecialChars($string){
        return str_replace(array('&#x20', '>', ';', '&nbsp'), array(' ', '', '', ''), $string);
    }
    
    
    function fetchAllDataIntoAJsonArray($content){
        $reponse = array();
        $matchedContents = $this->fetchDataIntoAnArray($content);
        $finalResponse = $this->turnArrayIntoAJsonArray($matchedContents);
        return $finalResponse;
    }
    
    
    function fetchDataIntoAnArray($content){
        $matchedLines = array();
        $index=0;
        while($index<count($content)){
    
            $line = '';
            if(endsWith($content[$index],':')){
                $line = $content[$index];
                $index++;
                while(!(endsWith($content[$index], ':'))){
                    $line = $line.';'.$content[$index];
                    $index++;
    
                    if($index==count($content)){
                        break;
                    }
                }
                $matchedLines[] = $line;
                
            }
        }
        return $matchedLines;
    }
    
    function turnArrayIntoAJsonArray($matchedContents){
        $jsonArray = array();
        
        foreach($matchedContents as $line){
            $splited = explode(':', $line);
            $key = $this->getSanitizedJsonKey($splited[0]);
            $value = substr($splited[1], 1);
            $jsonArray[$key] = $value;
    
        }
    
        return $jsonArray;
    }
    
    function getSanitizedJsonKey($jsonKey){
        switch($jsonKey){
            case 'Tipo':
                return 'tipo';
            case 'Título':
                return 'titulo';
            case 'Autor(es)/Inventor(es)':
                return 'autor';
            case 'Resumo':
                return 'resumo';
            case 'Resumo ':
                return 'abstract';
            case 'Palavras-chave':
                return 'keywords';
            case 'Assunto CNPq':
                return 'assunto';
            case 'Departamento':
                return 'departament';
            case 'In':
                return 'in';
            case 'Número':
                return 'numero';
            case 'Data de publicação':
                return 'dataPublicacao';
            case 'País de publicação': 
                return 'pais';
            case 'Idioma da publicação':
                return 'lang';
            case 'Tipo de acesso':
                return 'tipoAcesso';
            case 'Citação':
                return 'citacao';
            case 'URI':
                return 'uri';
            case 'Aparece nas coleções':
                return 'colecoes';
            default: 
                return 'unknown';
    
        }
    }
    
    function fetchFileUrl($content){
        return "https://pantheon.ufrj.br/".get_string_between($content, 'href="','"');
    }

}

