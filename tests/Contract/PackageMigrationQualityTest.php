<?php

declare(strict_types=1);

use PhpParser\ParserFactory;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

it('keeps every released package migration checksum locked and parseable', function (): void {
    $root = dirname(__DIR__, 2);
    $catalog = require $root.'/tools/package-family.php';
    $contractRelativePath = $catalog['quality']['released_migrations_contract'] ?? null;

    expect($contractRelativePath)->toBeString()->not->toBeEmpty();

    $contractPath = $root.'/'.$contractRelativePath;

    expect($contractPath)->toBeFile();

    $contracts = json_decode(
        file_get_contents($contractPath),
        true,
        flags: JSON_THROW_ON_ERROR,
    );
    $parser = (new ParserFactory)->createForNewestSupportedVersion();

    foreach ($contracts['packages'] ?? [] as $package => $contract) {
        foreach ($contract['migrations'] ?? [] as $relativePath => $checksum) {
            $migrationPath = $root.'/packages/nvl/'.$package.'/'.$relativePath;

            expect($migrationPath)->toBeFile()
                ->and(migrationContractChecksum($migrationPath))->toBe($checksum)
                ->and($parser->parse(file_get_contents($migrationPath)))->toBeArray();
        }
    }
});

/**
 * Hash executable migration tokens using the package-contract normalization.
 */
function migrationContractChecksum(string $path): string
{
    $normalized = '';

    foreach (token_get_all(file_get_contents($path)) as $token) {
        if (is_array($token)) {
            if (in_array($token[0], [T_COMMENT, T_DOC_COMMENT, T_WHITESPACE], true)) {
                continue;
            }

            $normalized .= $token[1];

            continue;
        }

        $normalized .= $token;
    }

    return hash('sha256', $normalized);
}

it('declares executable migration evidence and database-family coverage', function (): void {
    $root = dirname(__DIR__, 2);
    $catalog = require $root.'/tools/package-family.php';
    $contractRelativePath = $catalog['quality']['released_migrations_contract'] ?? null;

    expect($contractRelativePath)->toBeString()->not->toBeEmpty();

    $contractPath = $root.'/'.$contractRelativePath;

    expect($contractPath)->toBeFile();

    $contracts = json_decode(
        file_get_contents($contractPath),
        true,
        flags: JSON_THROW_ON_ERROR,
    );
    $workflow = Yaml::parseFile($root.'/.github/workflows/package-quality.yml');
    $postgresCommands = collect($workflow['jobs']['postgresql']['steps'] ?? [])
        ->pluck('run')->filter(static fn (mixed $command): bool => is_string($command))->implode("\n");
    $mysqlCommands = collect($workflow['jobs']['mysql-family']['steps'] ?? [])
        ->pluck('run')->filter(static fn (mixed $command): bool => is_string($command))->implode("\n");
    $databaseRunner = new Process([
        PHP_BINARY,
        $root.'/tools/run-package-tests.php',
        '--database',
        '--list',
    ], $root);
    $databaseRunner->mustRun();
    $databasePackages = preg_split('/\s+/', trim($databaseRunner->getOutput())) ?: [];

    foreach ($contracts['packages'] ?? [] as $package => $contract) {
        if (($contract['migrations'] ?? []) === []) {
            continue;
        }

        $evidence = $catalog['quality']['packages'][$package]['migration_tests'] ?? [];

        expect($evidence)->toBeArray()->not->toBeEmpty()
            ->and($catalog['database_tested'])->toContain($package)
            ->and($databasePackages)->toContain($package)
            ->and($postgresCommands)->toContain('tools/run-package-tests.php --database')
            ->and($mysqlCommands)->toContain('tools/run-package-tests.php --database');

        foreach ($evidence as $testPath) {
            expect($root.'/packages/nvl/'.$package.'/'.$testPath)->toBeFile();
        }
    }
});

it('selects at least one executable contract for every real database package', function (): void {
    $root = dirname(__DIR__, 2);
    $catalog = require $root.'/tools/package-family.php';

    foreach ($catalog['database_tested'] as $package) {
        $evidence = $catalog['quality']['packages'][$package]['migration_tests'] ?? [];
        $tests = array_values(array_filter(
            $evidence,
            static fn (string $path): bool => str_ends_with($path, 'Test.php'),
        ));

        expect($tests)->not->toBeEmpty();

        foreach ($tests as $testPath) {
            expect($root.'/packages/nvl/'.$package.'/'.$testPath)->toBeFile();
        }
    }
});

it('selects the remaining-package process races on their supported database jobs', function (): void {
    $root = dirname(__DIR__, 2);
    $catalog = require $root.'/tools/package-family.php';
    $selected = $catalog['quality']['packages'];
    $workflow = Yaml::parseFile($root.'/.github/workflows/package-quality.yml');
    $postgresDatabaseStep = collect($workflow['jobs']['postgresql']['steps'] ?? [])
        ->firstWhere('name', 'Database contract tests');

    expect($selected['taxonomy']['migration_tests'])
        ->toContain('tests/Tenancy/TaxonomyTenancyConcurrencyTest.php')
        ->and($selected['translatable']['migration_tests'])
        ->toContain('tests/Tenancy/Integration/TranslationTenancyConcurrencyTest.php')
        ->and($selected['auth']['migration_tests'])
        ->toContain(
            'tests/Feature/InvitationDeliveryOutcomeConcurrencyTest.php',
            'tests/Feature/Tenancy/MembershipOwnerConcurrencyTest.php',
        )
        ->and($selected['media']['migration_tests'])
        ->toContain(
            'tests/Feature/MediaOwnerSlotDatabaseConcurrencyTest.php',
            'tests/Tenancy/Integration/MediaTenantImportConcurrencyTest.php',
            'tests/Tenancy/Integration/MediaTenantOwnerSlotDatabaseConcurrencyTest.php',
        )
        ->and($selected['mail-notifications']['migration_tests'])
        ->toContain(
            'tests/Concurrency/PostgreSqlQueuedFailureConcurrencyTest.php',
            'tests/Concurrency/PostgreSqlScheduledMailConcurrencyTest.php',
            'tests/MySqlConcurrency/MySqlQueuedFailureConcurrencyTest.php',
        )
        ->and($postgresDatabaseStep['env']['REDIS_HOST'] ?? null)
        ->toBe('127.0.0.1');
});

it('release-reviews the forward-only Comments document migration without changing it', function (): void {
    $root = dirname(__DIR__, 2);
    $catalog = require $root.'/tools/package-family.php';
    $contractPath = $root.'/'.$catalog['quality']['released_migrations_contract'];
    $contracts = json_decode(
        file_get_contents($contractPath),
        true,
        flags: JSON_THROW_ON_ERROR,
    );
    $relativePath = 'database/migrations/2026_08_28_000001_add_comment_documents.php';
    $migrationPath = $root.'/packages/nvl/comments/'.$relativePath;
    $process = new Process([PHP_BINARY, $root.'/tools/validate-package-family.php'], $root);
    $process->setTimeout(30);
    $process->run();

    expect($contracts['packages']['comments']['migrations'][$relativePath] ?? null)
        ->toBe(migrationContractChecksum($migrationPath))
        ->and($catalog['quality']['packages']['comments']['migration_tests'] ?? [])
        ->toContain('tests/Feature/CommentRichDocumentLifecycleTest.php')
        ->and($process->isSuccessful())->toBeTrue($process->getErrorOutput());
});

it('locks the selected Activity ownership migration outside the primary migration set', function (): void {
    $root = dirname(__DIR__, 2);
    $catalog = require $root.'/tools/package-family.php';
    $contractPath = $root.'/'.$catalog['quality']['released_migrations_contract'];
    $contracts = json_decode(
        file_get_contents($contractPath),
        true,
        flags: JSON_THROW_ON_ERROR,
    );
    $relativePath = 'database/tenancy-migrations/2026_09_16_160001_add_activity_ownership.php';
    $migrationPath = $root.'/packages/nvl/activity/'.$relativePath;

    expect($contracts['packages']['activity']['migrations'][$relativePath] ?? null)
        ->toBe(migrationContractChecksum($migrationPath))
        ->and($catalog['quality']['packages']['activity']['analysis_paths'] ?? [])
        ->toContain('database/tenancy-migrations')
        ->and($catalog['quality']['packages']['activity']['migration_tests'] ?? [])
        ->toContain('tests/Tenancy/ActivityTenantTest.php');
});
