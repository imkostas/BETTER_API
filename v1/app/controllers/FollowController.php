<?php

class FollowController extends BaseController
{
    // Following action returns list of following
    public function followingAction($userID, $last, $limit)
    {
        // Initialize following array
        $following = [];

        // Initialize last
        if($last == 0)
        {
            $last = $this->getLast();
        }

        // Find following
        $follow = Follow::find([
            "conditions" => "follower_id = :userID: AND id < :last: AND is_deleted = 0",
            "order" => "id DESC",
            "limit" => $limit,
            "bind" => ["userID" => $userID, "last" => $last]
        ]);

        foreach($follow as $f)
        {
            // Create base object and dynamically add needed data (id, user_id, and username)
            $obj = new stdClass();
            $obj->id = $f->id;
            $obj->user_id = $f->user_id;
            $obj->username = User::findFirst(["conditions" => "id = :userID:", "bind" => ["userID" => $f->user_id]])->username;

            // Push into following
            array_push($following, $obj);
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['following' => $following]]);
    }

    // Follower action returns list of followers
    public function followerAction($userID, $last, $limit)
    {
        // Initialize followers array
        $followers = [];

        // Initialize last
        if($last == 0)
        {
            $last = $this->getLast();
        }

        // Find followers
        $follow = Follow::find([
            "conditions" => "user_id = :userID: AND id < :last: AND is_deleted = 0",
            "order" => "id DESC",
            "limit" => $limit,
            "bind" => ["userID" => $userID, "last" => $last]
        ]);

        foreach($follow as $follower)
        {
            // Create base object and dynamically add needed data (id, user_id, and username)
            $obj = new stdClass();
            $obj->id = $follower->id;
            $obj->user_id = $follower->follower_id;
            $obj->username = User::findFirst(["conditions" => "id = :userID:", "bind" => ["userID" => $follower->follower_id]])->username;

            // Find if user follows the follower
            $following = Follow::findFirst([
                "conditions" => "user_id = :userID: AND follower_id = :followerID: AND is_deleted = 0",
                "bind" => ["userID" => $follower->follower_id, "followerID" => $userID]
            ]);
            $obj->following = ($following == true)?1:0;

            // Push follower into list of followers
            array_push($followers, $obj);
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['followers' => $followers]]);
    }

	// Create action for following users
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

			// Verify user isn't trying to follow themselves
			if($json->user_id == $json->follower_id)
			{
				// Return conflict
				return $this->response(409, 'Conflict', ['error' => 'User cannot follow themselves']);
			}

            // Look for existing follow record
			$follow = Follow::findFirst([
				"conditions" => "user_id = :userID: AND follower_id = :followerID:",
				"bind" => ["userID" => $json->user_id, "followerID" => $json->follower_id]
			]);

            // Update follow if true
			if($follow == true)
			{
                if($follow->is_deleted == 0)
                {
                    // Return OK
                    return $this->response(200, 'OK', ['response' => ['User was already following']]);
                }

                // Undo soft delete
                $follow->is_deleted = 0;

                // Update follow
                if($follow->update() == true)
                {
                    // Return OK
                    return $this->response(200, 'OK', ['response' => ['follow' => $follow]]);
                }

                // Return conflict
                return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($follow)]);
			}

            // Initialize follow (create follow since doesn't exist)
            $follow = new Follow();
            $follow->user_id = $json->user_id;
            $follow->follower_id = $json->follower_id;
            $follow->created_at = date('Y-m-d H:i:s');
            $follow->is_deleted = 0;

            // Create follow
            if($follow->create() == true)
            {
                // Return created
                return $this->response(201, 'Created', ['response' => ['follow' => $follow]]);
            }

            // Return conflict
            return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($follow)]);
		}

		// Return bad request
		return $this->response(400, 'Bad Request', ['error' => ['Request body should be sent as JSON']]);
	}

	// Delete action for soft deleting a follow
	public function deleteAction($userID, $followerID)
	{
        // Find follow
		$follow = Follow::findFirst([
			"conditions" => "user_id = :userID: AND follower_id = :followerID: AND is_deleted = 0",
			"bind" => ["userID" => $userID, "followerID" => $followerID]
		]);

        // If true, soft delete
		if($follow == true)
		{
            // Update follow
			$follow->is_deleted = 1;
			if($follow->update() == true)
			{
				// Return OK
				return $this->response(200, 'OK', ['response' => ['follow' => $follow]]);
			}
			
			// Return conflict
			return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($follow)]);
		}

		// Return OK
		return $this->response(200, 'OK', ['response' => ['User was never following or already deleted']]);
	}

    // Count returns either the amount of following or followers
    public function count($following, $userID)
    {
        // Return following count
        if($following == true)
        {
            return Follow::find(["conditions" => "follower_id = :userID: AND is_deleted = 0", "bind" => ["userID" => $userID]])->count();
        }

        // Return follower count
        return Follow::find(["conditions" => "user_id = :userID: AND is_deleted = 0", "bind" => ["userID" => $userID]])->count();
    }

    // Return id of last follow
    public function getLast()
    {
        $follow = Follow::findFirst(["order" => "id DESC"]);

        if($follow == true)
        {
            return $follow->id + 1;
        }
        else
        {
            return 1;
        }
    }
}