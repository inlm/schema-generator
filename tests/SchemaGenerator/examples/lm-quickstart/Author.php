<?php

/**
 * @property int $id
 * @property string $name
 * @property Book[] $books m:belongsToMany
 * @property Book[] $reviewedBooks m:belongsToMany(reviewer_id)
 * @property string|null $web
 */
class Author extends \LeanMapper\Entity
{
}
