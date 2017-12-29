
# SqlMemoryDumper

`SqlMemoryDumper` generates SQL queries (`CREATE TABLE`, `ALTER TABLE`,...) and saves it into memory.


```php
$driver = Inlm\SchemaGenerator\Dumpers\SqlDumper::MYSQL; // or instance of CzProject\SqlGenerator\IDriver
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlMemoryDumper($driver);
$sql = $dumper->getSql();
```
