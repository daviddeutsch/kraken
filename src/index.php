<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Saltwater\Server as S;

S::init( array('Kraken\Kraken') );

S::$n->route->go();
