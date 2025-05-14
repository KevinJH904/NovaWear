<?php
/**
 * KumbiaPHP Web Framework
 * Parámetros de conexión a la base de datos
 */
return [
    'development' => [
        'host'     => '127.0.0.1',
        'username' => 'web', //no es recomendable usar el usuario root
        'password' => '123456789',
        'name'     => 'PuntoVenta',
        'type'     => 'mysql',
        'charset'  => 'utf8',
        'port' => '3307',
        /**
         * dsn: Cadena de conexión a la base de datos
         */
        //'dsn' => '',
        /**
         * pdo: activar conexiones PDO (On/Off); descomentar para usar
         */
        //'pdo' => 'On',
        ],

    'production' => [
        /**
         * host: ip o nombre del host de la base de datos
         */
        'host'     => '127.0.0.1',
        /**
         * username: usuario con permisos en la base de datos
         */
        'username' => 'web', //no es recomendable usar el usuario root
        /**
         * password: clave del usuario de la base de datos
         */
        'password' => '123456789',
        /**
         * test: nombre de la base de datos
         */
        'name'     => 'PuntoVenta',
        /**
         * type: tipo de motor de base de datos (mysql, pgsql o sqlite)
         */
        'type'     => 'mysql',
        /**
         * charset: Conjunto de caracteres de conexión, por ejemplo 'utf8'
         */
        'charset'  => 'utf8',

        'port' => '3307',
        /**
         * dsn: cadena de conexión a la base de datos
         */
        //'dsn' => '',
        /**
         * pdo: activar conexiones PDO (OnOff); descomentar para usar
         */
        //'pdo' => 'On',
        ],
];

/**
 * Ejemplo de SQLite
 */
/*'development' => [
    'type' => 'sqlite',
    'dsn' => 'temp/data.sq3',
    'pdo' => 'On',
] */
