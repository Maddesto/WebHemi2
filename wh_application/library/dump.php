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
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

/**
 * Dump data in a more pretty way
 */
function dump()
{
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    if (func_num_args() > 0) {
        $isCli = php_sapi_name() == 'cli';
        $content = '';

        foreach (func_get_args() as $index => $var) {
            if ($isCli) {
                var_dump($var);
                continue;
            }
            $file    = '&lt;unknown&gt';
            $line    = '&lt;unknown&gt';

            header('Content-type:text/html;charset=UTF-8');
            ob_start();
            var_dump($var);
            $dumpData = ob_get_clean();
            $dumpData = str_replace("=>\n", '=> ', $dumpData);

            if ($index === 0) {
                $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

                foreach ($backtrace as $traceInfo) {
                    if (!in_array($traceInfo['function'], ['varDump', 'dump'])) {
                        break;
                    }
                    $file  = $traceInfo['file'];
                    $line  = $traceInfo['line'];
                }

                $variable = 'unknown';
                $source = file($file);
                $code = $source[$line - 1];
                $match = [];

                if (preg_match('#dump\((.+)\)#', $code, $match)) {
                    $maxLength = strlen($match[1]);
                    $variable = '';
                    $c = 0;
                    for ($i = 0; $i < $maxLength; $i++) {
                        if ($match[1][$i] == '(') {
                            $c++;
                        }

                        if ($match[1][$i] == ')') {
                            $c--;
                        }

                        if ($c < 0) {
                            break;
                        }

                        $variable .= $match[1][$i];
                    }
                }

                $content = '<' . '?php ' . PHP_EOL;
                $content .= '// File: ' . $file . PHP_EOL;
                $content .= '// Line: ' . $line . PHP_EOL;
                $content .= '// Variable' . (func_num_args() > 1 ? 's' : '') . ': ' . $variable;
            }
            $content .= PHP_EOL . PHP_EOL;

            $content .= $dumpData;
            unset($dumpData);
        }

        if (!$isCli) {
            echo '<div style="border:1px solid gray;margin:10px;padding:5px;background:white;word-wrap:break-word;">';
            echo PHP_EOL . str_replace('&lt;?php', '', highlight_string($content, true)) . PHP_EOL;
            echo '</div>';
        }
    }
}
