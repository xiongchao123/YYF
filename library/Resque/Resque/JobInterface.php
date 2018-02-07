<?php

namespace Resque\Resque;

interface JobInterface
{
	/**
	 * @return bool
	 */
	public function perform();
}
