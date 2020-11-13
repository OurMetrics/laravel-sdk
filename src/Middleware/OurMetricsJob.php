<?php namespace OurMetrics\Laravel\Middleware;

use OurMetrics\Laravel\OurMetrics;
use OurMetrics\Laravel\Traits\HasMetric;
use OurMetrics\SDK\Models\Metric;
use OurMetrics\SDK\Models\Unit;

class OurMetricsJob
{
	use HasMetric;

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

		echo "Going to next ....\n";
		$next( $job );
		echo "Sending to OurMetrics ....\n";

		$end = microtime( true );

		if ( empty($job->logsMetric)  ) {
			OurMetrics::queue( new Metric( [
				method_exists($job, 'getMetricName') ? $job->getMetricName() : 'Queue processing time',
				$end - $start,
				method_exists($job, 'getMetricUnit') ? $job->getMetricUnit() : Unit::SECONDS,
				method_exists($job, 'getMetricDimensions') ? $job->getMetricDimensions() : [],
				method_exists($job, 'getMetricTimestamp') ? $job->getMetricTimestamp() : new \DateTime(),
			] ) );
		}
	}

	public function setMetricMetaJobClass( $jobClass )
	{
		$this->_ourMetricsPayload['jobClass'] = $jobClass;
	}

	protected function getMetricDimensions()
	{
		return [ 'job' => $this->_ourMetricsPayload['jobClass'] ?? 'Unknown' ];
	}
}