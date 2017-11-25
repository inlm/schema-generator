
# NeonExtractor

Extracts DB schema from [NEON](https://ne-on.org) file. Structure of file is same as for [NeonAdapter](neon-adapter.md).

```php
$schema = new CzProject\SqlSchema\Schema;
$adapter = new Inlm\SchemaGenerator\Extractors\NeonExtractor(__DIR__ . '/new-schema.neon');
```
