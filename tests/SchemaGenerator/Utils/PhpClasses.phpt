<?php

declare(strict_types=1);

use Inlm\SchemaGenerator\Utils\PhpClass;
use Inlm\SchemaGenerator\Utils\PhpClasses;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


// non exists class
test(function () {
	$phpClasses = new PhpClasses;
	Assert::false($phpClasses->hasClass('MyClass'));

	Assert::exception(function () use ($phpClasses) {
		$phpClasses->getClass('MyClass');
	}, \Inlm\SchemaGenerator\MissingException::class, 'Missing class MyClass.');
});


test(function () {
	$phpClasses = new PhpClasses;
	$myClass = new PhpClass(
		'MyClass',
		FALSE,
		NULL,
		[],
		NULL
	);
	$phpClasses->addClass($myClass);
	Assert::true($phpClasses->hasClass('MyClass'));
	Assert::false($phpClasses->hasClass('FooClass'));

	Assert::same($myClass, $phpClasses->getClass('MyClass'));

	Assert::false($phpClasses->isSubclassOf($myClass, 'MyClass'));
	Assert::false($phpClasses->isSubclassOf($myClass, 'ParentClass'));
	Assert::false($phpClasses->isSubclassOf($myClass, 'SuperInterface'));
});


test(function () {
	$phpClasses = new PhpClasses;

	$myClass = new PhpClass(
		'MyClass',
		FALSE,
		'ParentClass',
		[
			'SuperInterface',
		],
		NULL
	);
	$phpClasses->addClass($myClass);

	$parentClass = new PhpClass(
		'ParentClass',
		FALSE,
		NULL,
		[],
		NULL
	);
	$phpClasses->addClass($parentClass);

	Assert::false($phpClasses->isSubclassOf($myClass, 'MyClass'));
	Assert::true($phpClasses->isSubclassOf($myClass, 'ParentClass'));
	Assert::true($phpClasses->isSubclassOf($myClass, 'SuperInterface'));
	Assert::false($phpClasses->isSubclassOf($myClass, 'FooInterface'));
});
