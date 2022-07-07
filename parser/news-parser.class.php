<?php
require_once('../phpQuery/phpQuery/phpQuery.php');
require_once('../config/db.class.php');

class NewsParser{

    private $pq = NULL;
    private $count = 0;

    private $linkPath = "";
    private $titlePath = "";
    private $overviewPath = "";
    private $textPath = "";
    private $picturePath = "";

    private $linkArray = array();
    private $posts = array();


    public function __construct($url, $count){
        $result = $this->setUrl($url);
        $this->pq = phpQuery::newDocument($result);
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function setLinkPath($linkPath) {
        $this->linkPath = $linkPath;
    }
    public function setTitlePath($titlePath) {
        $this->titlePath = $titlePath;
    }
    public function setOverviewPath($overviewPath) {
        $this->overviewPath = $overviewPath;
    }
    public function setTextPath($textPath) {
        $this->textPath = $textPath;
    }
    public function setPicturePath($picturePath) {
        $this->picturePath = $picturePath;
    }

    public function parseNews(){
        $this->getPostLinks();
        $this->getPosts();
    }

    private function getPostLinks(){
        $linkList = $this->pq->find($this->linkPath);
        $counter = 1;
        foreach($linkList as $link){
            $this->linkArray[] = pq($link)->attr('href');
            if($counter == $this->count){
                break;
            }else $counter++;
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
            $pq = phpQuery::newDocument($post);

            $this->posts[] = [
                "title" => $this->getTextElement($pq, $this->titlePath),
                "overview" => $this->getTextElement($pq, $this->overviewPath),
                "text" => $this->getTextElement($pq, $this->textPath),
                "picture" => $pq->find($this->picturePath)->attr('src'),
                "rating" => rand(1,10),
                "link" => $link
            ];
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

    public function loadToDB(){
        $db = new Database();
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
            if(!$result){
                $sqlQuery = null;
                // header("location: ../index.php?error=queryfailed");
                exit("Failed to push the data into the database.");
            }
            $sqlQuery = null;
        }
    }
}