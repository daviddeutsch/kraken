<?php
namespace Kraken\Provider;

use Saltwater\Server as S;
use Saltwater\Salt\Provider;

class Authentication extends Provider
{
	/**
	 * @return Provider
	 */
	public static function getProvider()
	{
		return new Authentication();
	}

	public function check()
	{
		$cfg = S::$n->config;

		// Always require authentication
		if ( !isset($_GET['auth']) || !isset($cfg->auth) ) return false;

		if ( $_GET['auth'] != $cfg->auth ) return false;

		return true;
	}
}
