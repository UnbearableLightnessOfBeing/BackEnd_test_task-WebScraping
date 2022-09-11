<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Models\NewsPosts;
use App\App;


class NewsController
{

    protected NewsPosts $newsPosts;
    public function __construct(){

        $this->newsPosts = new NewsPosts();
    }

    public function index(): string
    {
        $posts = $this->newsPosts->getPosts();

        return json_encode($posts);
    }

    public function show(): string {

        $id = (int) App::getArrayOfParams($_SERVER['REQUEST_URI'])[1] ?? 0;

        $post = $this->newsPosts->getSinglePost($id);
        
        return json_encode($post);
    }

    public function edit(int $id): string {

        $putdata = file_get_contents("php://input");
        $decoded = json_decode($putdata, true);
        
        if(isset($decoded['rating'])) {
            $newRating = (int) $decoded['rating'];

            if($newRating < 0 || $newRating > 10) {
                http_response_code(400);
                return json_encode([
                    'status' => '400',
                    'message' => 'Rating should be in range of 0 to 10'
                ]);
            }

            $this->newsPosts->updateRating($id, $newRating);
            return json_encode([
                'status' => '200',
                'message' => 'Rating has been updated'
            ]);
        }else {
            http_response_code(400);
            return json_encode([
                'status' => '400',
                'message' => 'Rating is required'
            ]);
        }
    }

    public function refresh() {
        $this->newsPosts->refreshPosts();
        http_response_code(200);
            return json_encode([
                'status' => '200',
                'message' => 'Posts have been refreshed'
            ]);
    }
}
