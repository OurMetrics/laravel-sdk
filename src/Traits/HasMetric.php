<?php namespace OurMetrics\Laravel\Traits;

use OurMetrics\SDK\Models\Metric;
use OurMetrics\SDK\Models\Unit;

trait HasMetric
{
	public    $logsMetric         = true;
	protected $_ourMetricsPayload = [];

	protected $metricName;
	protected $metricValue      = 0.0;
	protected $metricUnit       = Unit::NONE;
	protected $metricDimensions = [];
	protected $metricTimestamp;

	protected function getMetricName() {
		return $this->metricName;
	}

	protected function getMetricValue() {
		return $this->metricValue;
	}

	protected function getMetricUnit() {
		return $this->metricUnit;
	}

	protected function getMetricDimensions() {
		return $this->metricDimensions;
	}

	protected function getMetricTimestamp() {
		return $this->metricTimestamp ?? new \DateTime();
	}

	public function getMetric(): Metric {
		return new Metric(
			$this->getMetricName(),
			$this->getMetricValue(),
			$this->getMetricUnit(),
			$this->getMetricDimensions(),
			$this->getMetricTimestamp()
		);
	}
}