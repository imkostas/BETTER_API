<?php

class HashtagController extends BaseController
{
    public function indexAction($name, $limit)
    {
        $hashtags = Hashtag::find([
            "conditions" => "name LIKE :name:",
            "limit" => $limit,
            "bind" => ["name" => $name . '%'],
            "columns" => "id, name"
        ]);

        // Return OK
        return $this->response(200, 'OK', ['response' => ['hashtags' => $hashtags->toArray()]]);
    }
}