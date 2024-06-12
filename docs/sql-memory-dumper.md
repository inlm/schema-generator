
# SqlMemoryDumper

`SqlMemoryDumper` generates SQL queries (`CREATE TABLE`, `ALTER TABLE`,...) and saves it into memory.


```php
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlMemoryDumper;
$dumper->setHeader(array(
	'SET foreign_key_checks = 1;',
));
$sql = $dumper->getSql();
```
