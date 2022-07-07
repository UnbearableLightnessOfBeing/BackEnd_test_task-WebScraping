<?php


class Api {
    private $connecion;
    private $table = 'posts';


    function __construct($db)
    {
        $this->connecion = $db;
    }

    //Methods
    public function getPosts(){
        $sqlQuery = "SELECT id, title, overview, SUBSTRING(text, 1, 200), rating FROM ". $this->table;
        $result = mysqli_query($this->connecion, $sqlQuery);
        if($result && mysqli_num_rows($result)>0){
            // $rawData = mysqli_fetch_all($result);
            $rawData = mysqli_fetch_all($result);
            $posts= array();
            foreach($rawData as $rawPost){
                array_push($posts, array(
                    'title' => $rawPost[1],
                    'overview' => $rawPost[2],
                    'text' => $rawPost[3] . '...',
                    'rating' => (int)$rawPost[4],
                    'link' => 'http://localhost/WebScraping/api/posts/' . $rawPost[0]
                ));
            }
            print_r(json_encode($posts));
        }
        else{
            http_response_code(404);
            $response = [
                'status' => false,
                'message' => 'Posts not found'
            ];
            print_r(json_encode($response));
        }
    }

    public function getSinglePost($postId){
        $sqlQuery = "SELECT title, overview, text, picture, rating FROM ". $this->table . " WHERE id = '$postId' LIMIT 0,1";
        $result = mysqli_query($this->connecion, $sqlQuery);
        if($result && mysqli_num_rows($result)>0){
            // $rawData = mysqli_fetch_all($result);
            $post = mysqli_fetch_assoc($result);
            print_r(json_encode($post));
        }
        else{
            http_response_code(404);
            $response = [
                'status' => false,
                'message' => 'Post not found'
            ];
            print_r(json_encode($response));
        }
    }

    public function updateRating($postId, $newRating){
        if($newRating > 0 && $newRating <=10 ){
            $sqlQuery = "UPDATE ". $this->table ." SET rating = '$newRating' WHERE id = '$postId'";
            mysqli_query($this->connecion, $sqlQuery);
            $response = [
                'status' => true,
                'message' => 'Rating has been updated!'
            ];
            print_r(json_encode($response));
        }
        else{
            http_response_code(404);
            $response = [
                'status' => false,
                'message' => 'Wrong rating (should be 1 to 10)'
            ];
            print_r(json_encode($response));
        }
    }


}