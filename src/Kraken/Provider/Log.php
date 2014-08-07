<?php
namespace Kraken\Provider;

use Saltwater\Server as S;
use Saltwater\App\Common\Log as AbstractLog;

class Log extends AbstractLog
{
	public static function getProvider() { return new Log; }

	/**
	 * @param mixed  $level
	 * @param string $message
	 * @param array  $context
	 *
	 * @return array|int|null|\RedBean_OODBBean
	 */
	public function log( $level, $message, array $context=array() )
	{
		return true;
	}
}
