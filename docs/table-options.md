
# Table Options

```php
$generator = new Inlm\SchemaGenerator\SchemaGenerator(...);

// set option
$generator->setOption('COLLATE', 'utf8mb4_czech_ci');

// remove option
$generator->removeOption('ENGINE');
$generator->setOption('ENGINE', NULL);
```

Prepared options:

- `ENGINE` = `InnoDB`
- `COLLATE` = `utf8mb4_czech_ci`
- `CHARACTER SET` = `utf8mb4`
