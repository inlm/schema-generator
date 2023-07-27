
# Bridges

## czproject/phpcli

If you use `czproject/phpcli` you can use next prepared commands:

```php
$integration = new Inlm\SchemaGenerator\Integrations\LeanMapperIntegration(...);
$application = new CzProject\PhpCli\Application\Application;
$application->addCommand(new Inlm\SchemaGenerator\Bridges\PhpCli\CreateMigrationCommand($integration));
$application->addCommand(new Inlm\SchemaGenerator\Bridges\PhpCli\DiffCommand($integration));
$application->addCommand(new Inlm\SchemaGenerator\Bridges\PhpCli\UpdateDatabaseCommand($integration));
$application->addCommand(new Inlm\SchemaGenerator\Bridges\PhpCli\InitFromDatabaseCommand($integration));
```


## [LeanMapper](https://leanmapper.com/)

Use package [`inlm/schema-generator-leanmapper`](https://github.com/inlm/schema-generator-leanmapper).


## [Dibi](https://dibiphp.com/)

Use package [`inlm/schema-generator-dibi`](https://github.com/inlm/schema-generator-dibi).
