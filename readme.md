
# Schema Generator

## Installation

[Download a latest package](https://github.com/inlm/schema-generator/releases) or use [Composer](http://getcomposer.org/):

```
composer require inlm/schema-generator
```

Schema Generator requires PHP 5.6.0 or later.


## Usage

```php
$extractor = new Inlm\SchemaGenerator\Extractors\LeanMapperExtractor(__DIR__ . '/model/Entities/', new LeanMapper\DefaultMapper);
$adapter = new Inlm\SchemaGenerator\Adapters\NeonAdapter(__DIR__ . '/.schema.neon');
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlDumper(__DIR__ . '/migrations/structures/');
$logger = new Inlm\SchemaGenerator\Loggers\MemoryLogger;

$generator = new Inlm\SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger);
// $generator->setTestMode();
$generator->generate();
```

**Extractor**

* [LeanMapperExtractor](#leanmapperextractor)
* [DibiMysqlExtractor](#dibimysqlextractor)

**Adapter**

- NeonAdapter

**Dumper**

* [SqlDumper](#sqldumper)
* [DibiDumper](#dibidumper)
* NullDumper

**Logger**

* MemoryLogger
* OutputLogger

### Custom types

```php
$generator->setCustomType($name, $dbType, $dbParameters, $dbOptions);
$generator->setCustomType('money', 'DECIMAL', array(15, 4));
```

Prepared default types:

- `money` - `DECIMAL(15, 4)`

### Default options

```php
// set option
$generator->setOption('COLLATE', 'utf8mb4_czech_ci');
// remove option
$generator->setOption('ENGINE', NULL);
```

Prepared default options:

- `ENGINE` = `InnoDB`
- `COLLATE` = `utf8mb4_czech_ci`
- `CHARACTER SET` = `utf8mb4`

## LeanMapperExtractor

``` php
$directories = '/path/to/model/Entities/';
// or
$directories = array(
	'/path/to/model/Entities/',
	'/path/to/package/Entities/',
);
$mapper = new LeanMapper\DefaultMapper;
$extractor = new Inlm\SchemaGenerator\Extractors\LeanMapperExtractor($directories, $mapper);
```

It generates schema from [Lean Mapper](http://leanmapper.com/) entities.

### Flags

```
@property string|NULL $web m:schemaType(varchar:50)
```

| Flag                    | Description                    | Example                                  |
| ----------------------- | ------------------------------ | ---------------------------------------- |
| `m:schemaType`          | column datatype                | `m:schemaType(varchar:50)`, `m:schemaType(int:10 unsigned)` |
| `m:schemaComment`       | column comment                 | `m:schemaComment(Lorem ipsum)`           |
| `m:schemaAutoIncrement` | has column AUTO_INCREMENT?     | `m:schemaAutoIncrement`                  |
| `m:schemaIndex`         | create INDEX for column        | `m:schemaIndex`                          |
| `m:schemaPrimary`       | create PRIMARY KEY for column  | `m:schemaPrimary`                        |
| `m:schemaUnique`        | create UNIQUE INDEX for column | `m:schemaUnique`                         |
If primary column is `integer` (`@property int $id`), automatically gets `AUTO_INCREMENT`.


**Default datatypes:**

| PHP type                          | Database type                         |
| --------------------------------- | ------------------------------------- |
| `integer`                         | `INT`, for primary key `INT UNSIGNED` |
| `boolean`                         | `TINYINT(1) UNSIGNED`                 |
| `float`                           | `DOUBLE`                              |
| `string`                          | `TEXT`                                |
| `DateTime` or `DateTimeInterface` | `DATETIME`                            |

You can use [custom types](#custom-types), for example `m:schemaType(money)` or `m:schemaType(money unsigned)`.

### Annotations

| Annotation       | Description         | Example                               |
| ---------------- | ------------------- | ------------------------------------- |
| `@schemaComment` | table comment       | `@schemaComment Lorem ipsum`          |
| `@schemaOption`  | table option        | `@schemaOption COLLATE utf8_czech_ci` |
| `@schemaIndex`   | create INDEX        | `@schemaIndex propertyA, propertyB`   |
| `@schemaPrimary` | create PRIMARY KEY  | `@schemaPrimary propertyA, propertyB` |
| `@schemaUnique`  | create UNIQUE INDEX | `@schemaUnique propertyA, propertyB`  |

You can define [default options](#default-options).


**Example:**

``` php
/**
 * @property int $id
 * @property string $name m:schemaType(varchar:100)
 * @schemaOption COLLATE utf8_czech_ci
 */
class Author extends \LeanMapper\Entity
{
}
```

## DibiMysqlExtractor

``` php
$connection = new Dibi\Connection(...);
$ignoredTables = array('migrations');
$extractor = new Inlm\SchemaGenerator\Extractors\DibiMysqlExtractor($connection, $ignoredTables);
```

It generates schema from existing MySQL database.

## SqlDumper

``` php
$driver = new CzProject\SqlGenerator\Drivers\MysqlDriver;
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlDumper(__DIR__ . '/migrations/structures/', $driver);
```

`SqlDumper` generates SQL queries (`CREATE TABLE`, `ALTER TABLE`,...) and saves it into file in given directory. File name has format `YYYY-MM-DD-HHMMSS.sql`. It's compatible with [Nextras\Migrations](https://github.com/nextras/migrations). You can enable deep structure and save files to subdirectories `YYYY/MM/YYYY-MM-DD-HHMMSS.sql`.

```
$dumper->setDeepStructure();
```

## DibiDumper

``` php
$connection = new Dibi\Connection(...);
$dumper = new Inlm\SchemaGenerator\Dumpers\DibiDumper($connection);
```

`DibiDumper` executes SQL queries directly in database. It requires installed [Dibi](https://dibiphp.com/).

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
