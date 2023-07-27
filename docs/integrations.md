
# Integrations

**Integrations** helps you integrate Schema Generator into your aplication.
They provide standardized way to use.
Every integration implements `Inlm\SchemaGenerator\IIntegration` interface.

You can call these methods:

* `$integration->createMigration($description, $testMode)` - it creates new migration for production
* `$integration->showDiff()` - it shows changes between development and production
* `$integration->updateDevelopmentDatabase($testMode)` - it updates local development database
* `$integration->initFromDatabase()` - it inits schema file & creates first SQL migrations from current database


## Lean Mapper

For Lean Mapper you can use `LeanMapperIntegration`.

It requires package [`inlm/schema-generator-leanmmaper`](https://github.com/inlm/schema-generator-leanmapper).

```php
$integration = new Inlm\SchemaGenerator\LeanMapperBridge\LeanMapperIntegration(
	$schemaFile = __DIR__ . '/schema.neon',
	$migrationsDirectory = __DIR__ . '/migrations',
	$entityDirectories = __DIR__ . '/model/entities',
	$options = NULL,
	$customTypes = NULL,
	$ignoredTables = array('migrations'),
	$databaseType = NULL, // Database::* constant or NULL => autodetected from LeanMapper\Connection
	$connection = new LeanMapper\Connection(...),
	$mapper = new LeanMapper\DefaultMapper
);
```
