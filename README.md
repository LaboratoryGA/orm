# ORM
Doctrine 2 based object relational mapping module for Claromentis 7.x

## Motivation
This project was created to address the limited functionality of Claromentis' built-in database abstraction layer. These limitation include, but are not limited to:
* limited support for SQL types (particularly during table creation)
* no real support for "LIMIT" (in MSSQL "TOP"), resulting in queries which may run longer than necessary
* no support for referential integrity (particularly during table creation)
* ``DBObject`` not very useful for creating models and odd contraints (for example, you need to instatiate a "model" in order to query it)

## Drawbacks
Of course, there are drawbacks. Here is a non-exhaustive list of known shortcomings/pitfalls:
* requires installation of PDO library for particular database (not really difficult to do, but not something the Claromentis staff enable by default)
* requires actively running ``composer.phar`` to install dependencies (this support is not built into Claromentis' implementation of ``phing``)
* usage will in all probability fall outside support from Claromentis

## Installation
```shell
$ cd /Claromentis/web
$ git clone https://github.com/LaboratoryGA/orm.git intranet/orm
$ phing -Dapp=orm install
$ cd intranet/orm
$ php -d allow_url_fopen=true bin/composer.phar install
```
