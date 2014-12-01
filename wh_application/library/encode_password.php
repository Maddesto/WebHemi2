<?php

require_once __DIR__ . '/../vendor/zendframework/zend-crypt/Zend/Crypt/Password/PasswordInterface.php';
require_once __DIR__ . '/../vendor/zendframework/zend-crypt/Zend/Crypt/Password/Bcrypt.php';
require_once __DIR__ . '/../vendor/zendframework/zend-math/Zend/Math/Rand.php';
require_once __DIR__ . '/../vendor/zendframework/zend-stdlib/Zend/Stdlib/ArrayUtils.php';

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

if (!isset($argv[1])) {
    $argv[1] = randomPassword();
}

$bcrypt = new \Zend\Crypt\Password\Bcrypt();
$bcrypt->setCost(14);
$bcrypt->create($argv[1]);

echo PHP_EOL . 'WebHemi2 Password generator' . PHP_EOL;
echo 'Password text: '. $argv[1] . PHP_EOL;
echo 'Encoded password: ' . $bcrypt->create($argv[1]);
echo PHP_EOL;
