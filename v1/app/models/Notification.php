<?php // Notification model class

use \Phalcon\Mvc\Model\Validator\InclusionIn as InclusionInValidator;

class Notification extends BaseModel
{
    public $user_id;
    public $voted_post;
    public $favorited_post;
    public $new_follower;

    public function initialize()
    {
        // Creates relationship to a user
        //$this->hasOne('user_id', 'User', 'id');
    }

    public function validation()
    {
        // Valid voted_post value
        $this->validate(new InclusionInValidator([
            'field' => 'voted_post',
            'message' => 'Value should be 0 or 1',
            'domain' => [0, 1],
            'allowEmpty' => false
        ]));

        // Valid favorited_post value
        $this->validate(new InclusionInValidator([
            'field' => 'favorited_post',
            'message' => 'Value should be 0 or 1',
            'domain' => [0, 1],
            'allowEmpty' => false
        ]));

        // Valid new_follower value
        $this->validate(new InclusionInValidator([
            'field' => 'new_follower',
            'message' => 'Value should be 0 or 1',
            'domain' => [0, 1],
            'allowEmpty' => false
        ]));

        // Validation failed
        if($this->validationHasFailed() == true) return false;

        // Validation succeeded
        return true;
    }
}