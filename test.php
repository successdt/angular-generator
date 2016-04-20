<?php
define('HOST', '118.70.118.159');
define('USER', 'root');
define('PASS', 'qlhv@123121');

define('COMMAND', 'git pull origin develop');
define('COMMAND2', 'git pull origin staging');

define('DIR', '/var/www/dev/api');
define('DIR2', '/var/www/dev/ui');
define('DIR3', '/var/www/test/ui');
define('DIR4', '/var/www/test/api');
//define('DIR5', '/var/www/release/ui');
//define('DIR6', '/var/www/release/api');
//define('DIR7', '/var/www/gass/ui');
//define('DIR8', '/var/www/gass/api');



include('Net/SSH2.php');
$ssh = new Net_SSH2(HOST, 22);
if (!$ssh->login(USER, PASS)) {
	exit('Login Failed DESU!');
}

if (isset($_GET['project'])) {
	if ($_GET['project'] == 'ui') {
		echo $ssh->exec('cd ' . DIR2 . ';' . COMMAND);
		echo '=>dev/api<br/>';
		echo $ssh->exec('cd ' . DIR3 . ';' . COMMAND2);
		echo '=>test/ui<br/>';
	}


	if ($_GET['project'] == 'api') {
		echo $ssh->exec('cd ' . DIR . ';' . COMMAND);
		echo '=>dev/ui<br/>';
		echo $ssh->exec('cd ' . DIR4 . ';' . COMMAND2);
		echo '=>test/api<br/>';
	}

} else {
	echo "project [ui, api] paramater is required";
}


//echo $ssh->exec('cd ' . DIR5 . ';' . COMMAND2);
//echo '=>release/ui<br/>';
//echo $ssh->exec('cd ' . DIR6 . ';' . COMMAND2);
//echo '=>release/api<br/>';
//echo $ssh->exec('cd ' . DIR7 . ';' . COMMAND2);
//echo '=>gass/ui<br/>';
//echo $ssh->exec('cd ' . DIR8 . ';' . COMMAND2);
//echo '=>gass/api<br/>';