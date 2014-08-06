<?php

$tmp = __DIR__ . '/tmp';

$target = __DIR__ . '/../dist/kraken.phar';

if ( file_exists($target) ) unlink($target);

mkdir($tmp, 0755);

copy(__DIR__ . '/../composer.phar', __DIR__ . '/composer.phar');
copy(__DIR__ . '/../composer.json', __DIR__ . '/composer.json');

shell_exec('php composer.phar update --no-dev');

unlink(__DIR__ . '/composer.phar');
unlink(__DIR__ . '/composer.json');
unlink(__DIR__ . '/composer.lock');

rcopy(__DIR__ . '/vendor', $tmp . '/vendor');

rrmdir($tmp . '/vendor/daviddeutsch/redbean-adaptive');
rrmdir($tmp . '/vendor/daviddeutsch/saltwater/dev');
rrmdir($tmp . '/vendor/daviddeutsch/saltwater/docs');

rrmdir(__DIR__ . '/vendor');

mkdir($tmp . '/src', 0755);

rcopy(__DIR__ . '/../src', $tmp . '/src');

$phar = new Phar($target, 0, 'kraken.phar');

$phar->buildFromDirectory($tmp);

$phar->setStub(
	$phar->createDefaultStub('src/index.php', 'src/index.php')
);

rrmdir(__DIR__ . '/tmp');

function rrmdir( $path )
{
	if ( !is_dir($path) ) return;

	foreach ( scandir($path) as $item ) {
		if ($item == "." || $item == "..") continue;

		if ( is_dir($path . '/' . $item) ) {
			rrmdir($path . '/' . $item);
		} else {
			unlink($path . '/' . $item);
		}
	}

	rmdir($path);
}

function rcopy( $src, $dest )
{
	if ( is_dir($src) ) {
		if ( !is_dir($dest) ) mkdir($dest, 0755);

		foreach ( glob($src . '/*') as $item ) {
			if ( is_dir($item) ) {
				rcopy($item, $dest . '/' . basename($item));
			} else {
				copy($item, $dest . '/' . basename($item));
			}
		}
	} elseif ( file_exists($src) ) {
		copy($src, $dest . '/' . basename($src));
	}
}
