<?php namespace OurMetrics\Laravel\Mail;

use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Mail\Mailable as IlluminateMailable;
use OurMetrics\Laravel\OurMetrics;
use OurMetrics\SDK\Models\Metric;
use OurMetrics\SDK\Models\Unit;

class Mailable extends IlluminateMailable
{
	public function send( MailerContract $mailer ) {
		parent::send( $mailer );

		// todo queue metric better.. and allow changing name etc
		OurMetrics::queue( new Metric( 'Mail sent', 1.0, Unit::COUNT, [
			'mailable' => \get_class( $this ),
		] ) );
	}
}