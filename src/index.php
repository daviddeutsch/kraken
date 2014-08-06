<?php

require_once(__DIR__ . '/vendor/autoload.php');

use Saltwater\Server as S;

S::init( array('Kraken\Kraken'), __DIR__ . '/navigator.cache' );

S::$n->route->go();
