
# DibiAdapter

It loads schema from existing database. Requires [Dibi](https://dibiphp.com). It supports only MySQL at this time.

```php
$connection = new Dibi\Connection(...);
$ignoredTables = array('migrations');
$extractor = new Inlm\SchemaGenerator\Adapters\DibiAdapter($connection, $ignoredTables);
```

**Note:** saving of schema is not supported, use [DibiDumper](dibi-dumper.md).
