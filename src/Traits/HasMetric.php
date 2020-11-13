<?php namespace OurMetrics\Laravel\Traits;

use OurMetrics\SDK\Models\Metric;
use OurMetrics\SDK\Models\Unit;

trait HasMetric
{
	public $logsMetric = true;

	public $metricName;
	public $metricValue      = 0.0;
	public $metricUnit       = Unit::NONE;
	public $metricDimensions = [];
	public $metricTimestamp;

	protected $_ourMetricsPayload = [];

	public function getMetricName() {
		return $this->metricName;
	}

	public function getMetricValue() {
		return $this->metricValue;
	}

	public function getMetricUnit() {
		return $this->metricUnit;
	}

	public function getMetricDimensions() {
		return $this->metricDimensions;
	}

	public function getMetricTimestamp() {
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