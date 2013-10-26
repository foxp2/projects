<?php

namespace foxp2\backofficeBundle\Lib;

use Github;

class Utils extends \Github\Api\AbstractApi{   
    
    public static function getLimit($token)
    {        
        $client = new Github\Client();
        
        $client->authenticate($token, null, $client::AUTH_URL_TOKEN);    
        
        $limit = $client->getHttpClient()->get('rate_limit')->getContent();      
        
        return $limit;
    }
}
?>
