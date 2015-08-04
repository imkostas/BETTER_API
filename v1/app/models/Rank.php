<?php // Rank model class

class Rank extends BaseModel
{
	public $user_id;
	public $rank;
	public $total_points;
	public $weekly_points;
	public $daily_points;
	public $badge_tastemaker;
	public $badge_adventurer;
	public $badge_admirer;
	public $badge_role_model;
	public $badge_celebrity;
	public $badge_idol;

	public function initialize()
	{
		// Creates relationship to a user
		$this->hasOne('user_id', 'User', 'id');
	}
}