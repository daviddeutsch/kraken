<?php

Kraken::start();

//Untracked files my break the pull
//shell_exec('git stash --include-untracked');

//shell_exec( 'git pull origin master' );

echo '<pre>';

//echo shell_exec('git push origin master 2>&1');exit;

echo "\ngit --version\n",
shell_exec('git --version 2>&1');

$status = shell_exec('git status --porcelain -uall 2>&1');
$status = explode(PHP_EOL, $status);
$notify = false;
$untracked = array();
$modified = array();

foreach($status as $bit){
	if(strpos($bit, '??') === 0) $untracked[] = trim(str_replace('??', '', $bit));
	if(strpos($bit, 'M') !== false && strpos($bit, 'M') <= 4)   $modified[] = trim(str_replace('M', '', $bit));
}

//Reset logic
if($modified && isset($_GET['reset'], $_GET['name']) && isset($modified[$_GET['reset']]) && $modified[$_GET['reset']] == $_GET['name']) {
	$toreset = $modified[$_GET['reset']];
	echo "\ngit reset $toreset \n",
	shell_exec('git reset '.$toreset.' 2>&1'),
	"\n git checkout HEAD^ $toreset \n",
	shell_exec('git checkout HEAD^ '.$toreset.' 2>&1');
}

if(isset($_GET['status'])) {

	if($modified) {
		echo "\ngit diff HEAD \n",
		'<h3>Files with differences:</h3><code>', htmlspecialchars(shell_exec('git diff HEAD 2>&1')), '</code>';
	}

	if($untracked) {
		echo "\n", '<h3>Untracked files:</h3><ul>';
		foreach($untracked as $bit) {
			echo '<li><a href="', $bit, '" target="_blank">', $bit, '</a></li>';
		}
		echo '</ul>';
	}

	if($modified) {
		echo "\n", '<h3>Optionally reset changed files (discarding local edits):</h3><ul>';
		foreach($modified as $key => $bit) {
			echo '<li><a href="#" onclick="if(confirm(\'Are you sure you want to reset '.$bit.'? This cannot be undone!\')) window.location.href=\'http://'.$_SERVER['SERVER_NAME'], '/deploy.php?auth='.self::$cfg->auth.'&status&reset='.$key.'&name='.$bit.'\';">', $bit, '</a></li>';
		}
		echo '</ul>';
	}
}

if($modified || $untracked) {
	echo "\ngit status --porcelain -uall\n", '<code>', shell_exec('git status --porcelain -uall 2>&1'), '</code>';

	$notify = true;
}

if ( !is_dir(__DIR__ . '/.git') && !empty(self::$cfg->repo) ) {
	unlink(__DIR__ . '/config.json');
	unlink(__DIR__ . '/deploy.php');

	echo "\ngit clone " . self::$cfg->repo . "\n",
	shell_exec('git clone ' . self::$cfg->repo . ' . 2>&1');

	echo "\nOk.</pre>";
} elseif ( is_dir(__DIR__ . '/.git') ) {
	$pullresult = shell_exec('git pull origin ' . self::$cfg->branch . ' 2>&1');
	echo "\ngit pull origin " . self::$cfg->branch . "\n",
	$pullresult;

	if(strpos($pullresult, 'Aborting') !== false) $notify = 2;

	echo "\nOk.</pre>";
} else {
	echo "\nError.</pre>";
}

if($notify && $_SERVER['REQUEST_METHOD'] == 'POST') {

	if(!$modified)        $message = "There are files on server at ".$_SERVER['HTTP_HOST']." that needs to be added to the code repo: <http://".$_SERVER['HTTP_HOST'].'/deploy.php?auth='.self::$cfg->auth.'&status|View details>';
	elseif($notify !== 2) $message = "Files have changed on ".$_SERVER['HTTP_HOST']." and may cause trouble in the future: <http://".$_SERVER['HTTP_HOST'].'/deploy.php?auth='.self::$cfg->auth.'&status|View details>';
	else                  $message = 'Automatic deploy to '.$_SERVER['HTTP_HOST']." failed: <http://".$_SERVER['HTTP_HOST'].'/deploy.php?auth='.self::$cfg->auth.'&status|View full report>';

	$payload = array(
		'color' => !$modified ? 'good' : ($notify === 2 ? 'danger' : 'warning'),
		"fallback" => $message,
		"text" => $message,
		//'pretext' => false,
		"fields" => array()
	);
	if($modified) $payload['fields'][] = array('title' => 'Server-modified files', 'value' => implode("\n", $modified), 'short' => true);
	if($untracked) {
		$listuntracked = array();
		foreach($untracked as $value) {
			$listuntracked[] = '<http://'.$_SERVER['HTTP_HOST'].'/'.$value.'|'.$value.'>';
		}
		$payload['fields'][] = array('title' => 'Untracked files', 'value' => implode("\n", $listuntracked), 'short' => true);
	}

	exec("curl -X POST --data-urlencode 'payload=".json_encode($payload)."' https://[name].slack.com/services/hooks/incoming-webhook?token=[token]");
}

class Kraken
{
	private static $cfg;

	public static function start()
	{
		if ( !self::getCfg() ) self::unauthorizedDeploy();

		if ( !self::checkAuth() ) self::unauthorizedDeploy();

	}

	public static function getCfg()
	{
		if ( !file_exists('kraken.json') ) return false;

		self::$cfg = json_decode( file_get_contents('kraken.json') );

		// Default branch is master
		if ( !isset(self::$cfg->branch) ) self::$cfg->branch = 'master';

		return true;
	}

	public static function checkAuth()
	{
		// Always require authentication
		if ( !isset($_GET['auth']) || !isset(self::$cfg->auth) ) return false;

		if ( $_GET['auth'] != self::$cfg->auth ) return false;

		return true;
	}

	public static function unauthorizedDeploy()
	{
		header("HTTP/1.0 401 Unauthorized");

		exit;
	}
}
