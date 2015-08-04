<?php // Follow model class

class Follow extends BaseModel
{
    public $id;
	public $user_id;
	public $follower_id;
	public $created_at;
	public $is_deleted;

	public function initialize(){}
}