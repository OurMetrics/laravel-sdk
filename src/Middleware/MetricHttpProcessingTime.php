<?php namespace OurMetrics\Laravel\Middleware;

use OurMetrics\Laravel\OurMetrics;
use OurMetrics\SDK\Models\Metric;
use OurMetrics\SDK\Models\Unit;
use Illuminate\Http\Request;

class MetricHttpProcessingTime
{
	/**
	 * @param Request  $request
	 * @param \Closure $next
	 *
	 * @return mixed
	 */
	public function handle( $request, \Closure $next ) {
		$request->attributes->set( '_ourMetrics_start', microtime( true ) );

		return $next( $request );
	}

	/**
	 * @param Request $request
	 * @param         $response
	 */
	public function terminate( Request $request, $response ) {
		OurMetrics::dispatch( new Metric(
			'HTTP Processing Time',
			microtime( true ) - ( defined( 'LARAVEL_START' ) ? LARAVEL_START : $request->attributes->get( '_ourMetrics_start' ) ),
			Unit::SECONDS,
			[
				'user'     => optional( $request->user() )->id ?? 'guest',
				'endpoint' => $request->method() . ' ' . ( optional( $request->route() )->uri() ?? '/' ),
			],
			new \DateTime()
		) );
	}
}