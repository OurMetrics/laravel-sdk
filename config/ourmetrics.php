<?php return [
	/*
	|--------------------------------------------------------------------------
	| Project Key
	|--------------------------------------------------------------------------
	|
	| If left empty, OurMetrics will be disabled.
	*/
	'project_key'          => env( 'OURMETRICS_KEY' ),

	/*
	|--------------------------------------------------------------------------
	| Track controller methods
	|--------------------------------------------------------------------------
	|
	| When enabled, every controller method called will be monitored and counted.
	|
	| Example: UserController@store
	*/
	'track_controllers'    => false,

	/*
	|--------------------------------------------------------------------------
	| HTTP Connection
	|--------------------------------------------------------------------------
	|
	| - Endpoint to send metrics to.
	| - Connection timeout
	*/
	'endpoint'             => env( 'OURMETRICS_ENDPOINT', 'https://api.ourmetrics.app/metrics' ),
	'timeout'              => env( 'OURMETRICS_TIMEOUT', 2.0 ),

	/*
	|--------------------------------------------------------------------------
	| HTTP Headers
	|--------------------------------------------------------------------------
	|
	| Headers being sent.
	| Currently it is not possible to add/remove headers.
	*/
	'headers'              => [
		'user_agent' => 'OurMetrics SDK v0.1.0',
		'connection' => 'close',
	],

	/*
	|--------------------------------------------------------------------------
	| Dispatch queued metrics automatically?
	|--------------------------------------------------------------------------
	*/
	'dispatch_on_destruct' => true,
];