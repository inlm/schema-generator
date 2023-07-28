<?php

use Inlm\SchemaGenerator\Utils\PhpClass;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	$phpClass = new PhpClass(
		'MyClass',
		FALSE,
		NULL,
		[],
		NULL
	);

	Assert::same('MyClass', $phpClass->getName());
	Assert::false($phpClass->isAbstract());
	Assert::false($phpClass->hasParent());
	Assert::null($phpClass->getParent());
	Assert::false($phpClass->extendsClass('ParentClass'));
	Assert::false($phpClass->implementsInterface('SuperInterface'));

	Assert::exception(function () use ($phpClass) {
		$phpClass->loadFile();

	}, \Inlm\SchemaGenerator\InvalidStateException::class, 'PHP class has no file defined.');
});


test(function () {
	$phpClass = new PhpClass(
		'MyClass',
		TRUE,
		'ParentClass',
		[
			'SuperInterface',
			'FooInterface',
		],
		NULL
	);

	Assert::same('MyClass', $phpClass->getName());
	Assert::true($phpClass->isAbstract());
	Assert::true($phpClass->hasParent());
	Assert::same('ParentClass', $phpClass->getParent());
	Assert::true($phpClass->extendsClass('ParentClass'));
	Assert::false($phpClass->extendsClass('FooClass'));
	Assert::true($phpClass->implementsInterface('SuperInterface'));
	Assert::true($phpClass->implementsInterface('FooInterface'));
	Assert::false($phpClass->implementsInterface('BarInterface'));
});
