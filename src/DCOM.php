<?php

namespace JordJD\DCOM;

use JordJD\DCOM\Exceptions\ConnectionException;
use JordJD\DCOM\Exceptions\DriverUnavailableException;
use JordJD\DCOM\Exceptions\InvalidObjectTypeException;
use JordJD\DCOM\Exceptions\MissingEnvironmentVariableException;
use JordJD\DCOM\Exceptions\UnsupportedDatabaseTypeException;
use mysqli;
use PDO;

abstract class DCOM
{
    private static $envPrefix = 'DCOM';
    private static $connections = [];

    public static function getConnection($name)
    {
        if (array_key_exists($name, self::$connections)) {
            return self::$connections[$name];
        }

        $objType = self::getEnvVar($name, 'object_type');
        $dbType = self::getEnvVar($name, 'database_type');

        switch ($objType) {

            case 'mysqli':

                if ($dbType != 'mysql') {
                    throw new UnsupportedDatabaseTypeException('Mysqli objects only support MySQL databases. Change your database type to \'mysql\'.');
                }

                if (!class_exists('mysqli')) {
                    throw new DriverUnavailableException('The mysqli extension is not available on this server.');
                }

                $dbHost = self::getEnvVar($name, 'database_host');
                $dbUsername = self::getEnvVar($name, 'database_username');
                $dbPassword = self::getEnvVar($name, 'database_password', true);
                $dbName = self::getEnvVar($name, 'database_name');

                try {
                    $mysqli = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
                } catch (\Exception $e) {
                    throw new ConnectionException('Failed to connect to MySQL: '.$e->getMessage(), 0, $e);
                }

                if ($mysqli->connect_errno) {
                    throw new ConnectionException("Failed to connect to MySQL: (".$mysqli->connect_errno.") ".$mysqli->connect_error, $mysqli->connect_errno);
                }

                self::$connections[$name] = $mysqli;

                return $mysqli;

            case 'pdo':

                if (!class_exists('PDO')) {
                    throw new DriverUnavailableException('The PDO extension is not available on this server.');
                }

                $availableDrivers = PDO::getAvailableDrivers();

                if (!in_array($dbType, $availableDrivers)) {
                    throw new UnsupportedDatabaseTypeException('PDO on this server does not support the requested database type. Change your database type to one of the following: '.implode(', ', $availableDrivers));
                }

                $dbHost = self::getEnvVar($name, 'database_host');
                $dbUsername = self::getEnvVar($name, 'database_username');
                $dbPassword = self::getEnvVar($name, 'database_password', true);
                $dbName = self::getEnvVar($name, 'database_name');

                $dsn = $dbType.':dbname='.$dbName.';host='.$dbHost;

                try {
                    $pdo = new PDO($dsn, $dbUsername, $dbPassword);
                } catch (\PDOException $e) {
                    throw new ConnectionException('Failed to connect to database via PDO: '.$e->getMessage(), 0, $e);
                }

                self::$connections[$name] = $pdo;

                return $pdo;

            default:

                throw new InvalidObjectTypeException('Unexpected object type: \''.$objType.'\'.');

        }
    }

    private static function getEnvVar($name, $key, $allowEmpty = false)
    {
        $varName = strtoupper(self::$envPrefix.'_'.$name.'_'.$key);

        $value = getenv($varName);

        if ($value === false || (!$allowEmpty && $value === '')) {
            throw new MissingEnvironmentVariableException('Missing or empty environment variable: \''.$varName.'\'. Please ensure it exists in your `.env` file.');
        }

        return $value;
    }
}
