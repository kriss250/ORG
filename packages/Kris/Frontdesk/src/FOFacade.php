<?php namespace Kris\Frontdesk;

use Illuminate\Support\Facades\Facade;

class FOFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'FO'; }

}