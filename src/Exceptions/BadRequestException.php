<?php

namespace Egoist\IndexNow\Exceptions;

class BadRequestException extends IndexNowException
{
	public function __construct()
	{
		parent::__construct('400 Bad Request. It commonly indicates "Invalid format."', 400);
	}
}