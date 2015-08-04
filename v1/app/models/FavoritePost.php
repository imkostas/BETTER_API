<?php // FavoritePost model class

class FavoritePost extends BaseModel
{
    public $id;
	public $post_id;
	public $user_id;
	public $created_at;
	public $is_deleted;

	public function initialize()
	{
		// Used to select a post using a given post id
		$this->belongsTo('post_id', 'Post', 'id');

		// Used to select a user using a given user id
		//$this->belongsTo('user_id', 'User', 'id');
	}
}