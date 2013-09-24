<?php

namespace foxp2\backofficeBundle\Lib;

use Github;

class Utils extends \Github\Api\AbstractApi{   
    
    public static function getLimit()
    {        
        $client = new Github\Client();
        
        $limit = $client->getHttpClient()->get('rate_limit')->getContent();      
        
        return $limit;
    }
}
?>
