<?php
	ini_set('display_errors', '1');
	ini_set('displaystartuperrors', 1);
	error_reporting(E_ALL);
	
	use \Phalcon\Mvc\Micro,
        \Phalcon\DI\FactoryDefault,
        \Phalcon\Config\Adapter\Ini,
        \Phalcon\Loader,
        \Phalcon\Events\Manager,
        \Phalcon\Db\Adapter\Pdo\Mysql,
        \Phalcon\Db\Profiler,
        \Phalcon\Debug;

    // Attach debugger
	(new Debug())->listen();

    // Deploy app
    $app = new Micro();

	// Initialize DI
	$di = new FactoryDefault();

    // Set config
    $di->set('config', function(){ return new Ini("../app/config/config.ini"); });

    // Set DI
    $app->setDI($di);

    // Composer autoload.php
//    include $app->config->composer->autoload;

    // Loader
    (new Loader())->registerDirs([
        $app->config->phalcon->controllers,
        $app->config->phalcon->models,
        $app->config->phalcon->validations
    ])->register();

	// Database
	$di->set('db', function() use ($app)
	{
	    $profiler = $app->di->getProfiler();

	    $eventsManager = new Manager();
	    $eventsManager->attach('db', function($event, $connection) use ($profiler)
	    {
	        if($event->getType() == 'beforeQuery')
	        {
	            $profiler->startProfile($connection->getSQLStatement());
	        }
	        if($event->getType() == 'afterQuery')
	        {
	            $profiler->stopProfile();
	        }
	    });

	    $connection = new Mysql([
	        "host" => $app->config->database->host,
			"username" => $app->config->database->username,
			"password" => $app->config->database->password,
			"dbname" => $app->config->database->dbname
	    ]);
	    $connection->setEventsManager($eventsManager);

	    return $connection;
	});

    // MySQL Profiler
    $di->set('profiler', function(){ return new Profiler(); }, true);

	$app->before(function() use ($app)
	{
		if($app->config->api->maintenance == true)
		{
			$app->response->setStatusCode(401, "Unauthorized")->sendHeaders();
            $app->response->setContentType('application/json', 'UTF-8');
            $app->response->setContent(json_encode(['error' => ['API is currently down for maintenance']]));
            $app->response->send();
			return false;
		}
		return true;
	});
	
	// Routes
    $app->post('/country', [new CountryController(), 'createAction']);

    $app->get('/favoritehashtag/{userID:[0-9]+}/{last:[0-9]+}/{limit:[0-9]+}', [new FavoriteHashtagController(), 'indexAction']);
    $app->post('/favoritehashtag', [new FavoriteHashtagController(), 'createAction']);
    $app->delete('/favoritehashtag/{hashtagID:[0-9]+}/{userID:[0-9]+}', [new FavoriteHashtagController(), 'deleteAction']);

    $app->get('/favoritepost/{userID:[0-9]+}/{last:[0-9]+}/{limit:[0-9]+}', [new FavoritePostController(), 'indexAction']);
    $app->post('/favoritepost', [new FavoritePostController(), 'createAction']);
    $app->delete('/favoritepost/{postID:[0-9]+}/{userID:[0-9]+}', [new FavoritePostController(), 'deleteAction']);

    $app->get('/follow/0/{userID:[0-9]+}/{last:[0-9]+}/{limit:[0-9]+}', [new FollowController(), 'followerAction']);
    $app->get('/follow/1/{userID:[0-9]+}/{last:[0-9]+}/{limit:[0-9]+}', [new FollowController(), 'followingAction']);
    $app->post('/follow', [new FollowController(), 'createAction']);
    $app->delete('/follow/{userID:[0-9]+}/{followerID:[0-9]+}', [new FollowController(), 'deleteAction']);

    $app->get('/hashtag/{name:[a-zA-Z0-9]+}/{limit:[0-9]+}', [new HashtagController(), 'indexAction']);

    $app->post('/misuse', [new MisuseController(), 'createAction']);

    $app->post('/notification', [new NotificationController(), 'updateAction']);

    $app->get('/post/{userID:[0-9]+}/{last:[0-9]+}/{limit:[0-9]+}', [new PostController(), 'indexAction']);
    $app->get('/post/posted/{userID:[0-9]+}/{type:[0-1]}/{last:[0-9]+}/{limit:[0-9]+}', [new PostController(), 'postedAction']);
    $app->get('/post/voted/{userID:[0-9]+}/{last:[0-9]+}/{limit:[0-9]+}', [new PostController(), 'votedAction']);
    $app->get('/post/hashtag/{hashtagID:[0-9]+}/{userID:[0-9]+}/{last:[0-9]+}/{limit:[0-9]+}', [new PostController(), 'hashtagAction']);
    $app->get('/post/following/{userID:[0-9]+}/{last:[0-9]+}/{limit:[0-9]+}', [new PostController(), 'followingAction']);
    $app->get('/post/favoritehashtag/{userID:[0-9]+}/{last:[0-9]+}/{limit:[0-9]+}', [new PostController(), 'favoriteHashtagAction']);
    $app->get('/post/trending/{userID:[0-9]+}/{major:[0-9]+}/{minor:[0-9]+}/{limit:[0-9]+}', [new PostController(), 'trendingAction']);
    $app->get('/post/detail/{postID:[0-9]+}/{userID:[0-9]+}', [new PostController(), 'detailAction']);
    $app->get('/post/voters/{postID:[0-9]+}/{userID:[0-9]+}/{last:[0-9]+}/{limit:[0-9]+}', [new PostController(), 'votersAction']);
    $app->post('/post', [new PostController(), 'createAction']);

    $app->get('/rank/{limit:[0-9]+}', [new RankController(), 'indexAction']);

    $app->get('/user/{username:[a-zA-Z0-9]+}/{limit:[0-9]+}', [new UserController(), 'indexAction']);
    $app->get('/user/count/{userID:[0-9]+}', [new UserController(), 'countAction']);
    $app->post('/user', [new UserController(), 'createAction']);
    $app->post('/user/login', [new UserController(), 'loginAction']);
    $app->post('/user/update', [new UserController(), 'updateAction']);

    $app->post('/vote', [new VoteController(), 'createAction']);

    // Route not found
	$app->notFound(function () use ($app)
	{
        $app->response->setStatusCode(404, "Not Found")->sendHeaders();
        $app->response->setContentType('application/json', 'UTF-8');
        $app->response->setContent(json_encode(['error' => ['Request does not exist']]));
        $app->response->send();
	});
	
	$app->handle();