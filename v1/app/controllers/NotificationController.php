<?php

class NotificationController extends BaseController
{
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

            // Validate type
            if(!is_numeric($json->type))
            {
                // Return bad request
                return $this->response(400, 'Bad Request', ['error' => ['Invalid notification type']]);
            }

            // Find notification data for given user
            $notification = Notification::findFirst([
                "conditions" => "user_id = :userID:",
                "bind" => ["userID" => $json->user_id]
            ]);

            // Toggle notification according to type
            switch(intval($json->type))
            {
                case 0:
                    $notification->voted_post = ($notification->voted_post == 1) ? 0 : 1;
                    break;
                case 1:
                    $notification->favorited_post = ($notification->favorited_post == 1) ? 0 : 1;
                    break;
                case 2:
                    $notification->new_follower = ($notification->new_follower == 1) ? 0 : 1;
                    break;
                default:
                    // Return bad request
                    return $this->response(400, 'Bad Request', ['error' => ['Invalid notification type']]);
            }

            // Update notification
            if($notification->update() == true)
            {
                // Return OK
                return $this->response(200, 'OK', ['response' => ['notification' => $notification]]);
            }

            // Return conflict
            return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($notification)]);
        }

        // Return bad request
        return $this->response(400, 'Bad Request', ['error' => ['Request body should be sent as JSON']]);
    }
}