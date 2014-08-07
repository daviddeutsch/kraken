<?php
namespace Kraken\Provider;

use Saltwater\Server as S;
use Saltwater\Salt\Provider;
use Saltwater\App\Common\Config as AbstractConfig;

class Config extends AbstractConfig
{
	static $config;

	public static function getProvider()
	{
		if ( empty(self::$config) ) {
			self::$config = json_decode(
				file_get_contents(__DIR__ . '/../../config.json')
			);
		}

		return self::$config;
	}
}
