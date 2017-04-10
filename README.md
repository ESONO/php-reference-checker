# php-reference-checker

This tool aims to find assignments of method return values by reference in cases where the result is a value 
instead of a reference.

Example:

```php
function foo() // does not return a reference
{
    return 'x';
}

$x = &foo(); // expects a reference return value, which will trigger a notice in PHP 7.0+
```

Installation
------------

Download or clone the repository.

Usage
-----

Open a shell and navigate to the directory to check, then execute this line:

```bash
/path/to/php-reference-checker/bin/console reference-checker:check . .
```

Note the two dots at the end.

The first argument denotes the file or directory to check for reference assignments. Directories are checked recursively.

The second argument denotes the root class path. This is needed so that a repository of all methods can be built in
order to check if assignments of returned references are indeed references or simple values.

License
-------

The tool is MIT-licensed (see LICENSE file).

Contributions
-------------

Contributions are highly welcome. Please follow these guidelines when contributing:

- you accept that anyone can use your code by the terms of the MIT license.
- please add unit tests.
- please use the PHP-CS-Fixer with the following command to ensure a common styling:

```bash
php-cs-fixer fix --config=".php_cs.dist" src tests
```
