
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

```php
$integration = new Inlm\SchemaGenerator\Integrations\LeanMapperIntegration(
	$schemaFile = __DIR__ . '/schema.neon',
	$migrationsDirectory = __DIR__ . '/migrations',
	$entityDirectories = __DIR__ . '/model/entities',
	$options = NULL,
	$customTypes = NULL,
	$ignoredTables = array('migrations'),
	$connection = new LeanMapper\Connection(...),
	$mapper = new LeanMapper\DefaultMapper
);
```
