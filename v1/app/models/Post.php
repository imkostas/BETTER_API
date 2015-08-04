<?php // Post model class
	
use \Phalcon\Mvc\Model\Validator\InclusionIn as InclusionInValidator;

class Post extends BaseModel
{
	public $id;
	public $user_id;
	public $layout;
	public $hotspot_one_x;
	public $hotspot_one_y;
	public $hotspot_two_x;
	public $hotspot_two_y;
	public $created_at;
    public $is_blocked;

	public function initialize()
	{
		// Creates relationship to a user - can be used to reference the user info of the poster
		$this->belongsTo('user_id', 'User', 'id');

		// Creates relationship to number of times a post has been reported misused
		//$this->hasMany('id', 'Misuse', 'post_id');

        // Creates relationship to all users who have voted on the post - can be used to count the number of voters
        $this->hasMany('id', 'Vote', 'post_id');

		// Creates relationship to hashtags in post - can be used to count number of hashtags in post
		//$this->hasMany('id', 'PostHashtagRelation', 'post_id');

		// Creates relationship to favorites on post - can be used to count the number of favorites
		//$this->hasMany('id', 'FavoritePost', 'post_id');

		// Creates relationship to hashtags in post - can be used to fetch hashtags in post
		$this->hasManyToMany('id', 'PostHashtagRelation', 'post_id', 'hashtag_id', 'Hashtag', 'id');

        // Connect a favorited hashtags to posts
        //$this->hasManyToMany('id', 'PostHashtagRelation', 'post_id', 'hashtag_id', 'FavoriteHashtag', 'hashtag_id');

		// These relationships exist - however, they may conflict due to pointing to the same table
		//$this->hasManyToMany('id', 'FavoritePost', 'post_id', 'user_id', 'User', 'id');
		//$this->hasManyToMany('id', 'Vote', 'post_id', 'user_id', 'User', 'id');
	}

	public function validation()
    {
        // Valid gender value
        $this->validate(new InclusionInValidator([
	        'field' => 'layout',
	        'message' => 'Layout value should be 0, 1, or 2',
	        'domain' => [0, 1, 2],
	        'allowEmpty' => false
        ]));

        // Valid is_blocked value
        $this->validate(new InclusionInValidator([
            'field' => 'is_blocked',
            'message' => 'Blocked value should be 0 or 1',
            'domain' => [0, 1],
            'allowEmpty' => false
        ]));

        // Validation failed
        if($this->validationHasFailed() == true) return false;
        
        // Validation succeeded
        return true;
    }
}