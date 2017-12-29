
# SqlDumper

`SqlDumper` generates SQL queries (`CREATE TABLE`, `ALTER TABLE`,...) and saves it into file in given directory. File name has format `YYYY-MM-DD-HHMMSS-label.sql`. It's compatible with [Nextras\Migrations](https://github.com/nextras/migrations).


```php
$driver = Inlm\SchemaGenerator\Dumpers\SqlDumper::MYSQL; // or instance of CzProject\SqlGenerator\IDriver
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlDumper(__DIR__ . '/migrations/structures/', $driver);
```

You can enable deep structure and save files to subdirectories:

```php
$dumper->setOutputStructure($dumper::YEAR_MONTH); // YYYY/MM/YYYY-MM-DD-HHMMSS.sql
$dumper->setOutputStructure($dumper::YEAR); // YYYY/YYYY-MM-DD-HHMMSS.sql
$dumper->setOutputStructure($dumper::FLAT); // YYYY-MM-DD-HHMMSS.sql
```
