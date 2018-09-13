
# Custom Types

```php
$generator = new Inlm\SchemaGenerator\SchemaGenerator(...);

$generator->setCustomType($name, $dbType, $dbParameters, $dbOptions);
$generator->setCustomType('money', 'DECIMAL', array(15, 4));
$generator->setCustomType('App\Model\Image', 'VARCHAR', array(100));
```

Prepared custom types for MySQL:

- `bcrypt` - `CHAR(60)`
- `md5` - `CHAR(32)`
- `money` - `DECIMAL(15, 4)`
