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
		'user_agent' => 'OurMetrics SDK v1.0.0',
		'connection' => 'close',
	],

	/*
	|--------------------------------------------------------------------------
	| Dispatch queued metrics automatically?
	|--------------------------------------------------------------------------
	*/
	'dispatch_on_destruct' => true,
];