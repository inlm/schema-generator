
# LeanMapperExtractor

`LeanMapperExtractor` generates schema from [Lean Mapper](http://leanmapper.com/) entities.

```php
$directories = '/path/to/model/Entities/';
// or
$directories = array(
	'/path/to/model/Entities/',
	'/path/to/package/Entities/',
);

$mapper = new LeanMapper\DefaultMapper;
$extractor = new Inlm\SchemaGenerator\Extractors\LeanMapperExtractor($directories, $mapper);
```

## Flags

```
@property string|NULL $web m:schemaType(varchar:50)
```

| Flag                    | Description                    | Example                                  |
| ----------------------- | ------------------------------ | ---------------------------------------- |
| `m:schemaType`          | column datatype                | `m:schemaType(varchar:50)`, `m:schemaType(int:10 unsigned)` |
| `m:schemaComment`       | column comment                 | `m:schemaComment(Lorem ipsum)`           |
| `m:schemaAutoIncrement` | has column AUTO_INCREMENT?     | `m:schemaAutoIncrement`                  |
| `m:schemaIndex`         | create INDEX for column        | `m:schemaIndex`                          |
| `m:schemaPrimary`       | create PRIMARY KEY for column  | `m:schemaPrimary`                        |
| `m:schemaUnique`        | create UNIQUE INDEX for column | `m:schemaUnique`                         |

If primary column is `integer` (`@property int $id`), automatically gets `AUTO_INCREMENT`.

Flag `m:schemaType` can be used with [custom types](custom-types.md) too - for example `m:schemaType(money)` or `m:schemaType(money unsigned)`.

In case if is flag `m:schemaType` missing, it uses [default type](default-types.md) or your [custom type](custom-types.md).


## Annotations

| Annotation       | Description         | Example                               |
| ---------------- | ------------------- | ------------------------------------- |
| `@schemaComment` | table comment       | `@schemaComment Lorem ipsum`          |
| `@schemaOption`  | table option        | `@schemaOption COLLATE utf8_czech_ci` |
| `@schemaIndex`   | create INDEX        | `@schemaIndex propertyA, propertyB`   |
| `@schemaPrimary` | create PRIMARY KEY  | `@schemaPrimary propertyA, propertyB` |
| `@schemaUnique`  | create UNIQUE INDEX | `@schemaUnique propertyA, propertyB`  |
| `@schemaIgnore`  | ignore entity       | `@schemaIgnore`                       |

You can define default [table options](table-options.md) globally.


## Example

```php
/**
 * @property int $id
 * @property string $name m:schemaType(varchar:100)
 * @schemaOption COLLATE utf8_czech_ci
 */
class Author extends \LeanMapper\Entity
{
}
```
