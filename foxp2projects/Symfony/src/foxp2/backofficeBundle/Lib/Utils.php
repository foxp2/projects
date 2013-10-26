<?php

namespace foxp2\backofficeBundle\Lib;

use Github;

class Utils extends \Github\Api\AbstractApi{   
    
    public static function getLimit()
    {        
        $client = new Github\Client();     
        
        $client->authenticate('43fb6741ec009d72a05cc3c7779c87b6e4c95aaf', null, $client::AUTH_URL_TOKEN);    
        
        $limit = $client->getHttpClient()->get('rate_limit')->getContent();      
        
        return $limit;
    }
}
?>
