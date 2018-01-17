
# DibiExtractor

It generates schema from existing database. Requires [Dibi](https://dibiphp.com). It supports only MySQL at this time.

```php
$connection = new Dibi\Connection(...);
$ignoredTables = array('migrations');
$extractor = new Inlm\SchemaGenerator\Extractors\DibiExtractor($connection, $ignoredTables);
```
