<?php
//require __DIR__ . '/vendor/autoload.php';
require '/home/rconfig/vendor/autoload.php'; // this will be used  to load phpseclib v2 in connection.class.php 


use phpseclib\Net\SSH2;

$ssh = new SSH2('localhost');
if (!$ssh->login('root', 'Paj3r0Sp0rt')) {
    exit('Login Failed');
}

echo $ssh->exec('pwd');
echo $ssh->exec('ls -la');
?>