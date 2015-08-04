<?php // FavoriteHashtag model class

class FavoriteHashtag extends BaseModel
{
    public $id;
	public $hashtag_id;
	public $user_id;
	public $created_at;
	public $is_deleted;

	public function initialize()
	{
		// Used to select a hashtag using a given favorited hashtag id
		$this->belongsTo('hashtag_id', 'Hashtag', 'id');

		// Used to select a user using a given user id
		//$this->belongsTo('user_id', 'User', 'id');
	}
}