
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
- PHPDoc types
	- `positive-int` - `UNSIGNED INT`
	- `negative-int` - `INT`
	- `non-positive-int` - `INT`
	- `non-negative-int` - `UNSIGNED INT`
	- `non-zero-int` - `INT`
	- `lowercase-string` - `TEXT`
	- `literal-string` - `TEXT`
	- `class-string` - `TEXT`
	- `interface-string` - `TEXT`
	- `trait-string` - `TEXT`
	- `enum-string` - `TEXT`
	- `callable-string` - `TEXT`
	- `array-key` - `TEXT`
	- `numeric-string` - `TEXT`
	- `non-empty-string` - `TEXT`
	- `non-empty-lowercase-string` - `TEXT`
	- `truthy-string` - `TEXT`
	- `non-falsy-string` - `TEXT`
	- `non-empty-literal-string` - `TEXT`
- [`inteve/types`](https://github.com/inteve/types)
	- `Inteve\Types\HexColor` - `CHAR(6)`
	- `Inteve\Types\Html` - `MEDIUMTEXT`
	- `Inteve\Types\Md5Hash` - `CHAR(32)`
	- `Inteve\Types\Password` - `VARCHAR(255)`
	- `Inteve\Types\UniqueId` - `CHAR(10)`
	- `Inteve\Types\Url` - `VARCHAR(255)`
	- `Inteve\Types\UrlSlug` - `VARCHAR(255)`
	- `Inteve\Types\UrlPath` - `VARCHAR(255)`
