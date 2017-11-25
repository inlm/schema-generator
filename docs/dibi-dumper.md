
# DibiDumper

`DibiDumper` executes SQL queries directly in database. It requires [Dibi](https://dibiphp.com/).


```php
$connection = new Dibi\Connection(...);
$dumper = new Inlm\SchemaGenerator\Dumpers\DibiDumper($connection);
```
