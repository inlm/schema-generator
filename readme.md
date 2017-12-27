
# Schema Generator

[![Build Status](https://travis-ci.org/inlm/schema-generator.svg?branch=master)](https://travis-ci.org/inlm/schema-generator)


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
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlDumper(__DIR__ . '/migrations/structures/', new CzProject\SqlGenerator\Drivers\MysqlDriver);
$logger = new Inlm\SchemaGenerator\Loggers\MemoryLogger;

$generator = new Inlm\SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger);
// $generator->setTestMode();

$generator->generate();
// or
$generator->generate('changes description');
```

## Documentation

* [Custom Types](docs/custom-types.md)
* [Table Options](docs/table-options.md)
* [Examples](docs/examples.md)


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
* [DibiDumper](docs/dibi-dumper.md)
* [NullDumper](docs/null-dumper.md)


### Loggers

* [MemoryLogger](docs/memory-logger.md)
* [OutputLogger](docs/output-logger.md)


------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
