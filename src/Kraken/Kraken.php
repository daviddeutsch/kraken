<?php

namespace Kraken;

use Saltwater\Salt\Module;

class Kraken extends Module
{
	protected $require = array(
		'module' => array('Saltwater\App\App')
	);

	protected $provide = array(
		'provider' => array(
			'authentication', 'config', 'log', 'route'
		),
		'context' => array(
			'Kraken'
		),
		'service' => array(

		)
	);
}
