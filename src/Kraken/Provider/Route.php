<?php
namespace Kraken\Provider;

use Saltwater\Server as S;
use Saltwater\App\Provider\Route as AppRoute;

class Route extends AppRoute
{
	public function go()
	{
		if ( S::$n->authentication->check() ) {
			parent::go();
		} else {
			S::halt(401, 'Unauthorized');
		}
	}
}
