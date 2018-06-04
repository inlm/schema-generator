
# Schema Generator

[![Build Status](https://travis-ci.org/inlm/schema-generator.svg?branch=master)](https://travis-ci.org/inlm/schema-generator)

<a href="https://www.patreon.com/bePatron?u=9680759"><img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" alt="Become a Patron!" height="35"></a>


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

$generator = new Inlm\SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger, Inlm\SchemaGenerator\Database::MYSQL);
// $generator->setTestMode();

$generator->generate();
// or
$generator->generate('changes description');
```

## Documentation

* [Custom Types](docs/custom-types.md)
* [Table Options](docs/table-options.md)
* [Examples](docs/examples.md)
* [Integrations](docs/integrations.md)
* [Bridges](docs/bridges.md)

Supported databases:

* MySQL


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

* [MemoryLogger](docs/memory-logger.md)
* [OutputLogger](docs/output-logger.md)


------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
