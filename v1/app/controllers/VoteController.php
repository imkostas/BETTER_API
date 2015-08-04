<?php

class VoteController extends BaseController
{
	// Create action for voting
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

			// Find post - this can be replaced by a front-end check
			$post = Post::findFirst($json->post_id);

			// Check voter not equal to poster
			if($post->user_id == $json->user_id)
			{
				// Return conflict
				return $this->response(409, 'Conflict', ['error' => ['User cannot vote on their own post']]);
			}
			
			// Initialze vote
			$vote = new Vote();
			$vote->post_id = $json->post_id;
			$vote->user_id = $json->user_id;
			$vote->vote = $json->vote;
			$vote->created_at = date('Y-m-d H:i:s');

			// Create vote
			if($vote->create() == true)
			{
				// Return created
				return $this->response(201, 'Created', ['response' => ['vote' => $vote]]);
			}
			
			// Return conflict
			return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($vote)]);
		}
		
		// Return bad request
		return $this->response(400, 'Bad Request', ['error' => ['Request body should be sent as JSON']]);
	}

    // Count returns the amount of votes made by a user
    public function count($userid)
    {
        return Vote::find(["conditions" => "user_id = :userid:", "bind" => ["userid" => $userid]])->count();
    }

    // Return id of last vote
    public function getLast()
    {
        $vote = Vote::findFirst(["order" => "id DESC"]);

        if($vote == true)
        {
            return $vote->id + 1;
        }
        else
        {
            return 1;
        }
    }
}