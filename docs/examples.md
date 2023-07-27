
# Examples

## How generate migration file from Lean Mapper entities

It requires package [`inlm/schema-generator-leanmmaper`](https://github.com/inlm/schema-generator-leanmapper).


```php
$extractor = new Inlm\SchemaGenerator\LeanMapperBridge\LeanMapperExtractor(__DIR__ . '/model/Entities/', new LeanMapper\DefaultMapper);
$adapter = new Inlm\SchemaGenerator\Adapters\NeonAdapter(__DIR__ . '/.schema.neon');
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlDumper(__DIR__ . '/migrations/structures/');
$logger = new CzProject\Logger\MemoryLogger;

$generator = new Inlm\SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger, Inlm\SchemaGenerator\Database::MYSQL);
$generator->generate();
```


## How initialize schema from existing database

```php
$connection = new Dibi\Connection(...);
$ignoredTables = array('migrations');
$extractor = new Inlm\SchemaGenerator\DibiBridge\DibiExtractor($connection, $ignoredTables);
$adapter = new Inlm\SchemaGenerator\Adapters\NeonAdapter(__DIR__ . '/.schema.neon');
$dumper = new Inlm\SchemaGenerator\Dumpers\NullDumper;
$logger = new CzProject\Logger\MemoryLogger;

$generator = new Inlm\SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger);
$generator->generate();
```


## How update database from Lean Mapper entities during development

It requires package [`inlm/schema-generator-leanmmaper`](https://github.com/inlm/schema-generator-leanmapper).


```php
$connection = new Dibi\Connection(...);
$ignoredTables = array('migrations');
$extractor = new Inlm\SchemaGenerator\LeanMapperBridge\LeanMapperExtractor(__DIR__ . '/model/Entities/', new LeanMapper\DefaultMapper);
$adapter = new Inlm\SchemaGenerator\DibiBridge\DibiAdapter($connection, $ignoredTables);
$dumper = new Inlm\SchemaGenerator\DibiBridge\DibiDumper($connection);
$logger = new CzProject\Logger\MemoryLogger;

$generator = new Inlm\SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger);
$generator->generate();
```
