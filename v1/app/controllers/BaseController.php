<?php

use \Phalcon\Mvc\Controller,
	\Phalcon\Http\Response;

class BaseController extends Controller
{
	public function authenticate($apiKey)
	{
		return strcmp($apiKey, 'better');
	}

	public function fetchErrors($obj)
	{
		$errors = [];
		foreach ($obj->getMessages() as $message) array_push($errors, $message->getMessage());
		return $errors;
	}
	
	public function response($statusCode, $statusMessage, $content)
	{
		$response = new Response();
		$response->setContentType('application/json', 'UTF-8');
		$response->setStatusCode($statusCode, $statusMessage);
        $response->setJsonContent($content);
        return $response;
	}
}

//$totalElapsedTime = 0.0;
//$profiles = $this->di->get('profiler')->getProfiles();
//foreach ($profiles as $profile) $totalElapsedTime += $profile->getTotalElapsedSeconds();
//echo "Total Elapsed Time: ", $totalElapsedTime, "\n";

// $profiles = $this->di->get('profiler')->getProfiles();
// foreach ($profiles as $profile) {
//    echo "SQL Statement: ", $profile->getSQLStatement(), "\n";
//    echo "Start Time: ", $profile->getInitialTime(), "\n";
//    echo "Final Time: ", $profile->getFinalTime(), "\n";
//    echo "Total Elapsed Time: ", $profile->getTotalElapsedSeconds(), "\n";
// }

/* PHQL Example

$phql = "INSERT INTO user (username, email, password, gender, birthday, country, created_at)
         VALUES (:username:, :email:, :password:, :gender:, :birthday:, :country:, :created_at:)";
$status = $this->modelsManager->executeQuery($phql, array(
    'username' => $user->username,
    'email' => $user->email,
    'password' => $this->security->hash($user->password),
    'gender' => $user->gender,
    'birthday' => $user->birthday,
    'country' => $user->country,
    'created_at' => date('Y-m-d H:i:s')
));

// Response
$response = new Response();
if($status->success())
{
    $response->setStatusCode(200, "Created");
    $response->setJsonContent(array('status' => 'OK', 'message' => 'Account created successfully'));
}
else
{
    $response->setStatusCode(409, "Conflict");
    $errors = array();
    foreach ($status->getMessages() as $message) $errors[] = $message->getMessage();
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
}
return $response; */