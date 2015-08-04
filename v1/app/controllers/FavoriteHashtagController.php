<?php

class FavoriteHashtagController extends BaseController
{
    // Index action for returning favorite hashtags
    public function indexAction($userID, $last, $limit)
    {
        // Initialize favorites array
        $favorites = [];

        // Initialize last
        if($last == 0)
        {
            $last = $this->getLast();
        }

        // Find favorites
        $favoriteHashtags = FavoriteHashtag::find([
            "conditions" => "user_id = :userID: AND id < :last: AND is_deleted = 0",
            "order" => "id DESC",
            "limit" => $limit,
            "bind" => ["userID" => $userID, "last" => $last]
        ]);

        // For each favorite find hashtag
        foreach($favoriteHashtags as $favoriteHashtag)
        {
            // Create base object and dynamically add needed data (id, hashtag_id, hashtag name)
            $favorite = new stdClass();
            $favorite->id = $favoriteHashtag->id;
            $favorite->hashtag_id = $favoriteHashtag->hashtag_id;
            $favorite->name = $favoriteHashtag->getHashtag([
                "conditions" => "id = :hashtagID:",
                "bind" => ["hashtagID" => $favoriteHashtag->hashtag_id],
                "columns" => "name"
            ])->name;

            // Push favorite
            array_push($favorites, $favorite);
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['favorite_hashtags' => $favorites]]);
    }

	// Create action for favoriting hashtags
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

            // Find favorite hashtag
            $favoriteHashtag = FavoriteHashtag::findFirst([
                "conditions" => "hashtag_id = :hashtagID: AND user_id = :userID:",
                "bind" => ["hashtagID" => $json->hashtag_id, "userID" => $json->user_id]
            ]);

            // Update favorite hashtag if true
            if($favoriteHashtag == true)
            {
                // Check if user already has hashtag set as favorite
                if($favoriteHashtag->is_deleted == 0)
                {
                    // Return OK
                    return $this->response(200, 'OK', ['response' => ['User already favoriting hashtag']]);
                }

                // Undo soft delete
                $favoriteHashtag->is_deleted = 0;
                $favoriteHashtag->created_at = date('Y-m-d H:i:s');

                // Update favorite hashtag
                if($favoriteHashtag->update() == true)
                {
                    // Return OK
                    return $this->response(200, 'OK', ['response' => ['$favorite_hashtag' => $favoriteHashtag]]);
                }

                // Return conflict
                return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($favoriteHashtag)]);
            }
			
			// Initialze favorite hashtag (create favorite since doesn't exist)
			$favoriteHashtag = new FavoriteHashtag();
			$favoriteHashtag->hashtag_id = $json->hashtag_id;
			$favoriteHashtag->user_id = $json->user_id;
			$favoriteHashtag->created_at = date('Y-m-d H:i:s');
            $favoriteHashtag->is_deleted = 0;

			// Create favorite hashtag
			if($favoriteHashtag->create() == true)
			{
				// Return created
				return $this->response(201, 'Created', ['response' => ['favorite_hashtag' => $favoriteHashtag]]);
			}

            // Return conflict
            return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($favoriteHashtag)]);
		}

        // Return bad request
        return $this->response(400, 'Bad Request', ['error' => ['Request body should be sent as JSON']]);
	}

    // Delete action to soft delete a favorite hashtag
    public function deleteAction($hashtagID, $userID)
    {
        // Find favorite hashtag
        $favoriteHashtag = FavoriteHashtag::findFirst([
            "conditions" => "hashtag_id = :hashtagID: AND user_id = :userID: AND is_deleted = 0",
            "bind" => ["hashtagID" => $hashtagID, "userID" => $userID]
        ]);

        // If true, soft delete
        if($favoriteHashtag == true)
        {
            // Update follow
            $favoriteHashtag->is_deleted = 1;
            if($favoriteHashtag->update() == true)
            {
                // Return OK
                return $this->response(200, 'OK', ['response' => ['favorite_hashtag' => $favoriteHashtag]]);
            }

            // Return conflict
            return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($favoriteHashtag)]);
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['User never favorited hashtag']]);
    }

    // Count returns the amount of favorite hashtags
    public function count($userID)
    {
        return FavoriteHashtag::find(["conditions" => "user_id = :userID: AND is_deleted = 0", "bind" => ["userID" => $userID]])->count();
    }

    // Return id of last favorite hashtag
    public function getLast()
    {
        $favoriteHashtag = FavoriteHashtag::findFirst(["order" => "id DESC"]);

        if($favoriteHashtag == true)
        {
            return $favoriteHashtag->id + 1;
        }
        else
        {
            return 1;
        }
    }
}