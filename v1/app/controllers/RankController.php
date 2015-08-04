<?php

class RankController extends BaseController
{
	public function indexAction($limit)
    {
        $allTimeLeaderboard = [];
        $weeklyLeaderboard = [];
        $dailyLeaderboard = [];

        $allTimeRanks = Rank::find([
            "order" => "total_points DESC",
            "limit" => $limit,
        ]);

        foreach($allTimeRanks as $rank)
        {
            $obj = new stdClass();
            $obj->user_id = $rank->user_id;
            $obj->username = $rank->getUser()->username;
            $obj->rank = $rank->rank;
            $obj->total_points = $rank->total_points;

            array_push($allTimeLeaderboard, $obj);
        }

        $weeklyRanks = Rank::find([
            "order" => "weekly_points DESC",
            "limit" => $limit,
        ]);

        foreach($weeklyRanks as $rank)
        {
            $obj = new stdClass();
            $obj->user_id = $rank->user_id;
            $obj->username = $rank->getUser()->username;
            $obj->rank = $rank->rank;
            $obj->total_points = $rank->total_points;

            array_push($weeklyLeaderboard, $obj);
        }

        $dailyRanks = Rank::find([
            "order" => "daily_points DESC",
            "limit" => $limit,
        ]);

        foreach($dailyRanks as $rank)
        {
            $obj = new stdClass();
            $obj->user_id = $rank->user_id;
            $obj->username = $rank->getUser()->username;
            $obj->rank = $rank->rank;
            $obj->total_points = $rank->total_points;

            array_push($dailyLeaderboard, $obj);
        }

        // Return OK
        return $this->response(200, 'OK', ['response' => ['all_time_leaderboard' => $allTimeLeaderboard, 'weekly_leaderboard' => $weeklyLeaderboard, 'daily_leaderboard' => $dailyLeaderboard]]);
    }
}