<?php

/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:hasMany(:book_tag:)
 */
class Tag extends \LeanMapper\Entity
{
}
