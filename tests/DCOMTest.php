<?php

namespace JordJD\DCOM\Tests;

use JordJD\DCOM\DCOM;
use JordJD\DCOM\Exceptions\InvalidObjectTypeException;
use JordJD\DCOM\Exceptions\MissingEnvironmentVariableException;
use JordJD\DCOM\Exceptions\UnsupportedDatabaseTypeException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class DCOMTest extends TestCase
{
    public function testMissingEnvironmentVariableHasSpecificException()
    {
        putenv('DCOM_MISSING_OBJECT_TYPE');

        try {
            DCOM::getConnection('missing');
            $this->fail('Expected a missing environment variable exception.');
        } catch (MissingEnvironmentVariableException $e) {
            $this->assertNotFalse(strpos($e->getMessage(), 'DCOM_MISSING_OBJECT_TYPE'));
        }
    }

    public function testInvalidObjectTypeHasSpecificException()
    {
        putenv('DCOM_INVALID_OBJECT_TYPE=odbc');
        putenv('DCOM_INVALID_DATABASE_TYPE=mysql');

        try {
            DCOM::getConnection('invalid');
            $this->fail('Expected an invalid object type exception.');
        } catch (InvalidObjectTypeException $e) {
            $this->assertNotFalse(strpos($e->getMessage(), 'odbc'));
        }
    }

    public function testUnsupportedMysqliDatabaseTypeHasSpecificException()
    {
        putenv('DCOM_UNSUPPORTED_OBJECT_TYPE=mysqli');
        putenv('DCOM_UNSUPPORTED_DATABASE_TYPE=pgsql');

        try {
            DCOM::getConnection('unsupported');
            $this->fail('Expected an unsupported database type exception.');
        } catch (UnsupportedDatabaseTypeException $e) {
            $this->assertNotFalse(strpos($e->getMessage(), 'MySQL'));
        }
    }

    public function testEmptyDatabasePasswordsAreAllowed()
    {
        putenv('DCOM_EMPTY_DATABASE_PASSWORD=');

        $method = new ReflectionMethod(DCOM::class, 'getEnvVar');
        $method->setAccessible(true);

        $this->assertSame('', $method->invoke(null, 'empty', 'database_password', true));
    }
}
