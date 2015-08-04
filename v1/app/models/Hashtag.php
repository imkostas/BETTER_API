<?php // Hashtag model class

class Hashtag extends BaseModel
{
	public $id;
	public $name;
	public $created_at;

	public function initialize()
	{
		// Used to count the number of times a given hashtag has been favorited
		//$this->hasMany('id', 'FavoriteHashtag', 'hashtag_id');

		// Used to count the number of posts referencing a given hashtag
		//$this->hasMany('id', 'PostHashtagRelation', 'hashtag_id');

		// Used to select users who have favorited a given hashtag
		//$this->hasManyToMany('id', 'FavoriteHashtag', 'hashtag_id', 'user_id', 'User', 'id');

		// Used to select posts referencing a given hashtag
		//$this->hasManyToMany('id', 'PostHashtagRelation', 'hashtag_id', 'post_id', 'Post', 'id');
	}
}