<?php
// Get Directory
$dir = realpath(dirname(__FILE__) . '/..');
define('ROOTPATH', $dir);

// Version for JS
define('VERSION', "-r1.0.0");

// Database Connection Credentials
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
// define('DB_USER', 'silahrep_miles');
// define('DB_PASS', 'S!lahRep0rtSales');
define('DB_NAME', 'transcribe');
$config['user'] = DB_USER;
$config['host'] = DB_HOST;
$config['pass'] = DB_PASS;
$config['db'] = DB_NAME;

// Connecting to DB
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$db->set_charset("utf8");
$dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST;
try {
    $PDO = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $PDO->exec("set names utf8");
} catch (PDOException $exception) {
    echo "Connection Error";
}

// Setting Root and SubRoot Paths
$config['root'] = 'http://www.uikuik.info/';
define('SUB_ROOT', ''); // if sub-directory exists
