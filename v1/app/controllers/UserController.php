<?php

class UserController extends BaseController
{
    public function indexAction($username, $limit)
    {
        // Find all users like $username
        $users = User::find([
            "conditions" => "username LIKE :username:",
            "limit" => $limit,
            "bind" => ["username" => $username . '%'],
            "columns" => "id, username"
        ]);

        // Return OK
        return $this->response(200, 'OK', ['response' => ['usernames' => $users->toArray()]]);
    }

    // Count action returns counts of all votes, posts, favorite posts, favorite hashtags, following, and followers
    public function countAction($userID)
    {
        // Vote count
        $voteController = new VoteController();
        $voteCount = $voteController->count($userID);

        // Post count
        $postController = new PostController();
        $postCount = $postController->count($userID);

        // Favorite post count
        $favoritePostController = new FavoritePostController();
        $favoritePostCount = $favoritePostController->count($userID);

        // Favorite hashtag count
        $favoriteHashtagController = new FavoriteHashtagController();
        $favoriteHashtagCount = $favoriteHashtagController->count($userID);

        // Following count
        $followController = new FollowController();
        $followingCount = $followController->count(true, $userID);

        // Follower count
        $followerCount = $followController->count(false, $userID);

        // Return OK
        return $this->response(200, 'OK', ['response' => ['vote_count' => $voteCount,
                                                            'post_count' => $postCount,
                                                            'favorite_post_count' => $favoritePostCount,
                                                            'favorite_hashtag_count' => $favoriteHashtagCount,
                                                            'following_count' => $followingCount,
                                                            'follower_count' => $followerCount]]);
    }

	// Create action for creating user accounts
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

			// Begin manual database transaction
			$this->db->begin();
			
			// Initialize user
			$user = new User();
			$user->username = $json->username;
			$user->email = $json->email;
			$user->gender = $json->gender;
			$user->birthday = $json->birthday;
			$user->country_id = Country::findFirstByName($json->country)->id; // This should always exist - no country should be selected that isn't already stored in database
			$user->created_at = date('Y-m-d H:i:s');
			
			// Set password or facebook id
			if(isset($json->password))
			{
				$user->password = $this->security->hash($json->password);
			}
			else
			{
				$user->facebook_id = $json->facebook_id;
			}
			
			// Create user
			if($user->create() == true)
			{
				// Initialize rank
				$rank = new Rank();
				$rank->user_id = $user->id;
				$rank->rank = 0;
				$rank->total_points = 0;
				$rank->weekly_points = 0;
				$rank->daily_points = 0;
				$rank->badge_tastemaker = 0;
				$rank->badge_adventurer = 0;
				$rank->badge_admirer = 0;
				$rank->badge_role_model = 0;
				$rank->badge_celebrity = 0;
				$rank->badge_idol = 0;

				// Create rank
				if($rank->create() == true)
				{
                    // Initialize notification
                    $notification = new Notification();
                    $notification->user_id = $user->id;
                    $notification->voted_post = 1;
                    $notification->favorited_post = 1;
                    $notification->new_follower = 1;

                    // Create notification
                    if($notification->create() == true)
                    {
                        // Add needed data to user
                        $user->country;
                        $user->rank;
                        $user->notification;

                        // Commit
                        $this->db->commit();

                        // Return created
                        return $this->response(201, 'Created', ['response' => ['user' => $user]]);
                    }

                    // Rollback since notification creation failed
                    $this->db->rollback();

                    // Return conflict
                    return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($notification)]);
				}

                // Rollback since rank creation failed
                $this->db->rollback();

                // Return conflict
                return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($rank)]);
			}

            // Rollback since user creation failed
            $this->db->rollback();

            //Return conflict
            return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($user)]);
		}

        // Return bad request
        return $this->response(400, 'Bad Request', ['error' => ['Request body should be sent as JSON']]);
	}

	// Login action for logging in users
	public function loginAction()
	{
		// Get json body
		if($json = $this->request->getJsonRawBody())
		{
			// Authenticate API key
			if(!isset($json->api_key) || $this->authenticate($json->api_key))
			{
				return $this->response(400, 'Bad Request', ['error' => ['API key is incorrect or missing']]);
			}
			
			// Log in user
			if(isset($json->facebook_id))
			{
				// Find user by facebook id
				$user = User::findFirstByFacebook_id($json->facebook_id);

                if($user == true)
                {
                    // Find country, rank, and notification
                    $user->country;
                    $user->rank;
                    $user->notification;

                    // Return OK
                    return $this->response(200, 'OK', ['response' => ['user' => $user]]);
                }

				// Return unauthorized
				return $this->response(401, 'Unauthorized', ['error' => ['There is no account linked to your Facebook. We should ask if they want to link this Facebook account to their existing account by showing a username and password login so we know which Better account to connect the Facebook account']]);
			}

            // Find user by username
            $user = User::findFirstByUsername($json->username);

            if($user == true)
            {
                // Find country, rank, and notification
                $user->country;
                $user->rank;
                $user->notification;

                // Authenticate password
                if(isset($json->password))
                {
                    // Return unauthorized if no password exists
                    if($user->password == null) return $this->response(401, 'Unauthorized', ['error' => ['No password has been set for this account. Either you have the wrong username or you should try logging in with Facebook.']]);

                    // Check hash
                    if($this->security->checkHash($json->password, $user->password))
                    {
                        // Return OK
                        return $this->response(200, 'OK', ['response' => ['user' => $user]]);
                    }

                    // Return unauthorized
                    return $this->response(401, 'Unauthorized', ['error' => ['Seems you have entered your password incorrectly']]);
                }

                // Check hash
                if(strcmp($user->password, $json->hash) == 0)
                {
                    // Return OK
                    return $this->response(200, 'OK', ['response' => ['user' => $user]]);
                }

                // Return unauthorized
                return $this->response(401, 'Unauthorized', ['error' => ['We are having trouble logging you in']]);
            }

            // Return unauthorized
            return $this->response(401, 'Unauthorized', ['error' => ['No account exists with that username']]);
		}

        // Return bad request
        return $this->response(400, 'Bad Request', ['error' => ['Request body should be sent as JSON']]);
	}

    public function updateAction()
    {
        // Get json body
        if($json = $this->request->getJsonRawBody())
        {
            // Authenticate API key
            if(!isset($json->api_key) || $this->authenticate($json->api_key))
            {
                return $this->response(400, 'Bad Request', ['error' => ['API key is incorrect or missing']]);
            }

            // Find user
            $user = User::findFirst([
                "conditions" => "id = :userID:",
                "bind" => ["userID" => $json->user_id]
            ]);

            if($user == true)
            {
                // Set new user data
                $user->gender = $json->gender;
                $user->birthday = $json->birthday;

                // Update user
                if($user->update() == true)
                {
                    // Return OK
                    return $this->response(200, 'OK', ['response' => ['user' => $user]]);
                }

                // Return conflict
                return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($user)]);
            }

            // Return conflict
            return $this->response(409, 'Conflict', ['error' => 'Invalid user id']);
        }

        // Return bad request
        return $this->response(400, 'Bad Request', ['error' => ['Request body should be sent as JSON']]);
    }
}