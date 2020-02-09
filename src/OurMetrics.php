<?php namespace OurMetrics\Laravel;

use Illuminate\Support\Arr;
use OurMetrics\SDK\Client;
use OurMetrics\SDK\Models\Metric;
use OurMetrics\SDK\Models\MetricList;

/**
 * @method static void queue( Metric[]|Metric|MetricList $metrics )
 * @method static void registerShutdownFunction()
 * @method static void dispatchQueued()
 * @method static void dispatch( Metric[]|Metric|MetricList $metrics )
 */
class OurMetrics extends \Illuminate\Support\Facades\Facade
{
	protected static function getFacadeAccessor() { return 'ourmetrics'; }

	public static function bind() {
		app()->singleton( 'ourmetrics', function () {
			return new Client(
				config( 'ourmetrics.project_key' ),
				Arr::except( config( 'ourmetrics' ), [ 'project_key' ] ),
				true
			);
		} );
	}
}