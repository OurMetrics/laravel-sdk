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
			OurMetrics::dispatchQueued();
		} );
	}

	public function register() {
		//
	}
}