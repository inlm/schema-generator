
# SqlMemoryDumper

`SqlMemoryDumper` generates SQL queries (`CREATE TABLE`, `ALTER TABLE`,...) and saves it into memory.


```php
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlMemoryDumper;
$dumper->setHeader(array(
	'SET foreign_key_checks = 1;',
));
$sql = $dumper->getSql();
```

If you need, you can provide custom SQL driver in constructor:

```php
$driver = new CzProject\SqlGenerator\Drivers\MysqlDriver; // instance of CzProject\SqlGenerator\IDriver
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlMemoryDumper($driver);
```
