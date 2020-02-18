<?php namespace OurMetrics\Laravel;

use Illuminate\Queue\Jobs\JobName;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use OurMetrics\Laravel\Traits\MetricsJobProcessingTime;

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

		// Queue events
		Queue::before( function ( MetricsJobProcessingTime $event ) {
			if ( $event->logsMetric ) {
				$event->setMetricMetaJobClass( JobName::resolve( \get_class( $event ), $event->job->payload() ) );
				$event->metricTimingBegin();
			}
		} );

		Queue::after( function ( MetricsJobProcessingTime $event ) {
			if ( $event->logsMetric ) {
				$event->metricTimingEnd();
				OurMetrics::queue( $event->getMetric() );
			}
		} );
	}

	public function register() {
		//
	}
}