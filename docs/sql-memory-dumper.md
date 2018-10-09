
# SqlMemoryDumper

`SqlMemoryDumper` generates SQL queries (`CREATE TABLE`, `ALTER TABLE`,...) and saves it into memory.


```php
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlMemoryDumper;
$dumper->setHeader(array(
	'SET foreign_key_checks = 1;',
));
$sql = $dumper->getSql();
```

If you need generate `... AFTER column` in `ALTER TABLE` statements, call:

```php
$dumper->enablePositionChanges();
```
