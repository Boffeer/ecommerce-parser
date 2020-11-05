<?php

require_once 'simple_html_dom.php'; // библиотека для парсинга
            
           


// ==== Get rouducts
function getLinks($url, $findpages = true)
{
    $vozdux_links;
    $links;

    // загрузка урла
    $data = file_get_html($url);
    // очистка от лишнего
    foreach($data->find('script,link,comment') as $tmp)$tmp->outertext = '';
   if($findpages)
   {
        // Ссылка
        foreach ($data->find('a.item-title') as $link) 
        {
            // static $counter;
            $links[] = $link->href;
            // $counter++;
        }
    }
    var_dump($links);
          echo '<br>';
          echo '<br>';
          echo '<br>';
          echo '<br>';
          echo '<br>';
          echo '<br>';
          echo '<br>';
          echo '<br>';
          echo '<br>';

    $vozdux_links = $links;

     $fp = fopen('vozdux-links.txt', 'a+');
     $get_links;

     foreach ($links as $lnk) 
     {
        if (is_array($lnk) || is_object($lnk))
         {
             foreach ($lnk as $key => $ars) {
                  $get_links[] = $ars;

             }
          // iconv('UTF-8', 'Windows-1252', $russian_please);
         }
          $get_links[] = $lnk;
          echo '<br>';
     }
      fputcsv($fp, "'".$get_links."'");
      fclose($fp);


    $data->clear();// подчищаем за собой
    unset($data);
}

$url = 'https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/?limit=100';

$urls = array('https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/?limit=100', 'https://vozdux.by/vytyazhki/naklonnye-vytyazhki/?limit=100', 'https://vozdux.by/vytyazhki/kupolnye-vytyazhki/?limit=100', 'https://vozdux.by/vytyazhki/ploskie-vytyazhki/?limit=100', 'https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/?limit=100', 'https://vozdux.by/vytyazhki/dekorativnye-vytyazhki/?limit=100', 'https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/?limit=100', 'https://vozdux.by/vytyazhki/vytyazhki-germes/?limit=100', 'https://vozdux.by/vytyazhki/vytyazhki-grand/?limit=100');


// getLinks($url);


foreach ($urls as $link) {
    getLinks($link);
}