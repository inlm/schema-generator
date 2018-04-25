
# DibiDumper

`DibiDumper` executes SQL queries directly in database. It requires [Dibi](https://dibiphp.com/). It supports only MySQL at this time.


```php
$connection = new Dibi\Connection(...);
$dumper = new Inlm\SchemaGenerator\Dumpers\DibiDumper($connection);
$dumper->setHeader(array(
	'SET foreign_key_checks = 1;',
));
```
