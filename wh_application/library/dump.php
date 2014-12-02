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
error_reporting(E_ALL);
ini_set('display_errors','On');

/**
 * Dump data in a more pretty way
 */
function dump()
{
    if (func_num_args() > 0) {
        $isCli = php_sapi_name() == 'cli';
        $content = '';

        foreach (func_get_args() as $index => $var) {
            if ($isCli) {
                var_dump($var);
            } else {
                $file    = '&lt;unknown&gt';
                $line    = '&lt;unknown&gt';

                @header('Content-type:text/html;charset=UTF-8');
                ob_start();
                var_dump($var);
                $dumpData = ob_get_clean();
                $dumpData = str_replace("=>\n", '=> ', $dumpData);

                if ($index === 0) {
                    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

                    foreach ($backtrace as $traceInfo) {
                        if (in_array($traceInfo['function'], array('varDump', 'dump'))) {
                            $file  = $traceInfo['file'];
                            $line  = $traceInfo['line'];
                        } else {
                            break;
                        }
                    }

                    $variable = 'unknown';
                    $source = file($file);
                    $code = $source[$line - 1];
                    $match = array();

                    if (preg_match('#dump\((.+)\)#', $code, $match)) {
                        $maxLength = strlen($match[1]);
                        $variable = '';
                        $c = 0;
                        for ($i = 0; $i < $maxLength; $i++) {
                            if ($match[1][$i] == '(')
                                $c++;

                            if ($match[1][$i] == ')')
                                $c--;

                            if ($c < 0)
                                break;

                            $variable .= $match[1][$i];
                        }
                    }

                    $content = '<' . '?php ' . PHP_EOL;
                    $content .= '// File: ' . $file . PHP_EOL;
                    $content .= '// Line: ' . $line . PHP_EOL;
                    $content .= '// Variable' . (func_num_args() > 1 ? 's' : '') . ': ' . $variable . PHP_EOL . PHP_EOL;
                } else {
                    $content .= PHP_EOL . PHP_EOL;
                }

                $content .= $dumpData;
                unset($dumpData);
            }
        }

        if (!$isCli) {
            echo '<div style="border:1px solid gray;margin: 10px;padding:5px;background:white;word-wrap:break-word;">' . PHP_EOL;
            echo str_replace('&lt;?php', '', highlight_string($content, true)) . PHP_EOL;
            echo '</div>';
        }
    }
}

