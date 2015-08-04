<?php

class FavoritePostController extends BaseController
{
    // Index action for returning favorite posts
    public function indexAction($userID, $last, $limit)
    {
        // Initialize feed array
        $feed = [];

        // Initialize last
        if($last == 0)
        {
            $last = $this->getLast();
        }

        // Find favorite posts
        $favoritePosts = FavoritePost::find([
            "conditions" => "user_id = :userID: AND id < :last: AND is_deleted = 0",
            "order" => "id DESC",
            "limit" => $limit,
            "bind" => ["userID" => $userID, "last" => $last]
        ]);

        foreach($favoritePosts as $favoritePost)
        {
            // Find post
            $post = $favoritePost->getPost("is_blocked = 0");

            if($post == true)
            {
                // Dynamically add new properties (username and vote counts) to post
                $post->last = $favoritePost->id;
                $post->username = $post->getUser()->username;
                $post->votes = $post->getVote()->count();
                $post->voted_zero = $post->getVote("vote = 0")->count();

                // Find vote
                $post->vote = Vote::findFirst([
                    "conditions" => "post_id = :postID: AND user_id = :userID:",
                    "bind" => ["postID" => $post->id, "userID" => $userID]
                ]);

                // Find hashtags
                $hashtags = [];
                foreach($post->getHashtag(["order" => "position ASC"]) as $hashtag)
                {
                    // Push hashtag names into array
                    array_push($hashtags, $hashtag->name);
                }

                // Flatten hashtag array into string
                $post->hashtags = implode(' ', $hashtags);

                array_push($feed, $post);
            }
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['feed' => $feed]]);
    }

	// Create action to favorite post
	public function createAction()
	{
		// Get json body
		if($json = $this->request->getJsonRawBody())
		{
			// Authenticate API key
			if(!isset($json->api_key) || $this->authenticate($json->api_key))
			{
				return $this->response(400, 'Bad Request', ['error' => ['API key is incorrect or missing']]);
			}

			// Verify user isn't trying to favorite their own post
			$post = Post::findFirst($json->post_id);
			if($post->user_id == $json->user_id)
			{
				// Return conflict
				return $this->response(409, 'Conflict', ['error' => 'User cannot favorite their own post']);
			}

            // Find favorite post
            $favoritePost = FavoritePost::findFirst([
                "conditions" => "post_id = :postID: AND user_id = :userID:",
                "bind" => ["postID" => $json->post_id, "userID" => $json->user_id]
            ]);

            // Update favorite post if true
            if($favoritePost == true)
            {
                // Check if user already has post set as favorite
                if($favoritePost->is_deleted == 0)
                {
                    // Return OK
                    return $this->response(200, 'OK', ['response' => ['User already favoriting post']]);
                }

                // Undo soft delete
                $favoritePost->is_deleted = 0;

                // Update favorite post
                if($favoritePost->update() == true)
                {
                    // Return OK
                    return $this->response(200, 'OK', ['response' => ['favorite_post' => $favoritePost]]);
                }

                // Return conflict
                return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($favoritePost)]);
            }

			// Initialize favorite post (create favorite since doesn't exist)
			$favoritePost = new FavoritePost();
			$favoritePost->post_id = $json->post_id;
			$favoritePost->user_id = $json->user_id;
			$favoritePost->created_at = date('Y-m-d H:i:s');
            $favoritePost->is_deleted = 0;

			// Create favorite post
			if($favoritePost->create() == true)
			{
				// Return created
				return $this->response(201, 'Created', ['response' => ['favorite_post' => $favoritePost]]);
			}

            // Return conflict
            return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($favoritePost)]);
		}

        // Return bad request
        return $this->response(400, 'Bad Request', ['error' => ['Request body should be sent as JSON']]);
	}

    // Delete action to soft delete a favorite post
    public function deleteAction($postID, $userID)
    {
        // Find favorite post
        $favoritePost = FavoritePost::findFirst([
            "conditions" => "post_id = :postID: AND user_id = :userID: AND is_deleted = 0",
            "bind" => ["postID" => $postID, "userID" => $userID]
        ]);

        // If true, soft delete
        if($favoritePost == true)
        {
            // Update follow
            $favoritePost->is_deleted = 1;
            if($favoritePost->update() == true)
            {
                // Return OK
                return $this->response(200, 'OK', ['response' => ['favorite_post' => $favoritePost]]);
            }

            // Return conflict
            return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($favoritePost)]);
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['User never favorited post']]);
    }

    // Count returns the amount of favorite posts
    public function count($userID)
    {
        return FavoritePost::find(["conditions" => "user_id = :userID: AND is_deleted = 0", "bind" => ["userID" => $userID]])->count();
    }

    // Return id of last favorite post
    public function getLast()
    {
        $favoritePost = FavoritePost::findFirst(["order" => "id DESC"]);

        if($favoritePost == true)
        {
            return $favoritePost->id + 1;
        }
        else
        {
            return 1;
        }
    }
}