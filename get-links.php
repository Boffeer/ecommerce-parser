<?php

require_once 'simple_html_dom.php'; // библиотека для парсинга
            
            $url = 'http://grand-germes.by/category/vytyazhki-kukhonnye/';


            $html = file_get_html($url.'?page=6&_=1604231408389');
            $parce_final = array();
            $product_pages = array();

            $page_number = 1;
            $page_number_link = &$page_number;
            // $page_end = 6;
            $page_pagination = '?page='.$page_number_link.'&_=1604231408389';

            foreach ($html->find('div.name a') as $element) { //выбираем все li сообщений
                $product = 'http://grand-germes.by/category/vytyazhki-kukhonnye' . $element->href . '<br>';
                 // = $element->href;
                // echo $element . '<br>';
                $product_pages[] = $product;
                $page_number++;
            }


            for (; $page_number < 10; $page_number++){


                // global $pagination;

                $html = file_get_html($url . $page_pagination);

                foreach ($html->find('div.name a') as $element){
                    $product = 'http://grand-germes.by/category/vytyazhki-kukhonnye' . $element->href . '<br>';
                 // = $element->href;
                // echo $element . '<br>';
                    $product_pages[] = $product;
                }
            }