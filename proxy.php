<?php

require_once 'simple_html_dom.php'; // библиотека для парсинга

// $context = array('http' => array('proxy' => 'tcp://82.209.216.156:1080','request_fulluri' => true,),);
// $stream = stream_context_create($context);
// $dom = file_get_html('http://grand-germes.by/', false, $stream);


$dom = file_get_html('http://grand-germes.by/');
$ip = $dom->find('span#ip', 0)->innertext;
var_dump($ip);