<?php

/**
 * WebHemi2
 *
 * PHP version 5.4
 *
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
 * @category  WebHemi2
 * @package   WebHemi2_Component
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Component\Cipher;

use Zend\Crypt\BlockCipher;
use Zend\Crypt\Symmetric\Mcrypt;

/**
 * WebHemi2
 *
 * Cryptography component
 *
 * @category  WebHemi2
 * @package   WebHemi2_Component
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class Cipher
{
    /**
     * Encrypts plaintext with given parameters
     *
     * @static
     *
     * @param string $data   The data that will be encrypted with the given cipher and mode. If the size of the data is
     *     not n * blocksize, the data will be padded with '\0'. The returned crypttext can be larger than the size of
     *     the data that was given by data.
     * @param string $key   The key with which the data will be encrypted. If it's smaller than the required keysize, it
     *     is padded with '\0'. It is better not to use ASCII strings for keys.
     * @param string $salt   [optional] Used for the initialization in CBC, CFB, OFB modes, and in some algorithms in
     *     STREAM mode. If you do not supply an IV, while it is needed for an algorithm, the function issues a warning
     *     and uses an IV with all its bytes set to '\0'.
     * @param string $cipher   [optional] One of the MCRYPT_ciphername constants, or the name of the algorithm as string
     * @param string $mode  [optional] One of the MCRYPT_MODE_modename constants, or one of the following strings:
     *     "ecb", "cbc", "cfb", "ofb", "nofb" or "stream".
     *
     * @return string   The encrypted data, as a string.
     */
    public static function encode($data, $key, $salt = null, $cipher = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC)
    {
        // If there is no mcrypt library, we still have to support some kind of encoding.
        if (!extension_loaded('mcrypt')) {
            $key = str_split(str_pad('', strlen($data), $key, STR_PAD_RIGHT));
            $characterArray = str_split($data);

            foreach ($characterArray as $index => $character) {
                $tmp = ord($character) + ord($key[$index]);
                $characterArray[$index] = chr($tmp > 255 ? ($tmp - 256) : $tmp);
            }
            return join('', $characterArray);
        } else {
            $blockCipher = new BlockCipher(
                new Mcrypt(
                    [
                        'algorithm' => $cipher,
                        'mode' => $mode,
                        'key' => $key,
                        'salt' => $salt
                    ]
                )
            );

            return $blockCipher->encrypt($data);
        }
    }

    /**
     * Decrypts crypttext with given parameters
     *
     * @static
     *
     * @param string $data   The data that will be decrypted with the given cipher and mode. If the size of the data is
     *     not n * blocksize, the data will be padded with '\0'.
     * @param string $key   The key with which the data was encrypted. If it's smaller than the required keysize, it is
     *     padded with '\0'.
     * @param string $salt   [optional] The iv parameter is used for the initialization in CBC, CFB, OFB modes, and in
     *     some algorithms in STREAM mode. If you do not supply an IV, while it is needed for an algorithm, the function
     *     issues a warning and uses an IV with all its bytes set to '\0'.
     * @param string $cipher   [optional] One of the MCRYPT_ciphername constants, or the name of the algorithm as string
     * @param string $mode   [optional] One of the MCRYPT_MODE_modename constants, or one of the following strings:
     *     "ecb", "cbc", "cfb", "ofb", "nofb" or "stream".
     *
     * @return string   The decrypted data as a string.
     */
    public static function decode($data, $key, $salt = null, $cipher = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC)
    {
        // If there is no mcrypt library, we still have to support some kind of decoding.
        if (!extension_loaded('mcrypt')) {
            $key = str_split(str_pad('', strlen($data), $key, STR_PAD_RIGHT));
            $characterArray = str_split($data);

            foreach ($characterArray as $index => $character) {
                $tmp = ord($character) - ord($key[$index]);
                $characterArray[$index] = chr($tmp < 0 ? ($tmp + 256) : $tmp);
            }
            return join('', $characterArray);
        } else {
            $blockCipher = new BlockCipher(
                new Mcrypt(
                    [
                        'algorithm' => $cipher,
                        'mode' => $mode,
                        'key' => $key,
                        'salt' => $salt
                    ]
                )
            );

            return trim(rtrim($blockCipher->decrypt($data), "\0"));
        }
    }
}
