
# Examples

## How generate migration file from Lean Mapper entities

```php
$extractor = new Inlm\SchemaGenerator\Extractors\LeanMapperExtractor(__DIR__ . '/model/Entities/', new LeanMapper\DefaultMapper);
$adapter = new Inlm\SchemaGenerator\Adapters\NeonAdapter(__DIR__ . '/.schema.neon');
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlDumper(__DIR__ . '/migrations/structures/');
$logger = new Inlm\SchemaGenerator\Loggers\MemoryLogger;

$generator = new Inlm\SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger);
$generator->generate();
```


## How initialize schema from existing database

```php
$connection = new Dibi\Connection(...);
$ignoredTables = array('migrations');
$extractor = new Inlm\SchemaGenerator\Extractors\DibiExtractor($connection, $ignoredTables);
$adapter = new Inlm\SchemaGenerator\Adapters\NeonAdapter(__DIR__ . '/.schema.neon');
$dumper = new Inlm\SchemaGenerator\Dumpers\NullDumper;
$logger = new Inlm\SchemaGenerator\Loggers\MemoryLogger;

$generator = new Inlm\SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger);
$generator->generate();
```


## How update database from Lean Mapper entities during development

```php
$connection = new Dibi\Connection(...);
$ignoredTables = array('migrations');
$extractor = new Inlm\SchemaGenerator\Extractors\LeanMapperExtractor(__DIR__ . '/model/Entities/', new LeanMapper\DefaultMapper);
$adapter = new Inlm\SchemaGenerator\Adapters\DibiAdapter($connection, $ignoredTables);
$dumper = new Inlm\SchemaGenerator\Dumpers\DibiDumper($connection);
$logger = new Inlm\SchemaGenerator\Loggers\MemoryLogger;

$generator = new Inlm\SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger);
$generator->generate();
```
