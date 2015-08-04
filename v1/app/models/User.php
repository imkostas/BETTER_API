<?php // User model class
	
use \Phalcon\Mvc\Model\Validator\Uniqueness,
	\Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator,
	\Phalcon\Mvc\Model\Validator\Email as EmailValidator,
	\Phalcon\Mvc\Model\Validator\InclusionIn as InclusionInValidator;

class User extends BaseModel
{
	public $id;
	public $username;
	public $email;
    public $gender;
    public $birthday;
    public $country_id;
    public $password;
    public $password_temp;
    public $facebook_id;
    public $device;
    public $device_type;
	public $created_at;
	public $updated_at;
	public $last_login_at;

	public function initialize()
	{
		// Creates relationship to a country
		$this->belongsTo('country_id', 'Country', 'id');

		// Creates relationship to a user's rank
		$this->hasOne('id', 'Rank', 'user_id');

        // Creates relationship to a user's notification settings
        $this->hasOne('id', 'Notification', 'user_id');

		// Creates relationship to all of a user's points
		$this->hasMany('id', 'Point', 'user_id');

        // Creates relationship to all of a user's posts
        $this->hasMany('id', 'Post', 'user_id');

		// Creates relationship to all of a user's votes
		$this->hasMany('id', 'Vote', 'user_id');

		// Creates relationship to all of a user's favorite posts
		$this->hasMany('id', 'FavoritePost', 'user_id');

		// Creates relationship to all of a user's favorite hashtags
		$this->hasMany('id', 'FavoriteHashtag', 'user_id');

		// Creates a relationship to all of a user's favorite hashtags
		$this->hasManyToMany('id', 'FavoriteHashtag', 'user_id', 'hashtag_id', 'Hashtag', 'id');

		// These relationships exist - however, they may conflict due to pointing to the same table
		// $this->hasManyToMany('id', 'FavoritePost', 'user_id', 'post_id', 'Post', 'id');
		// $this->hasManyToMany('id', 'Vote', 'user_id', 'post_id', 'Post', 'id');
	}
	
	public function validation()
    {
	    // Username proper length
	    $this->validate(new StringLengthValidator([
			'field' => 'username',
			'max' => 20,
			'min' => 6,
			'messageMaximum' => 'Username should be between 6 and 20 characters',
			'messageMinimum' => 'Username should be between 6 and 20 characters'
		]));
	    
	    // Email proper length
	    $this->validate(new StringLengthValidator([
		    'field' => 'email',
			'max' => 64,
			'min' => 1,
			'messageMaximum' => 'Emails must be under 64 characters',
			'messageMinimum' => 'Are you sure your email has been entered correctly?'
		]));
		
		// Password proper length
		$this->validate(new StringLengthValidator([
			'field' => 'password',
			'max' => 70,
			'messageMaximum' => 'Please keep your password under 70 characters',
		]));
	    
	    // Valid email address
        $this->validate(new EmailValidator([
	        'field' => 'email',
        	'message' => 'Email is not valid'
        ]));
        
        // Valid gender value
        $this->validate(new InclusionInValidator([
	        'field' => 'gender',
	        'message' => 'Gender value is not valid',
	        'domain' => [1, 2],
	        'allowEmpty' => false
        ]));
	    
	    // Unique username
		$this->validate(new Uniqueness([
            "field"   => "username",
            "message" => "Username must be unique"
        ]));
        
        // Unique email
		$this->validate(new Uniqueness([
            "field"   => "email",
            "message" => "Email must be unique"
        ]));
        
        // Unique facebook_id
		$this->validate(new Uniqueness([
            "field"   => "facebook_id",
            "message" => "Facebook id must be unique"
        ]));

        // Validation failed
        if($this->validationHasFailed() == true) return false;
        
        // Validation succeeded
        return true;
    }
}