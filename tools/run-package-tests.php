<?php

declare(strict_types=1);

use Nvl\Suite\Quality\PackageTestRunner;

$root = dirname(__DIR__);
$autoload = $root.'/vendor/autoload.php';

if (! is_file($autoload)) {
    fwrite(STDERR, "Install root Composer dependencies before running package tests.\n");

    exit(2);
}

require $autoload;
require __DIR__.'/package-test-runner.php';

$catalog = require __DIR__.'/package-family.php';

if (! is_array($catalog)) {
    fwrite(STDERR, "The package family catalog is invalid.\n");

    exit(2);
}

$validatedCatalog = [];

foreach ($catalog as $key => $value) {
    if (! is_string($key)) {
        fwrite(STDERR, "The package family catalog must use string keys.\n");

        exit(2);
    }

    $validatedCatalog[$key] = $value;
}

exit((new PackageTestRunner($root, $validatedCatalog))->run(array_slice($argv, 1)));
