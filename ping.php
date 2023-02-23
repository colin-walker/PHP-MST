<?php

// ping rssCloud for update notifications 

function doPing($feed) {
$url = 'http://rpc.rsscloud.io/ping';

$fields = array('url' => $feed);

$postdata = http_build_query($fields);

$ch = curl_init($url);
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_POSTREDIR, CURL_REDIR_POST_ALL);
$result = curl_exec($ch);
curl_close($ch);
}

?>