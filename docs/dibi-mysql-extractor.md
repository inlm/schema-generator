
# DibiMysqlExtractor

It generates schema from existing MySQL database. Requires [Dibi](https://dibiphp.com).

```php
$connection = new Dibi\Connection(...);
$ignoredTables = array('migrations');
$extractor = new Inlm\SchemaGenerator\Extractors\DibiMysqlExtractor($connection, $ignoredTables);
```
