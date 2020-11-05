<?php

require_once 'simple_html_dom.php'; // библиотека для парсинга
            



// ==== DB connect ====
// $host = 'localhost'; // адрес сервера 
// $user = 'parser'; // имя пользователя
// $pass = '1234'; // пароль
// $db_name = 'grand_germes'; // имя базы данных
// $linkSQL = mysqli_connect($host, $user, $pass, $db_name);

// Ругаемся, если соединение установить не удалось
// if (!$linkSQL) {
//   echo 'Не могу соединиться с БД. Код ошибки: ' . mysqli_connect_errno() . ', ошибка: ' . mysqli_connect_error();
//   exit;
// }
// ---- DB conntct ----



// == uppercaser ==
function mb_ucfirst($str, $encoding='UTF-8')
{
    $str = mb_ereg_replace('^[\ ]+', '', $str);
    $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
           mb_substr($str, 1, mb_strlen($str), $encoding);
    return $str;
}
// -- uppercaser --





// == Экспорт в цсв ==
function export_csv(
    $table,         // Имя таблицы для экспорта
    $afields,       // Массив строк - имен полей таблицы
    $filename,      // Имя CSV файла для сохранения информации
                // (путь от корня web-сервера)
    $delim=',',         // Разделитель полей в CSV файле
    $enclosed='"',      // Кавычки для содержимого полей
    $escaped='\\',      // Ставится перед специальными символами
    $lineend='\\r\\n'){     // Чем заканчивать строку в файле CSV

$q_export = 
"SELECT ".implode(',', $afields).
"   INTO OUTFILE '".$_SERVER['DOCUMENT_ROOT'].$filename."' ".
"FIELDS TERMINATED BY '".$delim."' ENCLOSED BY '".$enclosed."' ".
"    ESCAPED BY '".$escaped."' ".
"LINES TERMINATED BY '".$lineend."' ".
"FROM ".$table
;

    // Если файл существует, при экспорте будет выдана ошибка
    if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)) 
        unlink($_SERVER['DOCUMENT_ROOT'].$filename); 
    return mysql_query($q_export);
}
// -- Экспорт цсв --






// ==== Links array ====
$all_links = array('http://grand-germes.by/product/vytyazhka-grand-gloucester-60-chernyy/',
'http://grand-germes.by/product/100/',
'http://grand-germes.by/product/vytyazhka-grand-gloucester-60-belaya/',
'http://grand-germes.by/product/vytyazhka-grand-corsa/',
'http://grand-germes.by/product/kukhonnaya-vytyazhka-germes-aura-60-chernaya/',
'http://grand-germes.by/product/100-1/',
'http://grand-germes.by/product/138/',
'http://grand-germes.by/product/99/',
'http://grand-germes.by/product/84/',
'http://grand-germes.by/product/143/',
'http://grand-germes.by/product/177/',
'http://grand-germes.by/product/81/',
'http://grand-germes.by/product/germes-geneve-60-chernaya/',
'http://grand-germes.by/product/144/',
'http://grand-germes.by/product/vytyazhka-grand-petra-60-chyornaya-/',
'http://grand-germes.by/product/146/',
'http://grand-germes.by/product/germes-chester-60-nerzhstal/',
'http://grand-germes.by/product/141/',
'http://grand-germes.by/product/vytyazhka-grand-gloucester-60-inox/',
'http://grand-germes.by/product/89/',
'http://grand-germes.by/product/88/',
'http://grand-germes.by/product/145/',
'http://grand-germes.by/product/99-1/',
'http://grand-germes.by/product/176/',
'http://grand-germes.by/product/127/',
'http://grand-germes.by/product/vytyazhka-grand-petra-60-chyornaya--1/',
'http://grand-germes.by/product/117/',
'http://grand-germes.by/product/vytyazhka-germes-alfa-gc-90-chyornyy/',
'http://grand-germes.by/product/kukhonnaya-vytyazhka-germes-aura-90-belaya/',
'http://grand-germes.by/product/vytyazhka-germes-alfa-gc-60-chernyy/',
'http://grand-germes.by/product/118/',
'http://grand-germes.by/product/vytyazhka-kukhonnaya-germes-pula-60-nerzhaveyushchaya-stal-1/',
'http://grand-germes.by/product/kukhonnaya-vytyazhka-germes-toscana-sensor-60-chyornaya/',
'http://grand-germes.by/product/vytyazhka-germes-bravo-sensor-60-belyy/',
'http://grand-germes.by/product/124/',
'http://grand-germes.by/product/vytyazhka-kukhonnaya-germes-pula-60-nerzhaveyushchaya-stal-1-1/',
'http://grand-germes.by/product/vytyazhka-grand-gloucester-60-chernyybelyy/',
'http://grand-germes.by/product/140/',
'http://grand-germes.by/product/vytyazhka-grand-porto-60-chernaya/',
'http://grand-germes.by/product/vytyazhka-grand-gloucester-60-belyynerzhaveyushchaya-stal/',
'http://grand-germes.by/product/120/',
'http://grand-germes.by/product/vytyazhka-grand-boston-60-chernyynerzhaveyushchaya-stal/',
'http://grand-germes.by/product/vytyazhka-germes-chester-60-chyornyy/',
'http://grand-germes.by/product/123/',
'http://grand-germes.by/product/kukhonnaya-vytyazhka-germes-aura-90-chernaya/',
'http://grand-germes.by/product/142/',
'http://grand-germes.by/product/vytyazhka-grand-petra-push-60-chernaya/',
'http://grand-germes.by/product/125/',
'http://grand-germes.by/product/102/',
'http://grand-germes.by/product/vytyazhka-grand-gloucester-50-chernyy-1/',
'http://grand-germes.by/product/93/',
'http://grand-germes.by/product/vytyazhka-grand-gloucester-50-belaya-1/',
'http://grand-germes.by/product/vytyazhka-germes-bravo-sensor-60-chernyy/',
'http://grand-germes.by/product/vytyazhka-germes-toscana-etno-60-chyornaya-s-risunkom/',
'http://grand-germes.by/product/83/',
'http://grand-germes.by/product/vytyazhka-germes-sigma-60-sm-chyornaya/',
'http://grand-germes.by/product/vytyazhka-germes-loksa-60-chyornaya/',
'http://grand-germes.by/product/vytyazhka-grand-petra-gc-60-chernaya/',
'http://grand-germes.by/product/germes-brighton-60-/',
'http://grand-germes.by/product/vytyazhka-kukhonnaya-germes-pula-60-nerzhaveyushchaya-stal/',
  'http://grand-germes.by/product/vytyazhka-kukhonnaya-germes-parma-60-nerzh-stal/',
'http://grand-germes.by/product/158/',
'http://grand-germes.by/product/82/',
'http://grand-germes.by/product/134/',
'http://grand-germes.by/product/vytyazhka-germes-sarnia-push-60-chyornaya/',
'http://grand-germes.by/product/vytyazhka-kukhonnaya-germes-sintra-50-chyornaya/',
'http://grand-germes.by/product/kukhonnaya-vytyazhka-grand-fleet-nerzhaveyushchaya-stal/',
'http://grand-germes.by/product/vytyazhka-kukhonnaya-germes-york-60-nerzhaveyushchaya-stal/',
'http://grand-germes.by/product/vytyazhka-germes-elva-50-nerzhaveyushchaya-stal/',
'http://grand-germes.by/product/vytyazhka-kukhonnaya-germes-york-60-belaya/',
'http://grand-germes.by/product/103/',
'http://grand-germes.by/product/122/',
'http://grand-germes.by/product/128/',
'http://grand-germes.by/product/135/',
'http://grand-germes.by/product/149/',
'http://grand-germes.by/product/172/',
'http://grand-germes.by/product/180/',
'http://grand-germes.by/product/181/',
'http://grand-germes.by/product/vytyazhka-germes-alfa-gc-60-belyy/',
'http://grand-germes.by/product/vytyazhka-germes-sarnia-60-chyornaya/',
'http://grand-germes.by/product/vytyazhka-germes-sarnia-60-chyornaya-1/',
'http://grand-germes.by/product/vytyazhka-germes-calabria-52-nerzhaveyushchaya-stal/',
'http://grand-germes.by/product/vytyazhka-germes-baden-60-chernyy/',
'http://grand-germes.by/product/vytyazhka-germes-alt-50-nerzhaveyushchaya-stal/',
'http://grand-germes.by/product/vytyazhka-germes-arosa-60-chernyy/',
'http://grand-germes.by/product/vytyazhka-germes-bravo-push-60-belyy/',
'http://grand-germes.by/product/vytyazhka-germes-bravo-push-60-chernyy/',
'http://grand-germes.by/product/vytyazhka-grand-petra-push-60-belaya/',
'http://grand-germes.by/product/vytyazhka-germes-alt-sensor-50-nerzhaveyushchaya-stal/',
'http://grand-germes.by/product/vytyazhka-grand-boston-90-chernyynerzhaveyushchaya-stal/',
  'http://grand-germes.by/product/vytyazhka-germes-toscana-sensor-60-belaya/',
'http://grand-germes.by/product/vytyazhka-germes-sarnia-60-belaya/',
'http://grand-germes.by/product/vytyazhka-germes-loksa-60-belaya/',
'http://grand-germes.by/product/vytyazhka-germes-alfa-gc-90-belyy/',
'http://grand-germes.by/product/vytyazhka-germes-elva-60-chernaya/',
'http://grand-germes.by/product/vytyazhka-germes-elva-60-nerzhaveyushchaya-stal/',
'http://grand-germes.by/product/vytyazhka-germes-baden-50-belyy/',
'http://grand-germes.by/product/vytyazhka-germes-baden-50-chernyy/',
'http://grand-germes.by/product/vytyazhka-germes-baden-60-belyy/',
'http://grand-germes.by/product/vytyazhka-germes-baden-sensor-50-chernyy/',
'http://grand-germes.by/product/vytyazhka-germes-baden-sensor-50-belyy/',
'http://grand-germes.by/product/vytyazhka-germes-baden-sensor-60-chernyy/',
'http://grand-germes.by/product/vytyazhka-germes-baden-sensor-60-belyy/',
'http://grand-germes.by/product/vytyazhka-grand-lester-gc-50-chyornyy/',
'http://grand-germes.by/product/vytyazhka-grand-lester-gc-60-chyornyy/',
'http://grand-germes.by/product/vytyazhka-grand-turino-gc-60-belyy/',
'http://grand-germes.by/product/vytyazhka-grand-turino-gc-90-belyy/',
'http://grand-germes.by/product/vytyazhka-grand-turino-gc-60-chernyy/',
'http://grand-germes.by/product/vytyazhka-grand-turino-gc-90-chernyy/',
'http://grand-germes.by/product/vytyazhka-grand-blagnac-60-nerzhaveyushchaya-stal/',
'http://grand-germes.by/product/vytyazhka-kukhonnaya-germes-york-chernaya/',
'http://grand-germes.by/product/vytyazhka-germes-pyramida-50-chyornyy/',
'http://grand-germes.by/product/vytyazhka-germes-sigma-90-sm-chernaya/',
'http://grand-germes.by/product/86/',
'http://grand-germes.by/product/vytyazhka-grand-toulouse-60-belaya/',
'http://grand-germes.by/product/139/',
'http://grand-germes.by/product/129/',
'http://grand-germes.by/product/85/',
'http://grand-germes.by/product/kukhonnaya-vytyazhka-germes-aura-60-belaya/',
'http://grand-germes.by/product/154/',
'http://grand-germes.by/product/vytyazhka-germes-beta-90-chernaya/',
'http://grand-germes.by/product/vytyazhka-grand-toulouse-60-chernaya/',
'http://grand-germes.by/product/160/',
'http://grand-germes.by/product/vytyazhka-germes-toscana-ethno-60-belyy-s-risunkom/',
'http://grand-germes.by/product/156/',
'http://grand-germes.by/product/94/',
'http://grand-germes.by/product/159/',
'http://grand-germes.by/product/79/',
'http://grand-germes.by/product/115/',
'http://grand-germes.by/product/119/',
'http://grand-germes.by/product/136/',
'http://grand-germes.by/product/132/',
'http://grand-germes.by/product/121/',
'http://grand-germes.by/product/126/',
'http://grand-germes.by/product/137/',
'http://grand-germes.by/product/148/',
'http://grand-germes.by/product/161/',
'http://grand-germes.by/product/162/',
'http://grand-germes.by/product/163/',
'http://grand-germes.by/product/164/',
'http://grand-germes.by/product/166/',
'http://grand-germes.by/product/171/',
'http://grand-germes.by/product/178/',
'http://grand-germes.by/product/179/',
'http://grand-germes.by/product/182/',
'http://grand-germes.by/product/183/',
'http://grand-germes.by/product/184/',
'http://grand-germes.by/product/185/',
'http://grand-germes.by/product/186/',
'http://grand-germes.by/product/187/',
'http://grand-germes.by/product/vytyazhka-germes-elva-50-belaya/',
'http://grand-germes.by/product/vytyazhka-germes-elva-60-belaya/',
'http://grand-germes.by/product/vytyazhka-germes-elva-50-chernaya/',
'http://grand-germes.by/product/vytyazhka-germes-loksa-50-chyornaya/',
);
// ---- Links array ----




// ==== test Links array ====
$test_links = array('http://grand-germes.by/product/vytyazhka-grand-gloucester-60-chernyy/',
'http://grand-germes.by/product/100/',
'http://grand-germes.by/product/vytyazhka-grand-gloucester-60-belaya/',
'http://grand-germes.by/product/vytyazhka-grand-corsa/',);
// ---- test Links array ----





// ==== Get rouducts
function getProducts($url, $findpages = true)
{
    global $page_begin, $page_end, $domain;
    static $page_number = 1;
    $page_number_link = &$page_number;
    $page_pagination = '?page='.$page_number_link.'&_=1604231408389';
    $parse_result = array();
    $n = 10;


    // загрузка урла
    $data = file_get_html($url);
    // очистка от лишнего
    foreach($data->find('script,link,comment') as $tmp)$tmp->outertext = '';

   if($findpages)
   {
        // Имя товара
        foreach ($data->find('span[itemprop="name"]') as $name) 
        {
            $product['name'] = $name->innertext;
        }

        // Цена товара
        foreach ($data->find('span[data-price]') as $price) 
        {
            $price->innertext = str_replace(' руб.', '', $price->innertext);
            trim($price->innertext);
            $product['price'] = $price->innertext;
        }

        // Наличие + регулярка на убирание ненужной херни
        foreach ($data->find('div.stocks') as $stocks) 
        {
            $stocks->innertext = preg_replace('(<([^>]+)>)', '', $stocks->innertext);
            $product['stocks'] = $stocks->innertext;
        }     



        // // Картинка главная
        // foreach ($data->find('img[itemprop="image"]') as $img) {
        //     $product['img'] = $domain . $img->src;
        // }
        // // Все остальные картинки
        // $secondary_img_number = 1;
        // foreach ($data->find('a[data-fancybox-group="thumb"]') as $secondary_img) 
        // {
        //     $product['img_' . $secondary_img_number] = $domain . $secondary_img->href;
        //     $secondary_img_number++;
        // }


        // ==== Статы ====
        foreach ($data->find('#features') as $features) 
        {
            // Поставить главный заголовок вмето кучи тего в начале
            $features->innertext = str_replace('<div class="panel-body"><dl class="expand-content">', 'Просто текст про то, что это характеристики', $features->innertext);

            // Разделить текст на массив. Разбивка по элементу
            $features->innertext = explode('<dt class="divider">', $features->innertext);
            // $product['features'] теперь массив, разбитый по строкам-категориям статов
            $product['features'] = $features->innertext;


            $feature_list = array();


            // ===) Делим весь получнный текст так, чтобы каждый элемент массива начинался с заголовка свойств, а далее шли сами свойсвта еще с тегами. 
            foreach ($product['features'] as $key => $feature_class)
            {
                $feature_class = explode( '</dt><dd class="divider"></dd><dt>', $feature_class );
                $feature_list[] = $feature_class;
            }
            // ----) Получаю массив, внутри которого заголовок блока статов и заголовок стата + стат вместе с тегами, еще не очищен


            // var_dump($feature_list);


            /*  ====)
                1. Прогоняю основной массив через форич
                    2. Каждый субмассив тоже прогоняю через форич
            */
            $temp = array();
            $ar_tag_cleaning = array();
            $counter = 0;
            $desctiption = '';

            foreach ($feature_list as $key => $feature) 
            {
                /*  ====) 
                    Пока это просто Массив по принципу:
                    0. Главный заголовок
                    n.
                        n[0] Заголовок категории статов
                        n[1] Вс статы в этой категории одной строкой вместе с тегами
                */



                foreach ($feature as $key2 => $value) 
                { 
                    // 1. Делим строкку на объект, где 0 — Заголовок, а все остальное — свойства
                    $value = explode( '</dd><dt>', $value);
                    $temp[] = $value;
                    $value1 = $value;
                    foreach ($value1 as $key3 => $tag_cleaning) 
                    {
                        if ($counter == 0){}
                        elseif ($counter % 2) 
                        {
                            // Действие для нечетных (Заголовков)
                            // echo '<br>' . $counter . ' Нечетный — Заголовок';   
                            $tag_cleaning = str_replace('</dt><dd>', ': ', $tag_cleaning );
                            $tag_cleaning = preg_replace('(<([^>]+)>)', '', $tag_cleaning );
                            // $csv_titles[] = '<br><p><b>' . $tag_cleaning.' '.$key.'<b/></p>';
                            $desctiption.= '<br><p><b>' . $tag_cleaning.'</b></p>';
                        }
                        else
                        {
                            // Действие для четных (Текста)
                            // echo $counter . ' четный — Статы';
                            $tag_cleaning = str_replace('</dt><dd>', ': ', $tag_cleaning );
                            $tag_cleaning = preg_replace('(<([^>]+)>)', '', $tag_cleaning );
                            // $csv_rows[] = '<p>' . $tag_cleaning .' '.$key.'</p>';
                            $desctiption.= '<p>' . $tag_cleaning .'</p>';
                        }
                    }
                    $counter++;
                }
            }
            // ----) На выходе получаем готовое описание одной строкой для опенкарта
            $product['features'] = $desctiption;


            // Нарезка фильтров и дополнительных столбцов из $product['features']

            // Производитель
            if ($product['manufacturer'] = stristr($desctiption, 'Брэнд:'))
            {
                $product['manufacturer'] = stristr($product['manufacturer'], '</p>', true);
                $product['manufacturer'] = str_replace('Брэнд: ', '', $product['manufacturer']);           
            }
            else
            {
                $product['manufacturer'] = ' ';
            }

            // =category=
            if ($product['category'] = stristr($desctiption, 'Конструкция:'))
            {
                $product['category'] = stristr($product['category'], '</p>', true);
                $product['category'] = str_replace('Конструкция: ', '', $product['category']);

            }
            else
            {
                $product['category'] = ' ';
            }
            // -category-

            // =color=
            if ($product['color'] = stristr($desctiption, 'Цвет:'))
            {
                $product['color'] = stristr($product['color'], '</p>', true);
                $product['color'] = str_replace('Цвет: ', '', $product['color']);
                $product['color'] = trim($product['color']);
                $product['color'] = mb_ucfirst($product['color']);

            }
            else
            {
                $product['color'] = ' ';
            }
            // -color-

            // =productivity=
            if ($product['productivity'] = stristr($desctiption, 'Производительность:'))
            {
                $product['productivity'] = stristr($product['productivity'], '</p>', true);
                $product['productivity'] = str_replace('Производительность: ', '', $product['productivity']);

            }
            else
            {
                $product['productivity'] = ' ';
            }
            // -productivity-

            // =controller=
            if ($product['controller'] = stristr($desctiption, 'Управление:'))
            {
                $product['controller'] = stristr($product['controller'], '</p>', true);
                $product['controller'] = str_replace('Управление: ', '', $product['controller']);
            }
            else
            {
                $product['controller'] = ' ';
            }
            // -controller-
            $parse_result[] = $product;
        }

        // Картинка главная
        foreach ($data->find('img[itemprop="image"]') as $img) {
            $parse_result['img'] = $domain . $img->src;
        }
        // Все остальные картинки
        $secondary_img_number = 1;
        foreach ($data->find('a[data-fancybox-group="thumb"]') as $secondary_img) 
        {
            $parse_result['img_' . $secondary_img_number] = $domain . $secondary_img->href;
            $secondary_img_number++;
        }


   }


    $fp = fopen('grandgermes-products.txt', 'a+');
    $send_to_text;

    foreach ($parse_result as $fields) 
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
     var_dump($send_to_text);




   $data->clear();// подчищаем за собой
   unset($data);
}
// ---- Get products ----


$page_begin = 1;
$page_end = 10;
$domain = 'http://grand-germes.by';
$url = 'http://grand-germes.by/';

// $url = 'http://grand-germes.by/product/vytyazhka-germes-loksa-50-chyornaya/';
// getProducts($url);

foreach ($all_links as $link) {
    getProducts($link);
}










            // $url = 'http://grand-germes.by/';


            // $parce_final = array();
            // $product_pages = array();

            // $page_number = 1;
            // $page_number_link = &$page_number;
            // // $page_end = 6;
            // $page_pagination = '?page='.$page_number_link.'&_=1604231408389';

            // $html = file_get_html($url . $page_pagination);

            // //чистка от говна лишнего
            // foreach($data->find('script,link,comment') as $tmp)$tmp->outertext = '';


            // for ($page_number; $page_number < 11; $page_number++){
            //     foreach ($html->find('div.name a') as $element) { //выбираем все li сообщений
            //         $product = $element->href;
            //         $product_link = $element->href;
            //         // echo $element . '<br>';
            //         $product_pages[] = 'http://grand-germes.by' . $product;
            //     }


            //      // как-то их обрабатываем
            //      $html->clear(); // подчищаем за собой
            //      unset($html);
            //  }


            // var_dump($product_pages);