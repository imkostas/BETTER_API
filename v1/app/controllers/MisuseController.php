<?php

class MisuseController extends BaseController
{
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

            // Initialize misuse
            $misuse = new Misuse();
            $misuse->post_id = $json->post_id;
            $misuse->user_id = $json->user_id;
            $misuse->created_at = date('Y-m-d H:i:s');

            // Create misuse
            if($misuse->create() == true)
            {
                if(Misuse::find(["conditions" => "post_id = :postID:", "bind" => ["postID" => $json->post_id]])->count() == 3)
                {
                    // Send email here to signal when post has been blocked

                    // Find misused post
                    $post = Post::findFirst([
                        "conditions" => "id = :postID:",
                        "bind" => ["postID" => $json->post_id]
                    ]);

                    // Block post
                    $post->is_blocked = 1;

                    // Update post
                    if($post->update() == true)
                    {
                        // Commit
                        $this->db->commit();

                        // Return OK
                        return $this->response(200, 'OK', ['response' => ['misuse' => $misuse]]);
                    }

                    // Rollback since post update failed
                    $this->db->rollback();

                    // Return conflict
                    return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($post)]);
                }

                // Commit
                $this->db->commit();

                // Return OK
                return $this->response(200, 'OK', ['response' => ['misuse' => $misuse]]);
            }

            // Rollback since misuse creation failed
            $this->db->rollback();

            // Return conflict
            return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($misuse)]);
        }

        // Return bad request
        return $this->response(400, 'Bad Request', ['error' => ['Request body should be sent as JSON']]);
    }
}