<?php namespace OurMetrics\Laravel;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use OurMetrics\Laravel\Middleware\MetricHttpProcessingTime;

class OurMetricsServiceProvider extends ServiceProvider
{
	public function boot()
	{
		/* --------------------------------------------------------
		 * Config
		 * ----------------------------------------------------- */
		if ( $this->app->runningInConsole() ) {
			$this->publishes( [
				__DIR__ . '/../config/ourmetrics.php' => config_path( 'ourmetrics.php' ),
			] );

			$this->mergeConfigFrom(
				__DIR__ . '/../config/ourmetrics.php', 'ourmetrics'
			);
		}

		/* --------------------------------------------------------
		 * Container binding
		 * ----------------------------------------------------- */
		OurMetrics::bind();

		/* --------------------------------------------------------
		 * Queue events
		 * ----------------------------------------------------- */
		Queue::looping( function () {
			try {
				// Ensure that queued metrics are dispatched eventually.
				OurMetrics::dispatchQueued();
			} catch ( \Exception $exception ) {
				// Let us not break the app because of metrics. App > Metrics
			}
		} );

		/* --------------------------------------------------------
		 * Middleware alias
		 * ----------------------------------------------------- */
		$router = $this->app->make( Router::class );
		$router->aliasMiddleware( 'ourmetrics', MetricHttpProcessingTime::class );
	}

	public function register()
	{
		//
	}
}