<?php

// include('./simple_html_dom.php');

// $html = file_get_html('https://news.mail.ru/story/politics/ukraine_conflict/');

// // find all link
// foreach($html->find('a') as $e) 
//     echo $e->href . '<br>';




$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTMLFile('https://news.mail.ru/');



// $ch = curl_init('https://mail.ru/');
// // curl_setopt($ch, CURLOPT_URL, 'https://mail.ru/');
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// $html = curl_exec($ch);

// $dom = new DOMDocument();
// $dom->loadHTMLFile($html);

$posts = array();

$links = $dom->getElementsByTagName('a');

$id = 1;
foreach($links as $link){
    $attr = $link->attributes;

    if($attr && $attr->getNamedItem('class')->nodeValue == 'newsitem__title link-holder'|| 
        str_contains($attr->getNamedItem('class')->nodeValue, 'js-topnews__item')){
        // echo '<b>Link: </b>' . $link->getAttribute('href') . '<br>';
        
        parseOnePost($link->getAttribute('href'), $posts, $id);
        // echo '<br><br><br>';
        $id++;
    }
  
}

echo '<pre>';
var_dump($posts);
echo '</pre>';

$jsonData = json_encode($posts);
file_put_contents("jsonData.txt", $jsonData);



function parseOnePost($link, &$posts, $id){
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTMLFile($link);

    //header parsing

    $headers = $dom->getElementsByTagName('h1');

    foreach($headers as $h){
        if($h->attributes->getNamedItem('class')->nodeValue == 'hdr__inner'){
            $title = $h->textContent;

            // $posts->array_push($h->textContent);
            
            // echo '<b>Title: </b>' . $h->textContent;
        }
    }


    // overview parsing
    $divs = $dom->getElementsByTagName('div');

    foreach($divs as $div){

        foreach($div->attributes as $attr){
            // if($attr->nodeValue == 'article__intro meta-speakable-intro'){
            // if(strpos($attr->nodeValue, 'article__intro')){
            if(str_contains($attr->nodeValue, 'article__intro')){
                $overview =  $div->textContent;
                // echo '<br>';
                // echo '<b>Overview: </b>' . $div->textContent;
                
                
            }
            
        }

        // if($div->attributes->getNamedItem('class')->nodeValue == 'article__intro meta-speakable-intro'){
        //     // $overview = $div;
        //     // echo '<b>Overview: </b>' . $overview->textContent;
        //     echo $div->hasAttributes();
        // }
    }

    //text parsing
    $divs = $dom->getElementsByTagName('div');

    foreach($divs as $div){

        foreach($div->attributes as $attr){
            if(str_contains($attr->nodeValue, 'article__text')){
                // echo '<br>';
                // echo '<b>Text: </b>' . $div->textContent;
                // echo '<b>Text: </b>';
                $text = '';
                foreach($div->childNodes as $child){
                    if(!str_contains($child->attributes->getNamedItem('class')->nodeValue, 'article__item_picture') &&
                        !str_contains($child->attributes->getNamedItem('class')->nodeValue, 'article__item_image js-module') &&
                        !str_contains($child->attributes->getNamedItem('class')->nodeValue, 'article__item_embed') &&
                        !str_contains($child->attributes->getNamedItem('class')->nodeValue, 'article__item_crosslink_news js-module' &&
                        $child->textContent != '')){
                        $text .= $child->textContent . "\n";
                        // echo $child->textContent . '<br>';
                    }   
                    // echo $child->attributes->getNamedItem('class')->nodeValue . '<br>';
                }
                
            }
        }
    }

    //pic parsing
    $pics = $dom->getElementsByTagName('img');
    $picture = '';

    foreach($pics as $pic){
        foreach($pic->attributes as $attr){
            if(str_contains($attr->nodeValue, 'picture__image') || $attr->nodeValue == 'photo__pic'){

                // if($pic->parentNode->attributes->getNamedItem('class')->nodeValue == 'photo__inner' || 
                // $pic->parentNode->attributes->getNamedItem('class')->nodeValue == 'picture photo__picture'){
                //     echo '<br>';
                //     echo '<b>Pic: </b>' . $pic->getAttribute('src');
                // }

                if($pic->parentNode->parentNode->parentNode->parentNode->attributes->getNamedItem('class')->nodeValue == 'article-photo' ||
                    $pic->parentNode->parentNode->parentNode->parentNode->attributes->getNamedItem('class')->nodeValue == 'article-photo__inner'){
                    $picture = $pic->getAttribute('src');
                    // echo '<br>';
                    // echo '<b>Pic: </b>' . $pic->getAttribute('src');
                }
                
                
            }
        }
    }
  

    //setting raiting
    $rating = rand(1, 10);
    // echo '<br>';
    // echo '<b>Raiting: </b>' . $rating;


    $posts[] = [
        "id" => $id,
        "link" => $link,
        "title" => $title,
        "overview" => $overview,
        "text" => $text,
        "picture" => $picture,
        "rating" => $rating
    ];
}



