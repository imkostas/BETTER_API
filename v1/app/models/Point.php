<?php // Point model class

class Point extends BaseModel
{
    public $id;
    public $user_id;
    public $amount;
    public $type;
    public $created_at;

    public function initialize()
    {
        // Creates relationship to a user
        //$this->belongsTo('user_id', 'User', 'id');
    }
}