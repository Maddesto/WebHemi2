<?php

return array(
	'db'              => array(
		'driver'         => 'Pdo',
		'dsn'            => 'mysql:dbname=dbname;hostname=127.0.0.1',
		'username'       => 'username',
		'password'       => 'pasword',
		'driver_options' => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
		),
	),
);
