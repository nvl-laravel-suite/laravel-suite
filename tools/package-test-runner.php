<?php

declare(strict_types=1);

namespace Nvl\Suite\Quality;

use InvalidArgumentException;
use PDO;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * Runs isolated package test suites through a bounded process pool.
 */
final readonly class PackageTestRunner
{
    /** @var list<string> */
    private const array APPLICATION_ISOLATED_PACKAGES = [
        'activity',
        'auth',
        'comments',
        'mail-notifications',
        'media',
    ];

    /** @var list<string> */
    private const array DATABASE_TEST_PRIORITY = [
        'content',
        'auth',
        'activity',
        'comments',
        'pages',
        'tenancy',
    ];

    /** @var list<string> */
    private const array FULL_TEST_PRIORITY = [
        'pages',
        'media',
        'auth',
        'content',
        'comments',
        'mail-notifications',
        'tenancy',
    ];

    private string $isolationRoot;

    /**
     * @param  array<string, mixed>  $catalog
     */
    public function __construct(
        private string $root,
        private array $catalog,
    ) {
        $this->isolationRoot = $root.'/storage/framework/cache/package-tests/'.getmypid();
    }

    /**
     * Run the requested package test mode.
     *
     * @param  list<string>  $arguments
     */
    public function run(array $arguments): int
    {
        try {
            $options = $this->options($arguments);
            $packages = $this->packages($options['database'], $options['packages']);

            if ($options['list']) {
                fwrite(STDOUT, implode("\n", $packages)."\n");

                return 0;
            }

            if ($options['database']) {
                foreach ($packages as $package) {
                    $this->createDatabase($this->databaseName($package));
                }
            }

            $exitCode = $this->runPackages(
                $this->executionOrder($packages, $options['database']),
                $options['database'],
                $options['concurrency'],
            );

            if ($exitCode !== 0 || ! $options['database']) {
                return $exitCode;
            }

            return $this->runIntegrationContracts();
        } catch (Throwable $exception) {
            fwrite(STDERR, $exception->getMessage()."\n");

            return 2;
        } finally {
            (new Filesystem)->remove($this->isolationRoot);
        }
    }

    /**
     * @param  list<string>  $arguments
     * @return array{database: bool, list: bool, concurrency: int, packages: list<string>}
     */
    private function options(array $arguments): array
    {
        $database = false;
        $list = false;
        $packages = [];
        $configuredConcurrency = getenv('PACKAGE_TEST_CONCURRENCY');
        $concurrency = is_string($configuredConcurrency) && $configuredConcurrency !== ''
            ? filter_var($configuredConcurrency, FILTER_VALIDATE_INT)
            : 2;

        foreach ($arguments as $argument) {
            if ($argument === '--database') {
                $database = true;

                continue;
            }

            if ($argument === '--list') {
                $list = true;

                continue;
            }

            if (str_starts_with($argument, '--concurrency=')) {
                $concurrency = filter_var(
                    substr($argument, strlen('--concurrency=')),
                    FILTER_VALIDATE_INT,
                );

                continue;
            }

            if (str_starts_with($argument, '--')) {
                throw new InvalidArgumentException("Unknown option [{$argument}].");
            }

            $packages[] = $argument;
        }

        if (! is_int($concurrency) || $concurrency < 1 || $concurrency > 8) {
            throw new InvalidArgumentException('Package test concurrency must be between 1 and 8.');
        }

        return [
            'database' => $database,
            'list' => $list,
            'concurrency' => $concurrency,
            'packages' => array_values(array_unique($packages)),
        ];
    }

    /**
     * @param  list<string>  $requested
     * @return list<string>
     */
    private function packages(bool $database, array $requested): array
    {
        $catalogKey = $database ? 'database_tested' : 'packages';
        $available = $this->catalog[$catalogKey] ?? null;

        if (! is_array($available) || ! array_is_list($available)) {
            throw new RuntimeException("Package family [{$catalogKey}] metadata is invalid.");
        }

        foreach ($available as $package) {
            if (! is_string($package) || preg_match('/^[a-z0-9-]+$/', $package) !== 1) {
                throw new RuntimeException("Package family [{$catalogKey}] metadata is invalid.");
            }
        }

        if ($requested === []) {
            return $available;
        }

        foreach ($requested as $package) {
            if (! in_array($package, $available, true)) {
                throw new InvalidArgumentException("Package [nvl/{$package}] is not available in this test mode.");
            }
        }

        return $requested;
    }

    /**
     * Start historically slow suites first to minimize process-pool wall time.
     *
     * @param  list<string>  $packages
     * @return list<string>
     */
    private function executionOrder(array $packages, bool $database): array
    {
        $priority = array_flip($database ? self::DATABASE_TEST_PRIORITY : self::FULL_TEST_PRIORITY);
        $canonical = array_flip($packages);

        usort(
            $packages,
            static fn (string $left, string $right): int => [
                $priority[$left] ?? PHP_INT_MAX,
                $canonical[$left],
            ] <=> [
                $priority[$right] ?? PHP_INT_MAX,
                $canonical[$right],
            ],
        );

        return $packages;
    }

    /**
     * @param  list<string>  $packages
     */
    private function runPackages(array $packages, bool $database, int $concurrency): int
    {
        $pending = $packages;
        $running = [];
        $firstFailure = 0;

        while ($pending !== [] || $running !== []) {
            while ($pending !== [] && count($running) < $concurrency) {
                $package = array_shift($pending);

                $process = $this->packageProcess($package, $database);
                $process->start();
                $running[$package] = [
                    'process' => $process,
                    'started_at' => hrtime(true),
                ];
            }

            $completed = false;

            foreach ($running as $package => $state) {
                $process = $state['process'];

                if ($process->isRunning()) {
                    continue;
                }

                $completed = true;
                $exitCode = $process->getExitCode() ?? 1;
                $duration = (int) round((hrtime(true) - $state['started_at']) / 1_000_000);

                fwrite(STDOUT, "\nPackage nvl/{$package}\n");
                fwrite(STDOUT, $process->getOutput());
                fwrite(STDERR, $process->getErrorOutput());
                fwrite(
                    $exitCode === 0 ? STDOUT : STDERR,
                    sprintf(
                        "nvl/%s: %s (%.2f s)\n",
                        $package,
                        $exitCode === 0 ? 'passed' : 'failed',
                        $duration / 1000,
                    ),
                );

                if ($exitCode !== 0 && $firstFailure === 0) {
                    $firstFailure = $exitCode;
                }

                (new Filesystem)->remove($this->packageIsolationPath($package));
                unset($running[$package]);
            }

            if (! $completed && $running !== []) {
                usleep(20_000);
            }
        }

        (new Filesystem)->remove($this->isolationRoot);

        return $firstFailure;
    }

    private function packageProcess(string $package, bool $database): Process
    {
        $packageDirectory = $this->root.'/packages/nvl/'.$package;
        $testDirectory = $packageDirectory.'/tests';
        $configuration = $packageDirectory.'/phpunit.xml.dist';
        $relativeTestDirectory = 'packages/nvl/'.$package.'/tests';
        $relativeConfiguration = 'packages/nvl/'.$package.'/phpunit.xml.dist';

        if (! is_dir($testDirectory) || ! is_file($configuration)) {
            throw new RuntimeException("Package [nvl/{$package}] has no executable test suite.");
        }

        $command = [
            PHP_BINARY,
            '-d',
            'variables_order=EGPCS',
            $this->root.'/vendor/bin/pest',
            '--test-directory='.$relativeTestDirectory,
            '--configuration='.$relativeConfiguration,
            '--bootstrap=vendor/autoload.php',
            '--compact',
        ];

        if ($database) {
            array_push($command, ...$this->databaseTests($package));
        } else {
            $command[] = $relativeTestDirectory;
        }

        $environment = $this->preparePackageEnvironment($package);

        if ($database) {
            $environment['DB_DATABASE'] = $this->databaseName($package);
        }

        return new Process(
            $command,
            $this->root,
            $environment,
            timeout: null,
        );
    }

    /**
     * @return array<string, string>
     */
    private function preparePackageEnvironment(string $package): array
    {
        $filesystem = new Filesystem;
        $packageIsolation = $this->packageIsolationPath($package);
        $filesystem->remove($packageIsolation);

        if (in_array($package, self::APPLICATION_ISOLATED_PACKAGES, true)) {
            $application = $this->prepareIsolatedApplication($package);

            return [
                'APP_BASE_PATH' => $application,
            ];
        }

        return [];
    }

    private function prepareIsolatedApplication(string $package): string
    {
        $source = $this->root.'/vendor/orchestra/testbench-core/laravel';
        $target = $this->packageIsolationPath($package).'/application';

        if (! is_dir($source)) {
            throw new RuntimeException('The Testbench application skeleton is not installed.');
        }

        $filesystem = new Filesystem;
        $filesystem->remove($target);
        $filesystem->mirror($source, $target, options: ['override' => true]);

        foreach (['bootstrap/cache', 'database/migrations'] as $mutablePath) {
            $path = $target.'/'.$mutablePath;
            $filesystem->remove($path);
            $filesystem->mkdir($path);
        }

        $this->prepareStorage($target.'/storage');

        return $target;
    }

    private function prepareStorage(string $storage): void
    {
        $filesystem = new Filesystem;
        $filesystem->remove($storage);
        $filesystem->mkdir([
            $storage.'/app/public',
            $storage.'/framework/cache/data',
            $storage.'/framework/data',
            $storage.'/framework/sessions',
            $storage.'/framework/testing/disks',
            $storage.'/framework/views',
            $storage.'/logs',
        ]);
    }

    private function packageIsolationPath(string $package): string
    {
        return $this->isolationRoot.'/'.$package;
    }

    /**
     * @return list<string>
     */
    private function databaseTests(string $package): array
    {
        $quality = $this->catalog['quality'] ?? null;
        $descriptors = is_array($quality) ? ($quality['packages'] ?? null) : null;
        $descriptor = is_array($descriptors) ? ($descriptors[$package] ?? null) : null;
        $evidence = is_array($descriptor) ? ($descriptor['migration_tests'] ?? null) : null;

        if (! is_array($evidence) || ! array_is_list($evidence)) {
            throw new RuntimeException("Package [nvl/{$package}] has invalid database test metadata.");
        }

        $tests = [];

        foreach ($evidence as $relativePath) {
            if (! is_string($relativePath) || ! str_ends_with($relativePath, 'Test.php')) {
                continue;
            }

            $path = 'packages/nvl/'.$package.'/'.$relativePath;

            if (! is_file($this->root.'/'.$path)) {
                throw new RuntimeException(
                    "Package [nvl/{$package}] database test [{$relativePath}] does not exist.",
                );
            }

            $tests[] = $path;
        }

        if ($tests === []) {
            throw new RuntimeException("Package [nvl/{$package}] has no database contract tests.");
        }

        return $tests;
    }

    private function databaseName(string $package): string
    {
        $name = 'nvl_'.str_replace('-', '_', $package).'_test_ci';

        if (preg_match('/^nvl_[a-z_]+_test_ci$/', $name) !== 1) {
            throw new RuntimeException('Unsafe package test database name.');
        }

        return $name;
    }

    private function createDatabase(string $database): void
    {
        $driver = $this->databaseDriver();
        $pdo = $this->adminConnection($driver);

        if ($driver === 'pgsql') {
            $pdo->exec('CREATE DATABASE "'.$database.'"');

            return;
        }

        $pdo->exec(
            'CREATE DATABASE `'.$database.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
        );
    }

    private function runIntegrationContracts(): int
    {
        $database = 'nvl_package_test_integration';
        $this->createDatabase($database);
        $process = new Process(
            [
                PHP_BINARY,
                '-d',
                'memory_limit=1G',
                $this->root.'/vendor/bin/pest',
                '--compact',
                $this->root.'/tests/Feature/Integration',
            ],
            $this->root,
            ['DB_DATABASE' => $database],
            timeout: null,
        );
        $process->run();

        fwrite(STDOUT, "\nDatabase integration contracts\n");
        fwrite(STDOUT, $process->getOutput());
        fwrite(STDERR, $process->getErrorOutput());

        return $process->getExitCode() ?? 1;
    }

    private function databaseDriver(): string
    {
        $driver = getenv('DB_CONNECTION');

        if (! is_string($driver) || ! in_array($driver, ['pgsql', 'mysql', 'mariadb'], true)) {
            throw new RuntimeException('Database package tests require PostgreSQL, MySQL, or MariaDB.');
        }

        return $driver;
    }

    private function adminConnection(string $driver): PDO
    {
        $host = $this->environment('DB_HOST', '127.0.0.1');
        $port = $this->environment('DB_PORT', $driver === 'pgsql' ? '5432' : '3306');
        $username = $this->environment('DB_USERNAME', $driver === 'pgsql' ? 'postgres' : 'root');
        $password = $this->environment('DB_PASSWORD', '');
        $dsn = $driver === 'pgsql'
            ? sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $host,
                $port,
                $this->environment('DB_ADMIN_DATABASE', 'postgres'),
            )
            : sprintf('mysql:host=%s;port=%s', $host, $port);

        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    private function environment(string $key, string $default): string
    {
        $value = getenv($key);

        return is_string($value) && $value !== '' ? $value : $default;
    }
}
