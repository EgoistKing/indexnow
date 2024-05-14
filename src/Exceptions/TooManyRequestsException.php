<?php

namespace Egoist\IndexNow\Exceptions;

class TooManyRequestsException extends IndexNowException
{
	public function __construct()
	{
		parent::__construct('429 Too May Requests. Commonly it means "Too Many Requests (potential Spam)"', 429);
	}
}