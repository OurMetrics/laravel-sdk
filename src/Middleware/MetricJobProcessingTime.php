<?php namespace OurMetrics\Laravel\Middleware;

use OurMetrics\Laravel\OurMetrics;
use OurMetrics\SDK\Models\Metric;
use OurMetrics\SDK\Models\Unit;

class MetricJobProcessingTime
{
	/**
	 * @param mixed    $job
	 * @param callable $next
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function handle( $job, $next )
	{
		$start = microtime( true );

		$next( $job );

		$end = microtime( true );

		if ( empty( $job->logsMetric ) ) {
			OurMetrics::queue( new Metric(
				method_exists( $job, 'getMetricName' ) ? $job->getMetricName() : 'Queue: Time per Job',
				$end - $start,
				method_exists( $job, 'getMetricUnit' ) ? $job->getMetricUnit() : Unit::SECONDS,
				method_exists( $job, 'getMetricDimensions' ) ? $job->getMetricDimensions() : $this->getMetricDimensions( $job ),
				method_exists( $job, 'getMetricTimestamp' ) ? $job->getMetricTimestamp() : new \DateTime(),
			) );
		}
	}

	protected function getMetricDimensions( $job )
	{
		return [ 'job' => \get_class( $job ) ];
	}
}