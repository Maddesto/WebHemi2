<?php

return function ($class) {
    static $map;
    if (!$map) {
        $map = include __DIR__ . '/autoload_classmap.php';
    }

    if (!isset($map[$class])) {
        return false;
    }
    /** @noinspection PhpIncludeInspection */
    return include $map[$class];
};
