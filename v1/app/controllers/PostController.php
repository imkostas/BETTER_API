<?php

class PostController extends BaseController
{
    // Index action to return all posts without filter
    public function indexAction($userID, $last, $limit)
    {
        // Initialize feed array
        $feed = [];

        // Initialize last
        if($last == 0)
        {
            $last = $this->getLast();
        }

        // Find posts
        $posts = Post::find([
            "conditions" => "id < :last: AND is_blocked = 0",
            "order" => "id DESC",
            "limit" => $limit,
            "bind" => ["last" => $last]
        ]);

        // For each post find username, vote status, vote count, and hashtag data
        foreach($posts as $post)
        {
            // Dynamically add new properties to post
            $post->username = $post->getUser()->username;
            $post->votes = $post->getVote()->count();
            $post->voted_zero = $post->getVote(["conditions" => "vote = 0"])->count();

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

            // Push post into feed array
            array_push($feed, $post);
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['feed' => $feed]]);
    }

    // Posted action to return all posts posted by a user
    public function postedAction($userID, $type, $last, $limit)
    {
        // Initialize feed array
        $feed = [];

        // Initialize last
        if($last == 0)
        {
            $last = $this->getLast();
        }

        // Initialize conditions
        $conditions = ($type == 0)?"id < :last: AND user_id = :userID: AND is_blocked = 0":"id < :last: AND user_id = :userID:";

        // Find posts
        $posts = Post::find([
            "conditions" => $conditions,
            "order" => "id DESC",
            "limit" => $limit,
            "bind" => ["last" => $last, "userID" => $userID]
        ]);

        // For each post find username, vote status, vote count, and hashtag data
        foreach($posts as $post)
        {
            // Dynamically add new properties (username and vote counts) to post
            $post->username = $post->getUser()->username;
            $post->votes = $post->getVote()->count();
            $post->voted_zero = $post->getVote("vote = 0")->count();
            $post->vote = false;

            // Find hashtags
            $hashtags = [];
            foreach($post->getHashtag(["order" => "position ASC"]) as $hashtag)
            {
                // Push hashtag names into array
                array_push($hashtags, $hashtag->name);
            }

            // Flatten hashtag array into string
            $post->hashtags = implode(' ', $hashtags);

            // Push post into feed array
            array_push($feed, $post);
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['feed' => $feed]]);
    }

    // Voted action to return all posts voted on by a user
    public function votedAction($userID, $last, $limit)
    {
        // Initialize feed array
        $feed = [];

        // Initialize last
        if($last == 0)
        {
            $voteController = new VoteController();
            $last = $voteController->getLast();
        }

        // Find votes
        $votes = Vote::find([
            "conditions" => "user_id = :userID: AND id < :last:",
            "order" => "id DESC",
            "limit" => $limit,
            "bind" => ["userID" => $userID, "last" => $last]
        ]);

        foreach($votes as $vote)
        {
            // Find post
            $post = Post::findFirst([
                "conditions" => "id = :postID: AND is_blocked = 0",
                "bind" => ["postID" => $vote->post_id]
            ]);

            if($post == true)
            {
                // Dynamically add new properties (username and vote counts) to post
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

    // Hashtag action to find posts related to specific hashtag
    public function hashtagAction($hashtagID, $userID, $last, $limit)
    {
        // Initialize feed array
        $feed = [];

        // Initialize last
        if($last == 0)
        {
            $last = $this->getLast();
        }

        // Find favorite posts
        $postHashtagRelationships = PostHashtagRelation::find([
            "conditions" => "hashtag_id = :hashtagID: AND post_id < :last:",
            "order" => "post_id DESC",
            "limit" => $limit,
            "bind" => ["hashtagID" => $hashtagID, "last" => $last]
        ]);

        foreach($postHashtagRelationships as $postHashtagRelation)
        {
            // Find post
            $post = Post::findFirst([
                "conditions" => "id = :postID: AND is_blocked = 0",
                "bind" => ["postID" => $postHashtagRelation->post_id]
            ]);

            if($post == true)
            {
                // Dynamically add new properties (username and vote counts) to post
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

    // Following action to find posts of followed users
    public function followingAction($userID, $last, $limit)
    {
        // Initialize feed array
        $feed = [];

        // Initialize last
        if($last == 0)
        {
            $last = $this->getLast();
        }

        $phql = "SELECT post.* FROM post JOIN Follow ON post.user_id = Follow.user_id WHERE Follow.follower_id = :userID: AND post.id < :last: AND post.is_blocked = 0 AND Follow.is_deleted = 0 ORDER BY post.id DESC LIMIT {$limit}";
        $posts = $this->modelsManager->executeQuery($phql, ['userID' => $userID, 'last' => $last]);

        foreach($posts as $post)
        {
            // Dynamically add new properties (username and vote counts) to post
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

            // Push post into feed array
            array_push($feed, $post);
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['feed' => $feed]]);
    }

    // Favorite hashtag action to find posts connected to favorited hashtags
    public function favoriteHashtagAction($userID, $last, $limit)
    {
        // Initialize feed array
        $feed = [];

        // Initialize last
        if($last == 0)
        {
            $last = $this->getLast();
        }

        $phql = "SELECT post.* FROM post JOIN PostHashtagRelation ON post.id = PostHashtagRelation.post_id JOIN FavoriteHashtag ON PostHashtagRelation.hashtag_id = FavoriteHashtag.hashtag_id WHERE FavoriteHashtag.user_id = :userID: AND FavoriteHashtag.is_deleted = 0 AND FavoriteHashtag.user_id <> post.user_id AND post.id < :last: AND post.is_blocked = 0 GROUP BY PostHashtagRelation.post_id ORDER BY post.id DESC LIMIT {$limit}";
        $posts = $this->modelsManager->executeQuery($phql, ['userID' => $userID, 'last' => $last]);

        foreach($posts as $post)
        {
            // Dynamically add new properties (username and vote counts) to post
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

            // Push post into feed array
            array_push($feed, $post);
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['feed' => $feed]]);
    }

    public function trendingAction($userID, $major, $minor, $limit)
    {
        // Calculate time four weeks from current time
        $date = date('Y-m-d H:i:s', time() - 2419200);

        // Initialize feed array
        $feed = [];

        if($major == 0 && $minor == 0)
        {
            $phql = "SELECT COUNT(*) AS votes, post_id FROM Vote WHERE created_at >= :date: GROUP BY post_id ORDER BY votes DESC, post_id DESC LIMIT {$limit}";
            $trending = $this->modelsManager->executeQuery($phql, ["date" => $date]);
        }
        else
        {
            $phql = "SELECT COUNT(*) AS votes, post_id FROM Vote WHERE created_at >= :date: GROUP BY post_id HAVING votes <= :votes: AND (post_id < :postID: OR votes < :votes:) ORDER BY votes DESC, post_id DESC LIMIT {$limit}";
            $trending = $this->modelsManager->executeQuery($phql, ["date" => $date, "votes" => $major, "postID" => $minor]);
        }

        foreach($trending as $trend)
        {
            // Find post
            $post = Post::findFirst(["conditions" => "id = :postID: AND is_blocked = 0", "bind" => ["postID" => $trend->post_id]]);
            if($post == true)
            {
                // Dynamically add new properties (username and vote counts) to post
                $post->major = $trend->votes;
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

    // Details action to return the details for a given post
    public function detailAction($postID, $userID)
    {
        // Initialize variables
        $favoritedPost = 0;
        $following = 0;
        $favoriteHashtags = [];

        // Find post
        $post = Post::findFirst(["conditions" => "id = :postID:", "bind" => ["postID" => $postID]]);

        // If post isn't owned by user means we need favorite and follow data
        if($post->user_id != $userID)
        {
            // Find if user has favorited post
            $favoritePost = FavoritePost::findFirst([
                "conditions" => "post_id = :postID: AND user_id = :userID: AND is_deleted = 0",
                "bind" => ["postID" => $post->id, "userID" => $userID]
            ]);

            // Find if user follows the poster
            $follow = Follow::findFirst([
                "conditions" => "user_id = :userID: AND follower_id = :followerID: AND is_deleted = 0",
                "bind" => ["userID" => $post->user_id, "followerID" => $userID]
            ]);

            if($favoritePost == true) $favoritedPost = 1;
            if($follow == true) $following = 1;
        }

        // Find hashtags and check if user has favorited
        foreach($post->getHashtag(["order" => "position ASC"]) as $hashtag)
        {
            // Find favorite hashtag
            $favoriteHashtag = FavoriteHashtag::findFirst([
                "conditions" => "hashtag_id = :hashtagID: AND user_id = :userID: AND is_deleted = 0",
                "bind" => ["hashtagID" => $hashtag->id, "userID" => $userID]
            ]);

            // Create base object and dynamically store needed data (hashtag_id, favorited)
            $obj = new stdClass();
            $obj->hashtag_id = $hashtag->id;
            $obj->favorited = ($favoriteHashtag == true)?1:0;

            // Push favorite data into array
            array_push($favoriteHashtags, $obj);
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['favorited_post' => $favoritedPost, 'following' => $following, 'favorite_hashtags' => $favoriteHashtags]]);
    }

    // Voters action to return the voters of a post
    public function votersAction($postID, $userID, $last, $limit)
    {
        // Initialize voters array
        $voters = [];

        // Initialize last
        if($last == 0)
        {
            $voteController = new VoteController();
            $last = $voteController->getLast();
        }

        // Find all who voted on the post
        $vote = Vote::find([
            "conditions" => "post_id = :postID: AND id < :last:",
            "order" => "id DESC",
            "limit" => $limit,
            "bind" => ["postID" => $postID, "last" => $last]
        ]);

        // For each voter gather account and follow data
        foreach($vote as $voter)
        {
            // Find user
            $user = $voter->getUser();

            // Find follow
            $follow = Follow::findFirst([
                "conditions" => "user_id = :userID: AND follower_id = :followerID: AND is_deleted = 0",
                "bind" => ["userID" => $user->id, "followerID" => $userID]
            ]);

            // Dynamically add new properties (username and follow data) to voter
            $voter->username = $user->username;
            $voter->following = ($follow == true)?1:0;

            // Push voter into votes array
            array_push($voters, $voter);
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['voters' => $voters]]);
    }

	// Create action to create post
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
			
			// Initialize post
			$post = new Post();
			$post->user_id = intval($json->user_id);
			$post->layout = intval($json->layout);
			$post->hotspot_one_x = intval($json->hotspot_one_x);
			$post->hotspot_one_y = intval($json->hotspot_one_y);
			$post->hotspot_two_x = intval($json->hotspot_two_x);
			$post->hotspot_two_y = intval($json->hotspot_two_y);
			$post->created_at = date('Y-m-d H:i:s');
            $post->is_blocked = 0;

			// Create post
			if($post->create() == true)
			{
				// Move hashtags into array
				$hashtags = array_unique(explode(' ', $json->hashtags));

				// Loop through hashtags to create relation to post
				for($i = 0; $i < count($hashtags); $i++)
				{
					// Find hashtag
					$existingHashtag = Hashtag::findFirstByName($hashtags[$i]);

					// If hashtag doens't exist, create hashtag
					if($existingHashtag == false)
					{
						// Initialize hashtag
						$existingHashtag = new Hashtag();
						$existingHashtag->name = $hashtags[$i];
						$existingHashtag->created_at = date('Y-m-d H:i:s');

						// Create hashtag
						if($existingHashtag->create() == false)
						{
							// Rollback since hashtag creation failed
							$this->db->rollback();
							return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($existingHashtag)]);
						}
					}

					// Initialize post hashtag relation
					$postHashtagRelation = new PostHashtagRelation();
					$postHashtagRelation->post_id = $post->id;
					$postHashtagRelation->hashtag_id = $existingHashtag->id;
					$postHashtagRelation->position = $i;
                    $postHashtagRelation->created_at = date('Y-m-d H:i:s');

					// Create post hashtag relation
					if($postHashtagRelation->create() == false)
					{
						// Rollback since post hashtag relation creation failed
						$this->db->rollback();
						return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($postHashtagRelation)]);
					}
				}

				// Commit
				$this->db->commit();

				// Return created
				return $this->response(201, 'Created', ['response' => ['post' => $post]]);
			}

            // Rollback since post creation failed
            $this->db->rollback();

            // Return conflict
            return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($post)]);
		}

        // Return bad request
        return $this->response(400, 'Bad Request', ['error' => ['Request body should be sent as JSON']]);
	}

    // Count returns the amount of posts posted by a user
    public function count($userID)
    {
        return Post::find(["conditions" => "user_id = :userID:", "bind" => ["userID" => $userID]])->count();
    }

    // Return id of last post
    public function getLast()
    {
        $post = Post::findFirst(["order" => "id DESC"]);

        if($post == true)
        {
            return $post->id + 1;
        }
        else
        {
            return 1;
        }
    }
}