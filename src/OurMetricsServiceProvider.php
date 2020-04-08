<?php namespace OurMetrics\Laravel;

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Jobs\JobName;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class OurMetricsServiceProvider extends ServiceProvider
{
	public function boot() {
		// Config
		$this->publishes( [
			__DIR__ . '/../config/ourmetrics.php' => config_path( 'ourmetrics.php' ),
		] );

		$this->mergeConfigFrom(
			__DIR__ . '/../config/ourmetrics.php', 'ourmetrics'
		);

		// Container binding
		OurMetrics::bind();

		/* --------------------------------------------------------
		 * Controller events
		 * ----------------------------------------------------- */
//		if(config('ourmetrics.track_controllers')) { // todo use Sentry event handler to listen
//					$this->events->listen('', [$this, $handler]);
//		}

		/*
			'router.matched' => 'routerMatched',                         // Until Laravel 5.1
			'Illuminate\Routing\Events\RouteMatched' => 'routeMatched',  // Since Laravel 5.2
		 */

		/**
		 * protected function routerMatchedHandler(Route $route)
		 * {
		 * if ($route->getName()) {
		 * // someaction (route name/alias)
		 * $routeName = $route->getName();
		 * } elseif ($route->getActionName()) {
		 * // SomeController@someAction (controller action)
		 * $routeName = $route->getActionName();
		 * }
		 * if (empty($routeName) || $routeName === 'Closure') {
		 * // /someaction // Fallback to the url
		 * $routeName = $route->uri();
		 * }
		 *
		 * Integration::addBreadcrumb(new Breadcrumb(
		 * Breadcrumb::LEVEL_INFO,
		 * Breadcrumb::TYPE_NAVIGATION,
		 * 'route',
		 * $routeName
		 * ));
		 * Integration::setTransaction($routeName);
		 * }
		 */

		/* --------------------------------------------------------
		 * Queue events
		 * ----------------------------------------------------- */
		Queue::before( function ( JobProcessing $event ) {
			if ( ! empty( $event->logsMetric ) ) {
				$event->setMetricMetaJobClass( JobName::resolve( \get_class( $event ), $event->job->payload() ) );
				$event->metricTimingBegin();
			}
		} );

		Queue::after( function ( JobProcessed $event ) {
			if ( ! empty( $event->logsMetric ) ) {
				$event->metricTimingEnd();
				OurMetrics::queue( $event->getMetric() );
			}
		} );

		// Ensure that queued metrics are dispatched eventually.
		Queue::looping( function () {
			try {
				OurMetrics::dispatchQueued();
			} catch ( \Exception $exception ) {
				// Let us not break the app because of metrics. App > Metrics
			}
		} );
	}

	public function register() {
		//
	}
}