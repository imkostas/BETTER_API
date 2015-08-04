<?php // Vote model class
	
use \Phalcon\Mvc\Model\Validator\Uniqueness,
    \Phalcon\Mvc\Model\Validator\InclusionIn as InclusionInValidator;

class Vote extends BaseModel
{
    public $id;
	public $post_id;
	public $user_id;
	public $vote;
	public $created_at;

	public function initialize()
	{
		// Creates relationship to a post
		//$this->belongsTo('post_id', 'Post', 'id');

		// Creates relationship to a user
		$this->belongsTo('user_id', 'User', 'id');
	}

	public function validation()
    {
        // Unique composite of user_id and post_id
        $this->validate(new Uniqueness(["field" => ['user_id', 'post_id']]));

        // Valid gender value
        $this->validate(new InclusionInValidator([
	        'field' => 'vote',
	        'message' => 'Vote value is not valid',
	        'domain' => [1, 2],
	        'allowEmpty' => false
        ]));

        // Validation failed
        if($this->validationHasFailed() == true) return false;
        
        // Validation succeeded
        return true;
    }
}