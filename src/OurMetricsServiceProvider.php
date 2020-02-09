<?php namespace OurMetrics\Laravel;

use Illuminate\Queue\Jobs\JobName;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use OurMetrics\Laravel\Traits\MetricsJobProcessingTime;

class OurMetricsServiceProvider extends ServiceProvider
{
	public function boot() {
		OurMetrics::bind();

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