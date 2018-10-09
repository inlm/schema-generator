
# SqlDumper

`SqlDumper` generates SQL queries (`CREATE TABLE`, `ALTER TABLE`,...) and saves it into file in given directory. File name has format `YYYY-MM-DD-HHMMSS-label.sql`. It's compatible with [Nextras\Migrations](https://github.com/nextras/migrations).


```php
$dumper = new Inlm\SchemaGenerator\Dumpers\SqlDumper(__DIR__ . '/migrations/structures/');
$dumper->setHeader(array(
	'SET foreign_key_checks = 1;',
));
```

You can enable deep structure and save files to subdirectories:

```php
$dumper->setOutputStructure($dumper::YEAR_MONTH); // YYYY/MM/YYYY-MM-DD-HHMMSS.sql
$dumper->setOutputStructure($dumper::YEAR); // YYYY/YYYY-MM-DD-HHMMSS.sql
$dumper->setOutputStructure($dumper::FLAT); // YYYY-MM-DD-HHMMSS.sql
```

If you need generate `... AFTER column` in `ALTER TABLE` statements, call:

```php
$dumper->enablePositionChanges();
```
