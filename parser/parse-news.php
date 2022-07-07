<?php

require_once('./news-parser.class.php');
require_once('../config/cors.inc.php');


$rbcNewsParser = new  NewsParser('https://realty.rbc.ru/?utm_source=topline', 15);
$rbcNewsParser->setLinkPath('.js-news-feed-list > a');
$rbcNewsParser->setTitlePath('.article__content .article__header .article__header__title h1');
$rbcNewsParser->setOverviewPath('.article__content .article__text__overview span');
$rbcNewsParser->setTextPath('.article__content .article__text  p');
$rbcNewsParser->setPicturePath('.article__content .article__text .article__main-image img');

$rbcNewsParser->parseNews();
// $rbcNewsParser->showPostLinks();
$rbcNewsParser->showPosts();
$rbcNewsParser->loadToDB();

// $mailNews = new  NewsParser('https://news.mail.ru/', 15);
// $mailNews->setLinkPath('.js-module .block .wrapper .js-topnews__item,  .newsitem__title , .link-holder');
// $mailNews->setTitlePath('.js-module .block .wrapper .hdr__wrapper .hdr__inner');
// $mailNews->setOverviewPath('.js-module .block .wrapper .article__intro > p');
// $mailNews->setTextPath('.js-module .block .wrapper .article__text .article__item > p');
// $mailNews->setPicturePath('.js-module .block .wrapper .article__container .article-photo .photo__inner img');

// $mailNews->parseNews();
// // $mailNews->showPostLinks();
// $mailNews->showPosts();
// $mailNews->loadToDB();



?>