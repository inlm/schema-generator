
# SqlMemoryDumper

`SqlMemoryDumper` generates SQL queries (`CREATE TABLE`, `ALTER TABLE`,...) and saves it into memory.


```php
$driver = new CzProject\SqlGenerator\Drivers\MysqlDriver;
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlMemoryDumper($driver);
$sql = $dumper->getSql();
```
