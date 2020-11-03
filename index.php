<?php

require_once 'simple_html_dom.php'; // библиотека для парсинга
            





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






$test_links = array('http://grand-germes.by/product/vytyazhka-grand-gloucester-60-chernyy/',
'http://grand-germes.by/product/100/',
'http://grand-germes.by/product/vytyazhka-grand-gloucester-60-belaya/',
'http://grand-germes.by/product/vytyazhka-grand-corsa/',);







    function getProducts($url,$findpages = true)
    {
        global $page_begin, $page_end, $domain;
        static $page_number = 1;
        $page_number_link = &$page_number;
        $page_pagination = '?page='.$page_number_link.'&_=1604231408389';

        $n = 10;


        // загрузка урла
        $data = file_get_html($url);
        // очистка от лишнего
        foreach($data->find('script,link,comment') as $tmp)$tmp->outertext = '';

       if($findpages)
       {
            // сбор имен
            foreach ($data->find('span[itemprop="name"]') as $name) {
                $product['name'] = $name->innertext;
            }
            foreach ($data->find('span[data-price]') as $price) {
                $price->innertext = str_replace(' руб.', '', $price->innertext);
                trim($price->innertext);
                $product['price'] = $price->innertext;
            }
            // Наличие + регулярка
            foreach ($data->find('div.stocks') as $stocks) {
                $stocks->innertext = preg_replace('(<([^>]+)>)', '', $stocks->innertext);
                $product['stocks'] = $stocks->innertext;
            }     
            
            // Картинки
            foreach ($data->find('img[itemprop="image"]') as $img) {
                $product['img'] = $domain . $img->src;
            }
            foreach ($data->find('a[data-fancybox-group="thumb"]') as $secondary_img) {
                static $secondary_img_number = 1;
                $product['img_' . $secondary_img_number] = $domain . $secondary_img->href;
                $secondary_img_number++;
            }



            // ==== Статы ====
            // foreach ($data->find('dt.divider') as $stats) {
            //     static $stats_index = 1;
            //     $product['stats_'.$stats_index]['stats_title_'.$stats_index] = $stats->innertext;
            //     $stats_index++;
            // }

            // foreach ($data->find('dt') as $feature) {
            //     static $feature_index = 1;
            //     $product['stats_']['stats_descr_name_' . $feature_index] =  $feature->innertext;
            //     $feature_index++;
            // }
            // ---- Статы ----

            // ==== Статы ====
            foreach ($data->find('#features') as $features) {
                $features->innertext = str_replace('<div class="panel-body"><dl class="expand-content">', 'Просто текст про то, что это характеристики', $features->innertext);
                $features->innertext = explode('<dt class="divider">', $features->innertext);
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



                /*
                    ====)
                    1. Прогоняю основной массив через форич
                        2. Каждый субмассив тоже прогоняю через форич
                */
                $temp = array();
                $ar_tag_cleaning = array();
                foreach ($feature_list as $key => $feature) 
                {
                    foreach ($feature as $key2 => $value) 
                    { 
                        // 1. Делим строкку на объект, где 0 — Заголовок, а все остальное — свойства
                        $value = explode( '</dd><dt>', $value);
                        $temp[] = $value;
                        foreach ($value as $key3 => $tag_cleaning) {
                            $tag_cleaning = str_replace('</dt><dd>', ': ', $tag_cleaning );
                            $tag_cleaning = preg_replace('(<([^>]+)>)', '', $tag_cleaning );
                            // var_dump($tag_cleaning);
                            echo $tag_cleaning . ','; 
                            $temp[] = $tag_cleaning;
                        }
                    }
                    echo '<br><br>';
                    // var_dump($temp[$key]);
                    
                }
                // $feature_stats_arr = array();
                // var_dump($temp);

                // // Внутренний преебор
                // foreach ($feature_list as $key => $feature) 
                // {
                //     // Создание массива со всеми сроками
                //     foreach ($feature as $key => $feature_list_stat) {
                //         $feature_list_stat = explode( '</dd><dt>', $feature_list_stat);
                //     }
                //     var_dump($feature);
                //     // 1. Делим строкку на объект, где 0 — Заголовок, а все остальное — свойства
                //     // $feature = explode( '</dd><dt>', $feature);

                //     // foreach ($feature as $key => $feature_string)
                //     // {
                //     //     // 2. Заменяем теги-разделители на двоеточие и пробел
                //     //     $feature_string = str_replace('</dt><dd>', ': ', $feature_string );
                //     //     // 3. Удаляем оставшиеся теги
                //     //     $feature_string = preg_replace('(<([^>]+)>)', '', $feature_string );
                //     //     $feature_clean_string[] = $feature_string;
                //     //     $feature_list[1] = $feature_clean_string;
                //     // }
                // }
                //     // var_dump($feature_list);
                // var_dump($feature_stats_arr);





                // 1. Делим строкку на объект, где 0 — Заголовок, а все остальное — свойства
                // $product['features'][1][1] = explode( '</dd><dt>', $product['features'][1][1]);

                // // 2. Заменяем теги-разделители на двоеточие и пробел
                // $product['features'][1][1] = str_replace('</dt><dd>', ': ', $product['features'][1][1] );

                // // 3. Удаляем оставшиеся теги
                // $product['features'][1][1] = preg_replace('(<([^>]+)>)', '', $product['features'][1][1] );

            }



            // var_dump($product);
            // var_dump($product['features'][1][1]); 
            // var_dump($main_stats);


              // $my_arr = array(1, 2, 3, 4, 5);
               
              // foreach ($my_arr as $key => $value) {
              //   echo "[$key] => ", $value, "<br>";
                // }
       }

    }
    $page_begin = 1;
    $page_end = 10;
    $domain = 'http://grand-germes.by';
    // $url = 'http://grand-germes.by/';
    $url = 'http://grand-germes.by/product/vytyazhka-germes-loksa-50-chyornaya/';
    getProducts($url);


    // foreach ($test_links as $link) {
    //     getProducts($link);
    // }



















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