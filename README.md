# ORM
Doctrine 2 based object relational mapping module for Claromentis 7.x

## Motivation
This project was created to address the limited functionality of Claromentis' 
built-in database abstraction layer. These limitation include, but are not limited to:
* limited support for SQL types (particularly during table creation)
* no real support for "LIMIT" (in MSSQL "TOP"), resulting in queries which may 
run longer than necessary
* no support for referential integrity (particularly during table creation)
* ``DBObject`` not very useful for creating models and odd contraints (for 
example, you need to instatiate a "model" in order to query it)

## Drawbacks
Of course, there are drawbacks. Here is a non-exhaustive list of known 
shortcomings/pitfalls:
* requires installation of PDO library for particular database (not really 
difficult to do, but not something the Claromentis staff enable by default)
* requires actively running ``composer.phar`` to install dependencies (this 
support is not built into Claromentis' implementation of ``phing``)
* usage will in all probability fall outside support from Claromentis
* even though the implemented migrator here is more comprehensive than 
Claromentis' DBAL, the ``phing`` build doesn't support it - specifically, the 
generated ``_init/schema/schema.php`` file will not contain the custom 
migration scripts. See the section below *Using Migrator* on tips on how to use 
the migrator.

## Installation
```shell
$ cd /Claromentis/web
$ git clone https://github.com/LaboratoryGA/orm.git intranet/orm
$ phing -Dapp=orm install
$ cd intranet/orm
$ php -d allow_url_fopen=true bin/composer.phar install
```

## Using Migrator
The idea here is to replace the content of the ``01_xxx.php``, ``02_xxx.php``, 
etc. files with instructions to invoke the migrator. However, as mentioned
previously, if you run the following instruction:
```shell
$ phing -Dapp=your_app_name release_major
```
the contents of the resulting ``schema/schema.php`` will **not** contain the 
migrator instructions at all (although the individual ``migrations/to_??.??`` 
**will** contain them).

However, this is not all that much of a deal-breaker. The ``schema.php`` file
runs only once - during an actual ``install`` call to ``phing``. This is also
true of the ``init.php`` file. Therefore, the work-around is to simply add the
following to the **top** of your ``init.php`` file:
```php
<?php
foreach (glob(__DIR__ . '/[0-9][0-9]_*.php') as $migration) {
	include $migration;
}
```

Using the above methodology, the Claromentis system will still effectively keep
track of installations/upgrades. This is because ``schema.php`` will set the
version to the very latest, and all the migrations will run in the ``init.php``
as would be expected. Any subsequent ``upgrade`` invocations would explicitly
ignore ``init.php`` and only the relevant individual ``to_??.??.php`` files 
will be run, and since those are populated correctly by ``release_major``, 
the system should remain in perfect synchronization.

### Example migrator
```php
<?php
$migrator = new Claromentis\Orm\Migrator();

$migrator->up(function(Doctrine\DBAL\Schema\Schema $schema) {
	$table = $schema->createTable('test');
	
	$table->addColumn('id', 'integer');
	$table->setPrimaryKey(['id']);
});
```
