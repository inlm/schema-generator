<?php

/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $website
 * @schemaPrimary id
 * @schemaIndex name, website
 * @schemaUnique website
 */
class Book extends \LeanMapper\Entity
{
}
