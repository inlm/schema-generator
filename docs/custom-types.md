
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
- `DateInterval` - `TIME`
- [`inteve/types`](https://github.com/inteve/types)
	- `Inteve\Types\HexColor` - `CHAR(6)`
	- `Inteve\Types\Html` - `MEDIUMTEXT`
	- `Inteve\Types\Md5Hash` - `CHAR(32)`
	- `Inteve\Types\Password` - `VARCHAR(255)`
	- `Inteve\Types\UniqueId` - `CHAR(10)`
	- `Inteve\Types\Url` - `VARCHAR(255)`
	- `Inteve\Types\UrlSlug` - `VARCHAR(255)`
	- `Inteve\Types\UrlPath` - `VARCHAR(255)`
