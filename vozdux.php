<?php

require_once 'simple_html_dom.php'; // библиотека для парсинга
            

// == uppercaser ==
function mb_ucfirst($str, $encoding='UTF-8')
{
    $str = mb_ereg_replace('^[\ ]+', '', $str);
    $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
           mb_substr($str, 1, mb_strlen($str), $encoding);
    return $str;
}
// -- uppercaser --


// ==== Replacement ====
function replacement($search, $replace, $text, $c)
{
    if($c > substr_count($text, $search))
    {
        return false;
    }
    else
    {
        $arr = explode($search, $text);
        $result = '';
        $k = 1;
        foreach($arr as $value)
        {
            $k == $c ? $result .= $value.$replace : $result .= $value.$search;
            $k++;
        }
        $pos = strripos($result,$search);
        $result = substr_replace($result,'',$pos,$pos + 3);
        return $result;
    };
}
// ---- Replacement ----


// ==== Get rouducts
function getProducts($url, $findpages = true)
{
    global $page_begin, $page_end, $domain;
    static $page_number = 1;
    $page_number_link = &$page_number;
    $page_pagination = '?page='.$page_number_link.'&_=1604231408389';
    $parse_result = array();
    $n = 10;
    $description;


    // загрузка урла
    $data = file_get_html($url);
    // очистка от лишнего
    foreach($data->find('script,link,comment') as $tmp)$tmp->outertext = '';

   if($findpages)
   {
        // Имя товара
        foreach ($data->find('h1.b1c-name') as $name) 
        {
            $product['name'] = $name->innertext;
        }


        // Цена товара
        foreach ($data->find('.catalog-detail-item-price-new') as $price) 
        {
            $price->innertext = str_replace(' руб.', '', $price->innertext);
            trim($price->innertext);
            $product['price'] = $price->innertext;
        }


        // ==== Заголовки статов ====
        foreach ($data->find('tbody') as $features) 
        {
            $cut_features = stristr($features, '<p align="center">');
            $product['features'] =  $cut_features;
        }

         $temp_product_features = $product['features'];
        unset($product['features']);



        $feature_row;
        // Статы
        foreach ($data->find('td p span') as $key => $features_item) 
        {
            static $item_counter;
            // $features_item = str_replace("span", "p", $features_item);
            $features_item = str_replace("</p>\n<p>", "", $features_item);
            $item_counter++;
            $product['features'][] = $features_item;  
        }

        foreach ($product['features'] as $key => $feature)
        {
            // $feature = str_replace('Основные характеристики', 'убратьосновныестатыОсновные характеристики', $feature);
            $product['descr'].= '<p>'.$feature.'</p>';
        }

        $product['descr'] = stristr($product['descr'], '<p><span style="font-size:16px;"><strong>Основные характеристики');
        $product['descr'] = str_replace('</span></p><p><span style="font-size:16px;">', '<br>', $product['descr']);

        

// repalcement
        $st = $product['descr'];
        $st = str_replace('</strong><br>','</strong><hr>',$st);
        $count_br = (substr_count($st, 'br') + 1)/ 2;
        is_float($count_br) ? $count_br -= 0.5 : true;

        $count_br = $count_br + 1;


        for ($i = 1; $i < $count_br; $i++){
            $st = replacement('<br>', ': ', $st, $i);
        }

        $st = str_replace('hr','p', $st);
        $st = str_replace('<br>','</p><p>', $st);

        $st = str_replace('<strong>','<b>', $st);
        $st = str_replace('</strong>','</b>', $st);
        $st = str_replace('<span>','', $st);
        $st = str_replace('</span>','', $st);
        $st = str_replace('<span style="font-size:16px;">','', $st);

        unset($product['features']);
        unset($product['descr']);
        $product['descr'] .= $st;


        $descr = $product['descr'];


        // Производитель
        if ($product['manufacturer'] = stristr($descr, 'Брэнд:'))
        {
            $product['manufacturer'] = stristr($product['manufacturer'], '</p>', true);
            $product['manufacturer'] = str_replace('Брэнд: ', '', $product['manufacturer']);           
        }
        else{$product['manufacturer'] = ' ';}

        // =category=
        if ($product['category'] = stristr($descr, 'Конструкция:'))
        {
            $product['category'] = stristr($product['category'], '</p>', true);
            $product['category'] = str_replace('Конструкция: ', '', $product['category']);
            $product['category'] = mb_ucfirst($product['category']);
        }
        else{$product['category'] = ' ';}
        // -category-

        // =color=
        if ($product['color'] = stristr($descr, 'Цвет:'))
        {
            $product['color'] = stristr($product['color'], '</p>', true);
            $product['color'] = str_replace('Цвет: ', '', $product['color']);
            $product['color'] = trim($product['color']);
            $product['color'] = mb_ucfirst($product['color']);

        }
        else{$product['color'] = ' ';}
        // -color-

        // =productivity=
        if ($product['productivity'] = stristr($descr, 'Производительность:'))
        {
            $product['productivity'] = stristr($product['productivity'], '</p>', true);
            $product['productivity'] = str_replace('Производительность: ', '', $product['productivity']);

        }
        else{$product['productivity'] = ' ';}
        // -productivity-

        // =controller=
        if ($product['controller'] = stristr($descr, 'Управление:'))
        {
            $product['controller'] = stristr($product['controller'], '</p>', true);
            $product['controller'] = str_replace('Управление: ', '', $product['controller']);
        }
        else{$product['controller'] = ' ';}
        // -controller-

        // =remote=
        if ($product['remote'] = stristr($descr, 'Пульт ДУ:'))
        {
            $product['remote'] = stristr($product['remote'], '</p>', true);
            $product['remote'] = str_replace('Пульт ДУ: ', '', $product['remote']);

            $product['remote'] = mb_ucfirst($product['remote']);
        }
        else{ $product['remote'] = ' ';}
        // -remote-



        // mainpic
        foreach ($data->find('.catalog-detail-images img') as $key => $img) 
        {   $src = $img->src;
            $pic = preg_replace('/cache\/\d{1,3}-\d{1,3}\/data/i','cache/750-750/data',$src);
            $pic = str_replace(' ', '%20', $pic);
            $product['img_'.$key] = $pic;

        }

   }


    $fp = fopen('vozdux-products.txt', 'a+');
    $send_to_text;

    foreach ($product as $fields) 
    {
            if (is_array($fields) || is_object($fields))
            {
                foreach ($fields as $key => $ars) {
                     $send_to_text[] = $ars;

                }
             // iconv('UTF-8', 'Windows-1252', $russian_please);
            }
             $send_to_text[] = $fields;
             echo '<br>';
    }
     fputcsv($fp, $send_to_text);
     fclose($fp);




     echo '<pre>';
     // echo $data;
     var_dump($product);
     // echo $data->innertext;
     echo '</pre>';




   $data->clear();// подчищаем за собой
   unset($data);
}
// ---- Get products ----


$page_begin = 1;
$page_end = 10;
$domain = 'http://grand-germes.by';
// $url = 'http://grand-germes.by/';

$url_add = '#tab-description';

// $url = 'https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-grand-hc6290b-w-60#tab-description';
// getProducts($url);




$all_links = array("https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-p-3050-bk",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-p-3050-ix",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-p-3050-wh",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-p-3060-ix",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-p-3060-wh",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-2003-60-duralum",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-2003-60gbk",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5250-gbk",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5250-gwh",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5260-x-e",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5260-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-6600-duralum",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gc-dual-bk-45-class-a",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gc-dual-bk-75-x-class-a",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gc-dual-wh-45-class-a",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gc-dual-wh-75-x-class-a",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gl45x",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gl75x-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gt-plus-45-bk",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gt-plus-45-x",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-nodor-extender-22-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-nodor-extender-white-glass-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-nodor-extender-22-inox-70",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-nodor-gat-850-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-50-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-50-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-60-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-60-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-t-glass-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-t-glass-90",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-sg-ii-black-glass-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-sg-ii-white-glass-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-box-ii-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-box-ii-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-box-medium-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-box-medium-70",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-602-ss",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-502-ss",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-602-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-502-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-502-g",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-602-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-502-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-602-tc-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-602-tc-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-2301-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-2301-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-stash-plus-aluminium-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-stash-plus-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-gamma-black-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-altair-inox-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-altair-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-inox-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-hb6102c-s-m-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-decor-inox-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-decor-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-sensor-white-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-sensor-black-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-white-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-white-60",
"https://vozdux.by/vytyazhki/vytyazhka-germes-stash-plus-50-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-black-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-2003-60-gwh",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-2003-60-sd",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-2003-90-duralium",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5250-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5260-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gt-plus-45-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-schtoffmaerr-strelka-i-60-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-schtoffmaerr-strelka-ii-60-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-schtoffmaerr-strelka-ii-60-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-schtoffmaerr-strelka-turbo-60-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-g-45-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-ex-3015",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-ex-5105",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gt-plus-75-inoxa",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5260-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-6900-duralum",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gt-plus-45-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-60-beige",
"https://vozdux.by/index.php?route=product/product&amp;path=163_164&amp;product_id=2455",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gt-plus-45-bk-b",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-50-beige",
"https://vozdux.by/index.php?route=product/product&amp;path=163_164&amp;product_id=2454",
"https://vozdux.by/index.php?route=product/product&amp;path=163_164&amp;product_id=2457",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-sg-ii-beige-glass-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-sg-ii-black-glass-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-sg-ii-black-glass-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-ex-2036",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-stash-plus-60-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-calabria",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-chester-60-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-chester-60-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-chester-60-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-stash-plus-60-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-p-3050-bk",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-p-3050-ix",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-p-3050-wh",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-p-3060-ix",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-p-3060-wh",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-2003-60-duralum",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-2003-60gbk",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5250-gbk",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5250-gwh",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5260-x-e",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5260-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-6600-duralum",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gc-dual-bk-45-class-a",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gc-dual-bk-75-x-class-a",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gc-dual-wh-45-class-a",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gc-dual-wh-75-x-class-a",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gl45x",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gl75x-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gt-plus-45-bk",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gt-plus-45-x",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-nodor-extender-22-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-nodor-extender-white-glass-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-nodor-extender-22-inox-70",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-nodor-gat-850-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-50-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-50-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-60-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-60-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-t-glass-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-t-glass-90",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-sg-ii-black-glass-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-sg-ii-white-glass-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-box-ii-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-box-ii-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-box-medium-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-box-medium-70",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-602-ss",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-502-ss",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-602-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-502-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-502-g",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-602-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-502-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-602-tc-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-602-tc-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-2301-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-retracta-2301-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-stash-plus-aluminium-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-stash-plus-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-gamma-black-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-altair-inox-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-altair-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-inox-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-hb6102c-s-m-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-decor-inox-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-decor-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-sensor-white-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-sensor-black-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-white-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-white-60",
"https://vozdux.by/vytyazhki/vytyazhka-germes-stash-plus-50-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-black-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-2003-60-gwh",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-2003-60-sd",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-2003-90-duralium",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5250-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5260-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gt-plus-45-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-schtoffmaerr-strelka-i-60-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-schtoffmaerr-strelka-ii-60-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-schtoffmaerr-strelka-ii-60-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-schtoffmaerr-strelka-turbo-60-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-g-45-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-ex-3015",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-ex-5105",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gt-plus-75-inoxa",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-5260-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-tf-6900-duralum",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gt-plus-45-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-60-beige",
"https://vozdux.by/index.php?route=product/product&amp;path=163_164&amp;product_id=2455",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-cata-gt-plus-45-bk-b",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-s-ii-50-beige",
"https://vozdux.by/index.php?route=product/product&amp;path=163_164&amp;product_id=2454",
"https://vozdux.by/index.php?route=product/product&amp;path=163_164&amp;product_id=2457",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-sg-ii-beige-glass-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-sg-ii-black-glass-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-ciarko-sl-sg-ii-black-glass-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-exiteq-ex-2036",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-stash-plus-60-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-calabria",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-chester-60-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-chester-60-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-chester-60-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-stash-plus-60-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-adari-60-bk",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-ceres-900xgwh",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-adari-60-wh",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-ceres-600xgbk",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-ceres-600xgwh",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-podium-500-xgbk",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-podium-500-xgwh",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-podium-600-xgbk",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-podium-600-xgwh",
"https://vozdux.by/vytyazhki/vytyazhka-nodor-icon-bk",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-atria-ntf-round-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-atria-ntf-round-black-sensor-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-atria-ntf-round-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-black-diamond-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-black-pearl-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-black-pearl-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-black-pearl-luxe-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-black-shadow-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-citro-x-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-citro-x-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-galaxy-nts-ivory-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-galaxy-skm-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-galaxy-skm-black-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-galaxy-skm-green-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-galaxy-skm-red-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-galaxy-skm-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-galaxy-skm-white-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-illumia-white-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-kr",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-nsa-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-nsa-black-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-nsa-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-nsa-white-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-nto-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-numia-60-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-or-38",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-planet-60-xgbk",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-sbo-ii-black-50",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-sbo-ii-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-sbo-ii-white-50",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-sbo-ii-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-sms-iii-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-sms-iii-black-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-sms-iii-white-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-specjal-star-50-sbm-white",
"https://vozdux.by/vytyazhki/vytyazhka-ciarko-specjal-star-50-glass-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-specjal-star-50-white-glass",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-specjal-star-60-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-specjal-star-60-glass-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-specjal-star-60-glass-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-specjal-star-60-glbk-touch-contr",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-specjal-star-60-glwh-touch-contr",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-specjal-star-60-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-versum-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-white-pearl-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-snpt-60-luxe-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-snpt-60-luxe-inox",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-2026",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-913b-cs40-90-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-913b-cs40-60-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-932-cs29-60-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-932-cs29-90-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-victoria-60-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-victoria-60-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-vifania-600",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-anastasia-60-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-anastasia-60-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-laura-60-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-toscana-60-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-toscana-60-etno-arnament",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-delta-white-decor-50",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-delta-white-decor-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-delta-black-sensor-50",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-delta-black-sensor-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-delta-white-sensor-50",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-delta-white-sensor-50",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-valencia-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-valencia-black-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-hc9213-a-s-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-hc9225f-s-white-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-amelia-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-vega-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-vega-black60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-modena-sensor-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-modena-sensor-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-modena-sensor-brown-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-milano-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-milano-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-ceres-900xgbk",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-thalassa-600xgbk",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-thalassa-900xgbk",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-thalassa-900xgwh",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-schtoffmaerr-new-line-90-black-glass",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-schtoffmaerr-new-star-60-black-glass",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5026-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5026-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5026-inox",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5076",
"https://vozdux.by/index.php?route=product/product&amp;path=163_168&amp;product_id=2453",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-illumia-black-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5226",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5246",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-cata-thalassa-600xgwh",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-sko-60-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-sko-90-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-sko-60-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-sko-90-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5336",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5366",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5386-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-special-star-50-sbm-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5386-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5389-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5389-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5396",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5399",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5426",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5437-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-exiteq-ex-5437-white",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-beta-vl3-900-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-c-500-glass",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-c-600-glass",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-gamma-600",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-neblia-500-blanca",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-neblia-500-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-neblia-600-antic-black",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-neblia-600-antic-white",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-neblia-600-blanca",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-neblia-600-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-neblia-600-ivory-brass",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-neblia-600-negra",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-v-500-blanca",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-v-500-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-v-600-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-v-600-negra",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-cre-60",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-cre-90",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-crn-60",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-crn-90",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-sigma-50-black",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-sigma-50-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-sigma-60-black",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-sigma-60-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-sigma-light-60-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-exiteq-rosix-600-white",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-exiteq-rosix-600-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-exiteq-boragine-600-white",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-exiteq-boragine-600-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-exiteq-boragine-600-black",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-exiteq-kavez-500-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-exiteq-kavez-500-white",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-germes-alt-inox-60",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-germes-alt-black-60",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-grand-hc6290b-w-60",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-omega-60-black",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-omega-60-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-omega-60-white",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-omega-90-black",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-v-600-wh-c",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-c-600-glass-black-h",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-c-600-glass-inox-h",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-exiteq-ex-5266",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-gamma-vl3-700-glassd",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-gamma-900-vl3-glass-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-omega-90-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-exiteq-ex-3025-white",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-exiteq-ex-3025-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-exiteq-ex-2019-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-beta-vl3-600-inox",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-cata-c-900-glass-inox",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr-50-inox",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr-50-white",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr-50-brown",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr-50-black",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr-60-inox",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr-60-white",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr-60-brown",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr-60-black",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-exiteq-standard-501-brown",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-exiteq-standard-501-white",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-exiteq-standard-501-inox",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-exiteq-standard-601-brown",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-exiteq-standard-601-white",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-exiteq-standard-601-inox",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-exiteq-standard-602-inox",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-exiteq-standard-602-white",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-exiteq-sam-box-60",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-germes-slim-white-50",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-germes-slim-white-60",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-germes-slim-brown-60",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-germes-slim-black-60",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-germes-slim-inox-50",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-germes-slim-inox-60",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-cata-f-2050-white",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-cata-f-2060-brown",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-cata-f-2060-white",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-cata-p-3060-black",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-cata-p-3260-negra",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-cata-p-3260-brown",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-cata-p-3260-white",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-cata-f-2050-marron",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-cata-f-2050-inox-a",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-cata-f-2060-inox-b",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-exiteq-ex-5115-inox",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-exiteq-ex-5116-inox",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-cata-p-3260-inox-b",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr%D1%81-50-black",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr%D1%81-50-brown",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr%D1%81-50-ivory",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr%D1%81-50-white",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr%D1%81-60-black",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr%D1%81-60-ivory",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr%D1%81-60-brown",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-ciarko-zr%D1%81-60-white",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-cata-midas-600xgbk",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-cata-midas-600xgwh",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-cata-midas-900xgbk",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-cata-selene-600-inox",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-cata-selene-900-inox",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-cata-sygma-vl3-600",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-black-60",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-black-90",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-black-slim-60",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-black-slim-90",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-black-slim-round-60",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-black-slim-round-90",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-black-slim-w-90",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-bls-60",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-bls-90",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-white-90",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-exiteq-pravo-60-inox",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-exiteq-si-box-100-6-inox",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-exiteq-si-box-100-9-inox",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-exiteq-688-ks14-50-inox",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-exiteq-688-ks14-60-inox",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-exiteq-668-ks14-50-inox",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-exiteq-668-fs14-60-inox",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-exiteq-ex-2016-inox",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-exiteq-ex-1016",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-grand-siena-inox-60",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-grand-hc9222e-s",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-grand-medina-sensor-inox-60",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-cata-c-900-glass-black-h",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-cata-c-900-glass-inox-h",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-cata-selene-700-inox",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-cata-midas-900xgwh",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-white-slim-60-inox-1100",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-white-slim-90-inox-1100",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-cata-sygma-vl3-900",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-black-slim-60-inox-1100",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-exiteq-ex-5239",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-ciarko-quatro-black-slim-90-inox-1100",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-exiteq-ex-2016-black",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-cre-60",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-cre-90",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-crn-60",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-ciarko-crn-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-galaxy-skm-green-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-galaxy-skm-red-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-ciarko-or-38",
"https://vozdux.by/vytyazhki/dekorativnye-vytyazhki/vytyazhka-exiteq-ex-5046",
"https://vozdux.by/vytyazhki/dekorativnye-vytyazhki/vytyazhka-exiteq-ex-5049",
"https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/vytyazhka-ciarko-orw-black-glass",
"https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/vytyazhka-ciarko-orw-inox-glass",
"https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/vytyazhka-ciarko-orw-white-glass",
"https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/vytyazhka-cata-isla-gamma-900-inox",
"https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/vytyazhka-exiteq-ex-5016-green",
"https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/vytyazhka-exiteq-ex-5016-red",
"https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/vytyazhka-exiteq-ex-5016-white",
"https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/vytyazhka-exiteq-ex-5016-black",
"https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/vytyazhka-exiteq-ex-5209-white",
"https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/vytyazhka-exiteq-ex-5209-black",
"https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/vytyazhka-exiteq-ex-5319-white",
"https://vozdux.by/vytyazhki/ostrovnye-vytyazhki/vytyazhka-exiteq-ex-5319-black",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-germes-alt-inox-60",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-germes-alt-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-toscana-60-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-toscana-60-etno-arnament",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-delta-white-decor-50",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-delta-white-decor-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-delta-black-sensor-50",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-delta-black-sensor-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-delta-white-sensor-50",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-delta-white-sensor-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-stash-plus-aluminium-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-stash-plus-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-gamma-black-60",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-germes-slim-white-50",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-germes-slim-white-60",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-germes-slim-brown-60",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-germes-slim-black-60",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-germes-slim-inox-50",
"https://vozdux.by/vytyazhki/ploskie-vytyazhki/vytyazhka-germes-slim-inox-60",
"https://vozdux.by/vytyazhki/vytyazhka-germes-stash-plus-50-inox",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-murano-60-black",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-murano-60-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-stash-plus-60-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-calabria",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-chester-60-black",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-chester-60-inox",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-chester-60-white",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-germes-stash-plus-60-white",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-bristol-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-germes-bristol-black-60",
"https://vozdux.by/vytyazhki/kupolnye-vytyazhki/vytyazhka-grand-hc6290b-w-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-valencia-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-valencia-black-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-hc9213-a-s-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-hc9225f-s-white-90",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-amelia-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-vega-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-vega-black60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-modena-sensor-white-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-modena-sensor-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-modena-sensor-brown-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-milano-black-60",
"https://vozdux.by/vytyazhki/naklonnye-vytyazhki/vytyazhka-grand-milano-white-60",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-grand-siena-inox-60",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-grand-hc9222e-s",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-altair-inox-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-altair-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-inox-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-hb6102c-s-m-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-decor-inox-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-decor-inox-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-sensor-white-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-sensor-black-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-white-50",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-white-60",
"https://vozdux.by/vytyazhki/vstraivaemye-vytyazhki/vytyazhka-grand-toledo-black-60",
"https://vozdux.by/vytyazhki/t-obraznye-vytyazhki/vytyazhka-grand-medina-sensor-inox-60",
"https://vozdux.by/index.php?route=product/product&amp;path=163_164&amp;product_id=2455",
"https://vozdux.by/index.php?route=product/product&amp;path=163_168&amp;product_id=2453",
"https://vozdux.by/index.php?route=product/product&amp;path=163_164&amp;product_id=2454"
);





foreach ($all_links as $link) {
    getProducts($link.'#tab-description');
}
