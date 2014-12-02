<?php
/**
 * WebHemi2
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webhemi.gixx-web.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@gixx-web.com so we can send you a copy immediately.
 *
 * @category   WebHemi2
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

define('SCRIPT_VERSION', 'v.1.2');

require_once __DIR__ . '/../vendor/zendframework/zend-crypt/Zend/Crypt/Password/PasswordInterface.php';
require_once __DIR__ . '/../vendor/zendframework/zend-crypt/Zend/Crypt/Password/Bcrypt.php';
require_once __DIR__ . '/../vendor/zendframework/zend-math/Zend/Math/Rand.php';
require_once __DIR__ . '/../vendor/zendframework/zend-stdlib/Zend/Stdlib/ArrayUtils.php';

/**
 * Generate a random password string.
 *
 * @param int $length Password length. Default is 8.
 *
 * @return string
 */
function randomPassword($length = 8)
{
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1;

    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

if (!isset($argv[1])) {
    $argv[1] = randomPassword();
}

// Output
echo 'WebHemi2 Password generator (' . SCRIPT_VERSION . ')' . PHP_EOL . PHP_EOL;
echo 'Password text:    '. $argv[1] . PHP_EOL;
echo 'Encoded password: [please wait]';
$bcrypt = new \Zend\Crypt\Password\Bcrypt();
$bcrypt->setCost(14);
$password = $bcrypt->create($argv[1]);
echo "\r" . 'Encoded password: ' . $password . PHP_EOL;

// exit program normally
exit(0);
