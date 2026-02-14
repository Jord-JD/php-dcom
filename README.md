# PHP DCOM

The PHP DCOM (Database Connection Object Manager) is designed to allow easy management of 
multiple database connection objects. It stores database connection details within a `.env`
file in the root of your project.

## Installation

Simple run `composer require DivineOmega/php-dcom` to install.

You will need to set environment variables to use this library. If you are not using a
framework that allows you to set these easily, you can install the `dotenv-loader` 
package which will immediately allow do so via a `.env` file.

To install `dotenv-loader`, just run: `composer require DivineOmega/dotenv-loader`.

## Usage

These usage instructions assume you are able to set environment variables via a `.env`
file. If needed, create a `.env` file in the root of your project (alongside your 
`composer.json` file). This will be used to store you database connection details,
in the following format.

```
DCOM_MAIN_OBJECT_TYPE=mysqli
DCOM_MAIN_DATABASE_TYPE=mysql

DCOM_MAIN_DATABASE_HOST=localhost
DCOM_MAIN_DATABASE_USERNAME=username
DCOM_MAIN_DATABASE_PASSWORD=password
DCOM_MAIN_DATABASE_NAME=testdb
```

DCOM supports the creation of both `mysqli` and `pdo` objects.

After setting up your `.env` file, you can then establish a connection to the 
database, as shown below. DCOM will ensure your application only uses a 
single connection to each database per request.

```php
require 'vendor/autoload.php';

use \JordJD\DCOM\DCOM;

$mysqli = DCOM::getConnection("main");
```

Note that the connection name passed to the `getConnection` method matches 
what is defined in the `.env` file. You can therefore change this argument in
order to manage and connect to multiple databases easily.

## Example

For an actual example of how to use PHP DCOM, see the [`test` directory](test/).
