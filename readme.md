
# Schema Generator

[![Tests Status](https://github.com/inlm/schema-generator/workflows/Tests/badge.svg)](https://github.com/inlm/schema-generator/actions)


# Schema Generator

[![Tests Status](https://github.com/inlm/schema-generator/workflows/Tests/badge.svg)](https://github.com/inlm/schema-generator/actions)

<a href="https://www.janpecha.cz/donate/schema-generator/"><img src="https://buymecoffee.intm.org/img/donate-banner.v1.svg" alt="Donate" height="100"></a>


## Installation

[Download a latest package](https://github.com/inlm/schema-generator/releases) or use [Composer](http://getcomposer.org/):

```
composer require inlm/schema-generator
```

Schema Generator requires PHP 7.2.0 or later.


## Usage

```php
$extractor = new Inlm\SchemaGenerator\Extractors\LeanMapperExtractor(__DIR__ . '/model/Entities/', new LeanMapper\DefaultMapper);
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

* [LeanMapperExtractor](docs/leanmapper-extractor.md)
* [DibiExtractor](docs/dibi-extractor.md)
* [NeonExtractor](docs/neon-extractor.md)


### Adapters

Persists database schema in file, memory,...

- [NeonAdapter](docs/neon-adapter.md)
- [MemoryAdapter](docs/memory-adapter.md)
- [DibiAdapter](docs/dibi-adapter.md)


### Dumpers

Dumps changes of database schema into migration file, database,...

* [SqlDumper](docs/sql-dumper.md)
* [SqlMemoryDumper](docs/sql-memory-dumper.md)
* [DibiDumper](docs/dibi-dumper.md)
* [NullDumper](docs/null-dumper.md)


### Loggers

You can use loggers from `czproject/logger`.

* `CzProject\Logger\CliLogger`
* `CzProject\Logger\MemoryLogger`
* `CzProject\Logger\OutputLogger`
* or any else


### More

* [Custom Types](docs/custom-types.md)
* [Table Options](docs/table-options.md)
* [Examples](docs/examples.md)
* [Integrations](docs/integrations.md)
* [Bridges](docs/bridges.md)


------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
