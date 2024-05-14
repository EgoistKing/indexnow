<?php

namespace Egoist\IndexNow\Facades;

use Illuminate\Support\Facades\Facade;

class IndexNowFacade extends Facade
{
	protected static function getFacadeAccessor(): string
	{
		return 'indexnow';
	}
}
