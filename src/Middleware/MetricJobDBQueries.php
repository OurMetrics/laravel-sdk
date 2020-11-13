<?php namespace OurMetrics\Laravel\Middleware;

use Illuminate\Support\Facades\DB;
use OurMetrics\Laravel\OurMetrics;
use OurMetrics\SDK\Models\Metric;
use OurMetrics\SDK\Models\Unit;

class MetricJobDBQueries
{
	protected $dbConnections = [];

	public function __construct( ?array $dbConnections = null )
	{
		$this->dbConnections = $dbConnections ?? [ config( 'database.default' ) ];
	}

	/**
	 * @param mixed    $job
	 * @param callable $next
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function handle( $job, $next )
	{
		if ( empty( $job->logsMetric ) ) {
			foreach ( $this->dbConnections as $connection ) {
				DB::connection( $connection )->enableQueryLog();
			}
		}

		$next( $job );

		if ( empty( $job->logsMetric ) ) {
			$queries = 0;
			foreach ( $this->dbConnections as $connection ) {
				$queries += \count( DB::connection( $connection )->getQueryLog() );
				DB::connection( $connection )->disableQueryLog();
			}

			OurMetrics::queue( new Metric(
				method_exists( $job, 'getMetricName' ) ? $job->getMetricName() : 'Queue DB calls',
				$queries,
				method_exists( $job, 'getMetricUnit' ) ? $job->getMetricUnit() : Unit::COUNT,
				method_exists( $job, 'getMetricDimensions' ) ? $job->getMetricDimensions() : [],
				method_exists( $job, 'getMetricTimestamp' ) ? $job->getMetricTimestamp() : new \DateTime(),
			) );
		}
	}

	protected function getMetricDimensions()
	{
		return [ 'job' => \get_class( $this ) ];
	}
}