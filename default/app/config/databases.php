<?php
/**
 * KumbiaPHP Web Framework
 * Parámetros de conexión a la base de datos
 */
return [
    'development' => [
        'host'     => 'db-mysql-sfo3-58066-do-user-18079590-0.m.db.ondigitalocean.com',
        'username' => 'doadmin', //no es recomendable usar el usuario root
        'password' => 'AVNS_6-KQujFcvyFYZv3VV7X',
        'name'     => 'PuntoVentaKevo',
        'type'     => 'mysql',
        'charset'  => 'utf8',
        'port' => '25060',
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
        'host'     => 'db-mysql-sfo3-58066-do-user-18079590-0.m.db.ondigitalocean.com',
        'username' => 'doadmin', //no es recomendable usar el usuario root
        'password' => 'AVNS_6-KQujFcvyFYZv3VV7X',
        'name'     => 'PuntoVenta',
        'type'     => 'mysql',
        'charset'  => 'utf8',
        'port' => '25060',
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
