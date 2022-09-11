<?php


namespace App\Parser;


require_once(__DIR__ . '/../phpQuery/phpQuery/phpQuery.php');
require_once(__DIR__ . '/../config/db.class.php');
require_once(__DIR__ . '/../Model.php');

// use App\Model;

class NewsParser{

    private $pq = NULL;
    private $count = 0;

    private $linkPath = "";
    private $titlePath = array();
    private $overviewPath = array();
    private $textPath = array();
    private $picturePath = array();

    private $linkArray = array();
    private $posts = array();


    public function __construct($url, $count){
        $result = $this->setUrl($url);
        $this->pq = \phpQuery::newDocument($result);
        $this->count = $count;
    }

    private function setUrl($url){
        $headers = array(
            'cache-control: max-age=0',
            'upgrade-insecure-requests: 1',
            'user-agent: Mozila/5.0 (Windows NT 6.1) ApplWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',
            'sec-fetch-user: ?1',
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'x-compress: null',
            'sec-fetch-site: none',
            'sec-fetch-mode: navigate',
            'accept-encoding: deflate, br',
            'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7'
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        // echo $result;
        curl_close($ch);
        return $result;
    }

    public function setLinkPath($linkPath) {
        $this->linkPath = $linkPath;
    }
    public function setTitlePath(...$titlePath) {
        foreach($titlePath as $option){
            $this->titlePath[] = $option;
        }
    }
    public function setOverviewPath(...$overviewPath) {
        foreach($overviewPath as $option){
            $this->overviewPath[] = $option;
        }
    }
    public function setTextPath(...$textPath) {
        foreach($textPath as $option){
            $this->textPath[] = $option;
        }
    }
    public function setPicturePath(...$picturePath) {
        foreach($picturePath as $option){
            $this->picturePath[] = $option;
        }
    }

    public function parseNews(){
        $this->getPostLinks();
        $this->getPosts();
    }

    private function getPostLinks(){
        $linkList = $this->pq->find($this->linkPath);
        $counter = 1;
        foreach($linkList as $link){
            if($counter >= $this->count){
                return;
            }else{
                $counter++;
                $this->linkArray[] = pq($link)->attr('href');
            }
        }
    }
    public function showPostLinks(){
        echo '<pre>';
        var_dump($this->linkArray);
        echo '</pre>';
    }

    private function getPosts(){
        foreach($this->linkArray as $link){
            $post = $this->setUrl($link);
            $pq = \phpQuery::newDocument($post);
            $index = 0;
            do{
                if($index != 0){
                    array_pop($this->posts);
                }
                $this->posts[] = [
                    "title" => $this->getTextElement($pq, $this->titlePath[$index]),
                    "overview" => $this->getTextElement($pq, $this->overviewPath[$index]),
                    "text" => $this->getTextElement($pq, $this->textPath[$index]),
                    // "picture" => $pq->find($this->picturePath[$index])->attr('src'),
                    "picture" => $this->filterPictureLink($pq->find($this->picturePath[$index])->attr('src'), $link),
                    "rating" => rand(1,10),
                    "link" => $link
                ];
                $index ++;
            }
            // if there is no title and text in a post, then try the next settings
            while(!$this->posts[count($this->posts)-1]['title'] &&  
                   !$this->posts[count($this->posts)-1]['text']);
        }
    }

    private function filterPictureLink($pictureLink, $postLink){
        if($pictureLink && substr($pictureLink, 0 , 4) != 'http'){
            return $this->changePictureLink($pictureLink, $postLink);
        }else {
            return $pictureLink;
        }
    }

    private function changePictureLink($pictureLink, $postLink){
        $domainEnding = $this->findDomainEnding($postLink, '.com/', '.ru/'); // more endings can be added
        return substr($postLink, 0, strpos($postLink, $domainEnding) + strlen($domainEnding)) . substr($pictureLink, 2);
    }

    private function findDomainEnding($postLink, ...$endings){
        foreach($endings as $ending){
            if(strpos($postLink, $ending)){
                return $ending;
            }
        }
    }

    private function getTextElement($pq ,$elementPath){
        if($elementPath !== "" && $elementPath !== NULL){
            return $pq->find($elementPath)->text();
        }
        else{
            return "";
        }
    }
    public function showPosts(){
        echo '<pre>';
        var_dump($this->posts);
        echo '</pre>';
    }

    public function posts() {
        return $this->posts;
    }

    public function loadToDB(){
        $db = new \Database();
        //clean up the database and reset the autoincrement
        $sqlQuery = "DELETE FROM posts";
        mysqli_query($db->connect(), $sqlQuery);


        $sqlQuery = "ALTER TABLE posts AUTO_INCREMENT = 1";
        mysqli_query($db->connect(), $sqlQuery);


        //putting data into the database
        foreach($this->posts as $post){
            $sqlQuery = "insert into posts (title, overview, text, picture, rating, link) values ('$post[title]', '$post[overview]', '$post[text]',
             '$post[picture]', '$post[rating]', '$post[link]')";
            $result = mysqli_query($db->connect(), $sqlQuery);

            if(!$sqlQuery){
                // header("location: ../index.php?error=queryfailed");
                exit("Failed to push data into the database.");
            }
        }
    }
}