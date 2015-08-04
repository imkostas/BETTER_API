<?php // PostHashtagRelation model class

class PostHashtagRelation extends BaseModel
{
	public $post_id;
	public $hashtag_id;
	public $position;
	public $created_at;

	public function initialize()
	{
		// Creates relationship to a post
		//$this->belongsTo('post_id', 'Post', 'id');

		// Creates relationship to a hashtag
		//$this->belongsTo('hashtag_id', 'Hashtag', 'id');
	}
}