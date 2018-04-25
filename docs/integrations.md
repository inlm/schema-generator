
# Integrations

**Integrations** helps you integrate Schema Generator into your aplication.
They provide standardized way to use.
Every integration implements `Inlm\SchemaGenerator\IIntegration` interface.

You can call these methods:

* `$integration->createMigration($description, $testMode)` - it creates new migration for production
* `$integration->showDiff()` - it shows changes between development and production
* `$integration->updateDevelopmentDatabase($testMode)` - it updates local development database


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


## Bridges

### czproject/phpcli

If you use `czproject/phpcli` you can use next prepared commands:

```php
$application = new CzProject\PhpCli\Application\Application;
$application->addCommand(new Inlm\SchemaGenerator\Bridges\PhpCli\CreateMigrationCommand($integration));
$application->addCommand(new Inlm\SchemaGenerator\Bridges\PhpCli\DiffCommand($integration));
$application->addCommand(new Inlm\SchemaGenerator\Bridges\PhpCli\UpdateDatabaseCommand($integration));
```
