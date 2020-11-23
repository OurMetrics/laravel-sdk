<?php namespace OurMetrics\Laravel\Middleware;

use Illuminate\Http\Request;
use OurMetrics\Laravel\OurMetrics;
use OurMetrics\SDK\Models\Metric;
use OurMetrics\SDK\Models\Unit;

class MetricHttpProcessingTime
{
	/**
	 * @param Request     $request
	 * @param \Closure    $next
	 * @param boolean     $trackUser (include user id in tags?)
	 * @param null|string $grouping  (NULL, 'endpoint' or 'controller')
	 *
	 * @return mixed
	 */
	public function handle( $request, \Closure $next, $trackUser = true, $grouping = 'endpoint' )
	{
		$request->attributes->set( '_ourMetrics_start', microtime( true ) );
		$request->attributes->set( '_ourMetrics_trackUser', $trackUser );
		$request->attributes->set( '_ourMetrics_grouping', $grouping );

		return $next( $request );
	}

	/**
	 * @param Request $request
	 * @param         $response
	 *
	 * @throws \Exception
	 */
	public function terminate( Request $request, $response )
	{
		$tags = [];

		if ( $request->attributes->get( '_ourMetrics_trackUser', false ) ) {
			$tags['user'] = optional( $request->user() )->id ?? 'guest';
		}

		if ( $grouping = $request->attributes->get( '_ourMetrics_grouping' ) ) {
			if ( $grouping === 'controller' ) {
				if ( ( $controller = optional( $request->route() )->getAction() ) && isset( $controller['controller'] ) ) {
					$tags['controller'] = $controller['controller'];
				} else {
					$tags['controller'] = 'Closure';
				}
			} elseif ( $grouping === 'endpoint' ) {
				$tags['endpoint'] = $request->method() . ' ' . ( optional( $request->route() )->uri() ?? '/' );
			}
		}

		OurMetrics::dispatch( new Metric(
			'HTTP Processing Time',
			microtime( true ) - ( defined( 'LARAVEL_START' ) ? LARAVEL_START : $request->attributes->get( '_ourMetrics_start' ) ),
			Unit::SECONDS,
			$tags
		) );
	}
}