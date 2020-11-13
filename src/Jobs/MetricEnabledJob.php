<?php namespace OurMetrics\Laravel\Jobs;

use OurMetrics\Laravel\Traits\HasMetric;
use OurMetrics\SDK\Models\Unit;

class MetricEnabledJob
{
	use HasMetric;

	public $metricUnit = Unit::SECONDS;
	public $metricName = 'Queue Processing time';

	public function setMetricMetaJobClass( $jobClass )
	{
		$this->_ourMetricsPayload['jobClass'] = $jobClass;
	}

	public function metricTimingBegin()
	{
		$this->_ourMetricsPayload['start'] = microtime( true );
	}

	public function metricTimingEnd()
	{
		$this->_ourMetricsPayload['end'] = microtime( true );
	}

	protected function getMetricValue()
	{
		return ( $this->_ourMetricsPayload['end'] ?? 0.0 ) - ( $this->_ourMetricsPayload['start'] ?? 0.0 );
	}

	protected function getMetricDimensions()
	{
		return [ 'job' => $this->_ourMetricsPayload['jobClass'] ?? 'Unknown' ];
	}
}