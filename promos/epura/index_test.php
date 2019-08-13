<?php
$url = $_SERVER['REQUEST_URI'];
$query_str = parse_url($url, PHP_URL_QUERY);
if ($query_str!='') {$query_str='&'.$query_str;}
header('Location:https://siguesudando.com/?id=RXcwZnd1cmM4b1NrQXU5aDdrTXNxQT09'.$query_str);
?>
