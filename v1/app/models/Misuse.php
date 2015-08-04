<?php // Misuse model class

class Misuse extends BaseModel
{
    public $post_id;
    public $user_id;
    public $created_at;

    public function initialize()
    {
        // Creates relationship to a post
        //$this->hasOne('post_id', 'Post', 'id');

        // Creates relationship to a user
        //$this->hasOne('user_id', 'User', 'id');
    }
}