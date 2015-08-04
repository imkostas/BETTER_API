<?php // Country model class
	
use \Phalcon\Mvc\Model\Validator\Uniqueness;

class Country extends BaseModel
{
	public $id;
	public $name;

	public function initialize()
	{
		// Used to select users from a given country
		//$this->hasMany('id', 'User', 'country_id');
	}

	public function validation()
    {
        // Unique country name
		$this->validate(new Uniqueness([
            "field"   => "name",
            "message" => "Country name must be unique"
        ]));

        // Validation failed
        if($this->validationHasFailed() == true) return false;
        
        // Validation succeeded
        return true;
    }
}