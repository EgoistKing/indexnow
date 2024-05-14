<?php

namespace Egoist\IndexNow\Exceptions;

class TooManyUrlsException extends IndexNowException
{
	public function __construct()
	{
		parent::__construct("You can't submit more than 10.000 urls in one batch.", 413);
	}
}
