<?php

// Initialize the session
session_start();

// subscribe to feed via rssCloud

function pleaseNotify($feed, $domain, $port, $path) {

    $url = 'http://' . $domain . $path;
    $host = parse_url(BASE_URL,PHP_URL_HOST);
    
    $fields = array(
      'domain' => $host,
      'port' => '443',
      'path' => '/php-mst/notify/',
      'registerProcedure' => '',
      'protocol' => 'https-post',
      'url1' => $feed
    );
    
    $postdata = http_build_query($fields);
    
    try {
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        $httpReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = curl_exec($ch);
        if ($result === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        
    } catch(Exception $e) {
        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);
    } finally {
        curl_close($ch);
    }
}

?>