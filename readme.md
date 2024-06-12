# Schema Generator

[![Build Status](https://github.com/inlm/schema-generator/workflows/Build/badge.svg)](https://github.com/inlm/schema-generator/actions)
[![Downloads this Month](https://img.shields.io/packagist/dm/inlm/schema-generator.svg)](https://packagist.org/packages/inlm/schema-generator)
[![Latest Stable Version](https://poser.pugx.org/inlm/schema-generator/v/stable)](https://github.com/inlm/schema-generator/releases)
[![License](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://github.com/inlm/schema-generator/blob/master/license.md)

<a href="https://www.janpecha.cz/donate/schema-generator/"><img src="https://buymecoffee.intm.org/img/donate-banner.v1.svg" alt="Donate" height="100"></a>


## Installation

[Download a latest package](https://github.com/inlm/schema-generator/releases) or use [Composer](http://getcomposer.org/):

```
composer require inlm/schema-generator
```

Schema Generator requires PHP 5.6.0 or later.


## Usage

```php
$extractor = new Inlm\SchemaGenerator\LeanMapperBridge\LeanMapperExtractor(__DIR__ . '/model/Entities/', new LeanMapper\DefaultMapper);
$adapter = new Inlm\SchemaGenerator\Adapters\NeonAdapter(__DIR__ . '/.schema.neon');
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlDumper(__DIR__ . '/migrations/structures/');
$logger = new Inlm\SchemaGenerator\Loggers\MemoryLogger;

$generator = new Inlm\SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger, Inlm\SchemaGenerator\Database::MYSQL);
// $generator->setTestMode();

$generator->generate();
// or
$generator->generate('changes description');
```

## Documentation

Supported databases:

* MySQL


### How it works?

1) **adapter** loads old schema if exists. Schema can be stored in file, memory,...
2) **extractor** extracts new schema from entities, file, database,...
3) generator generates diff between old and new schema
4) **dumper** dumps differences to SQL file, database,...
5) **adapter** saves new schema (only if is test mode disabled)


### Extractors

Extracts new database schema from given source - entities, database or file.

* [LeanMapperExtractor](https://github.com/inlm/schema-generator-leanmapper) (package `inlm/schema-generator-leanmapper`)
* [DibiExtractor](https://github.com/inlm/schema-generator-dibi) (package `inlm/schema-generator-dibi`)
* [NeonExtractor](docs/neon-extractor.md)


### Adapters

Persists database schema in file, memory,...

- [NeonAdapter](docs/neon-adapter.md)
- [MemoryAdapter](docs/memory-adapter.md)
- [DibiAdapter](https://github.com/inlm/schema-generator-dibi) (package `inlm/schema-generator-dibi`)


### Dumpers

Dumps changes of database schema into migration file, database,...

* [SqlDumper](docs/sql-dumper.md)
* [SqlMemoryDumper](docs/sql-memory-dumper.md)
* [DibiDumper](https://github.com/inlm/schema-generator-dibi) (package `inlm/schema-generator-dibi`)
* [NullDumper](docs/null-dumper.md)


### Loggers

You can use loggers from `czproject/logger`.

* `CzProject\Logger\CliLogger`
* `CzProject\Logger\MemoryLogger`
* `CzProject\Logger\OutputLogger`
* or any else


### More

* [Default Types](docs/default-types.md)
* [Custom Types](docs/custom-types.md)
* [Table Options](docs/table-options.md)
* [Examples](docs/examples.md)
* [Integrations](docs/integrations.md)
* [Bridges](docs/bridges.md)


> [!TIP]
> If you need generate `... AFTER column` in `ALTER TABLE` statements, call:
>
> ```php
> $schemaGenerator->enablePositionChanges();
> ```

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
