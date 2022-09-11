<?php

namespace App\Parser;


require_once(__DIR__ . './news-parser.class.php');
require_once(__DIR__ . '/../config/cors.inc.php');

// $rbcNewsParser = new  NewsParser('https://realty.rbc.ru/?utm_source=topline', 15);
$rbcNewsParser = new  NewsParser('https://www.rbc.ru/', 15);
$rbcNewsParser->setLinkPath('.js-news-feed-list > .news-feed__item');

// rbc.ru site publishes different kinds of news posts with different DOM structure, so the parser looks for an appropriate set of paths to each post
// сайт rbc.ru публикует различные виды новостных постов с разной структурой DOM, поэтому парсер ищет подходящие параметры для каждого поста

$rbcNewsParser->setTitlePath('.article__content .article__header .article__header__title h1',
                             '.article__main .article__header .article__header-right .article__title',
                             '.interview__container .interview__header h1',
                             '.section--main .section__container .section__title > span');
$rbcNewsParser->setOverviewPath('.article__content .article__text__overview span',
                                '.article__main .article__header .article__header-right p',
                                '.interview__container .interview__header .interview__desc',
                                '.article .section__container .article__main-row .article__main-txt');
$rbcNewsParser->setTextPath('.article__content .article__text  p',
                            '.article__main .article__content > *',
                            '.interview__container > div',
                            '.article .section .container > * > *');
$rbcNewsParser->setPicturePath('.article__content .article__text .article__main-image img',
                               '.article__main .article__header .lazy-blur__imgS',
                               '',
                               '.article .section__container .article__main-row .article__main-img > img');

$rbcNewsParser->parseNews();
// $rbcNewsParser->showPostLinks();
$rbcNewsParser->showPosts();
$rbcNewsParser->loadToDB();





// $mailNews = new  NewsParser('https://news.mail.ru/', 15);
// $mailNews->setLinkPath('.js-module .block .wrapper .js-topnews__item,  .newsitem__title , .link-holder');
// $mailNews->setTitlePath('.js-module .block .wrapper .cols__inner .hdr__wrapper .hdr__inner');
// $mailNews->setOverviewPath('.js-module .block .wrapper .article__intro > p');
// $mailNews->setTextPath('.js-module .block .wrapper .article__text .article__item > p');
// $mailNews->setPicturePath('.js-module .block .wrapper .article__container .article-photo .photo__inner img');

// $mailNews->parseNews();
// // $mailNews->showPostLinks();
// $mailNews->showPosts();
// $mailNews->loadToDB();



?>