<?php

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Invoice\Adapter\Doctrine\Types\EmailType;

// replace with mechanism to retrieve EntityManager in your app
$paths = array(__DIR__ . "/../src/Invoice/Adapter/Doctrine/config/mapping");
$isDevMode = false;

$config = [
    'db_user' => getenv('POSTGRES_USER'),
    'db_password' => getenv('POSTGRES_PASSWORD'),
    'db_database' => getenv('POSTGRES_DB'),
    'db_host' => getenv('POSTGRES_HOST'),
];

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_pgsql',
    'user'     => $config['db_user'],
    'password' => $config['db_password'],
    'dbname'   => $config['db_database'],
    'host'   => $config['db_host'],
);

$config = Setup::createYAMLMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

if (!Type::hasType(EmailType::EMAIL_TYPE)) {
    Type::addType(EmailType::EMAIL_TYPE, EmailType::class);
}
