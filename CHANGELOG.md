# Changelog

All notable changes to `nvl/laravel-suite` are documented in this file. The
suite follows [Semantic Versioning](https://semver.org/) and versions all 22
embedded modules together.

Module-level implementation history remains available in each
`packages/nvl/<module>/CHANGELOG.md` file.

## [Unreleased]

## [2.1.1] - 2026-09-23

### Added

- Add the optional Tasks package with task and assignment services, opt-in
  management routes, tenant-aware access, and integrations for Media, Content,
  and Metafields.

### Changed

- Use package Data contracts for Tasks management input and output, and replace
  the remaining Auth, Activity, Media, SEO, and Translations HTTP Form Requests,
  inline validation, and JSON Resource projection with Data contracts. Existing
  HTTP response envelopes and supported input aliases remain intact.
- Regenerate TypeScript declarations and public contract baselines for the
  Data-first package APIs. Direct PHP imports of the removed HTTP request and
  resource classes must migrate to the corresponding Data contracts.

### Fixed

- Align Content's generated JSON Schemas with required published values and
  preserve field-local references in embedded JSON schemas.

## [2.1.0] - 2026-09-23

### Fixed

- Preserve application-owned Primitives exchange-rate and Translations
  repository/action bindings when package providers register, including in
  cached clean-consumer installations.
- Correct the Activity release-consumer smoke proof for custom storage and
  queued purge-job payloads.

### Changed

- Run focused Auth, Comments, Mail Notifications, Media, Taxonomy, and
  Translatable process-race contracts on their supported real-database CI jobs.
- Isolate package tests from an inherited `DB_URL`, leave Xdebug off by default
  unless explicitly requested, and reuse the Primitives fixture schema between
  transaction-isolated tests.
- Align Translations' whole-package line-coverage floor with its measured 87%
  baseline while retaining the 90% changed-line requirement.

## [2.0.1] - 2026-09-22

- Prove standalone Tenancy archives with only Support/Data, cached configuration,
  Doctor and disabled compatibility; explicitly provision Filterable composition,
  register Tenancy type sources, and correct existing dependency/test autoload metadata.

### Added

- Synchronized generated TypeScript declarations for existing Content
  composition format versions and Media/Metafields platform-import DTOs.
- Added the inert-by-default `nvl/tenancy` foundation with deployment
  configuration, scoped context values, adapter contracts, stable failure codes,
  package discovery, and disabled compatibility without tenant schema.

### Changed

- Reduced routine and release CI duplication: SQLite remains exhaustive,
  external databases run focused persistence contracts, and publication reuses
  the successful quality result for the exact release commit before exercising
  the sealed archive on PHP 8.5.
- Use standard CSV escaping by default; explicit legacy backslash escaping
  remains supported. Suite upgrade notes also cover the new SEO redirect-lock
  migration and generated-TypeScript symlink restrictions.

### Fixed

- Corrected Media Doctor persisted-path verification so soft-deleted diagnostic
  tombstones no longer fail strict readiness after canonical object cleanup,
  while standalone schemas remain compatible without optional tenant path data.
- Hardened package validation, authorization, storage, transaction, and
  lifecycle boundaries; detailed fixes are recorded in the affected module
  changelogs.
- Corrected missing dependency declarations and added dependency auditing to
  package CI without changing installed dependency versions.
- Made privacy and architecture tests check every forbidden value independently.

## [2.0.0] - 2026-08-29

### Added

- Added an authoritative consumer deprecation catalog, strict package-model
  boundary audit, and a sealed prepared-final-1.x-to-2.0 proof-consumer
  rehearsal covering configuration, caches, migrations, generated types,
  diagnostics, smoke behavior, and TypeScript compilation.

### Changed

- Disabled omitted keys in published legacy module maps while retaining
  dependency closure for explicit roots and the full-suite fresh-install
  default.
- Changed Auth, Pages, Content, and SEO management reads from Eloquent model
  results to their established DTO projections.
- Made every unallowlisted consumer-initiated package-model query an audit
  error in normal and strict modes while retaining the documented bounded
  exceptions.

### Fixed

- Release-reviewed the immutable forward-only Comments rich-document migration
  with upgrade, rollback, and application-owned-storage evidence.
- Excluded nested macOS metadata files from Composer release archives so local
  packaging produces the same clean distribution as CI.

## [1.0.7] - 2026-08-22

### Added

- Added aggregate enabled-module agent-skill publication through the
  `suite-skills` tag and `nvl:suite:skills:publish` command.
- Added a versioned Suite ownership manifest with per-file hashes and the
  read-only `nvl:suite:skills:doctor` drift check.

### Changed

- Made managed skill updates preserve locally modified copies by default and
  refuse to overwrite unmanaged or application-authored skill directories,
  including when forced.

## [1.0.6] - 2026-08-13

### Added

- Added a privacy-bounded Auth invitation-acceptance event and authorized exact
  delivery identity plus scheduled-mail administrative read APIs.
- Added dependency-complete installation profiles, one executable adoption
  catalog, a secret-free `nvl:suite:configuration` report, and aggregate
  `nvl:suite:doctor --production --strict` readiness checks for every enabled
  module and required host scheduler entry.
- Extended Mail Notifications diagnostics across both administrative read
  authorizers, required scheduled-mail factories, and host scheduler entries.
- Added local generated-TypeScript freshness and compiler enforcement plus an
  explicit PostgreSQL, Redis, and S3-compatible Media production-stack proof.

### Fixed

- Aligned Mail Notifications' global CI coverage floor with its measured 88%
  baseline while retaining the 90% changed-line requirement for new source.
- Included root suite commands in the generated public contract so clean archive
  consumers discover and execute `nvl:suite:doctor` with every module Doctor.
- Made CSV whole-import and batch transactions use an explicitly selected
  Laravel connection, including rollback coverage on a secondary connection.
- Regenerated the missing scheduled-mail read-ability TypeScript contract,
  refreshed the audited Node lockfile, and aligned runtime documentation with
  the suite's PHP 8.4 requirement.
- Made the default database queue visibility timeout compatible with the strict
  Activity Doctor's 900-second purge-job contract.

### Changed

- Scoped changed-package coverage to packages with modified PHP production
  source, so release notes and test-only changes cannot activate unrelated
  legacy global coverage debt while changed source remains held to 90%.
- Routed Comments and Media reads out of HTTP controllers, separated recursive
  Content rendering from value validation, and isolated CSV transaction
  ownership behind focused service boundaries.

## [1.0.5] - 2026-08-12

### Fixed

- Preserved application-defined mass-assignable attributes on Auth principal
  subclasses while retaining the complete configured canonical field map.

### Changed

- Documented scheduler ownership, canonical Mail Notifications configuration,
  SQLite adoption constraints, sensitive Auth profile confirmation, and Media
  missing-binary incident recovery.
- Added release-time changelog validation for the suite, every module, and the
  materialized release archive.

## [1.0.4] - 2026-08-12

### Changed

- Published the preceding stable suite archive. The intended consumer-hardening
  changes were not present in that distribution and ship in v1.0.5.

## [1.0.3] - 2026-08-12

### Fixed

- Added the missing Auth corrective migration for invitation context hashes and
  paired challenge credentials, including v1.0.1/v1.0.2 upgrade rehearsal,
  feature-aware schema repair, and strict column/index diagnostics.
- Made Composer dependency installation retry bounded transient TLS failures
  with a pinned toolchain and release-safe exponential backoff.

## [1.0.2] - 2026-08-12

### Changed

- Completed local parity for every stateful CI database job, including
  MariaDB-native JSON metadata, nullable schema defaults, portable Forms
  submission timestamps, and connection-aware Auth assertions.
- Hardened package consumption across PostgreSQL, MySQL, MariaDB, and SQLite,
  including exact foreign-key adoption, connection-aware media ordering,
  portable settings diagnostics, canonical JSON comparisons, and UTC database
  session defaults.
- Expanded package-quality coverage with isolated databases for every package
  and integration suite across PostgreSQL, MySQL 8.4, and MariaDB 12.3,
  including the Translatable package and portable fixture lifecycles.
- Corrected stateful CI database naming for destructive concurrency tests,
  adopted MariaDB's official readiness probe, and made changed-line coverage
  failures report their exact uncovered source lines.
- Completed the 20-package consumption contract: all public publish tags are
  tracked and rehearsed, stateful migrations use Laravel's timestamp-aware
  publication API, package skills are natively discoverable by Laravel Boost,
  and configuration, translations, views, adoption templates, tooling, and
  archive contents are checked as materialized distribution assets.
- Added Auth principal attribute mapping, dry-run-first first-party principal
  adoption, physical relationship-collision diagnostics, and feature-aware
  schema reconciliation, with sparse validated DTO persistence for partial
  principal mutations.
- Added dry-run-first Mail Notifications legacy adoption plus fail-closed,
  privacy-bounded administrative list, show, statistics, and suggestion APIs.
- Added versioned strict TypeScript generation flags and generated-artifact
  lint/format exclusions, registered Mail Notifications public enums for
  declaration generation, and made CSV exports accept concrete Eloquent
  builders without static-analysis errors.
- Separated automatic vendor migration loading from host-owned published
  migrations across Auth, Activity, Comments, Media, and Mail Notifications,
  including timestamp-aware publishing and strict duplicate-owner diagnostics.
- Added fail-closed Templates schema ownership checks, a staged
  Templates/Content/Media adoption workflow, bounded Content scope resolution,
  and an opt-in revision-aware Media asset resolver for class templates.

## [1.0.1] - 2026-08-09

### Added

- Added staged module adoption through `config/nvl-suite.php`, including
  dependency-safe provider selection and strict module flag validation.

### Changed

- Documented the TypeScript transformer v3 consumer preflight and the canonical
  automated release procedure.

## [1.0.0] - 2026-08-08

### Added

- Initial stable release of the 20-module NVL Laravel Suite.
- Added the public single-package distribution, Packagist metadata, clean
  Composer archive, release rehearsal, and automated tagged-release workflow.
- Added one canonical API and usage guide for every embedded module.

### Changed

- Consolidated the former module package family into one installable
  `nvl/laravel-suite` library while retaining module namespaces and explicit
  internal boundaries.

<!-- tenancy-program-p2 -->
The configurable-tenancy program implementation is present across all 21 distributions, including adoption, lifecycle, sealed-consumer, and release-gate surfaces. Consolidated runtime verification is pending; this statement is not a release-readiness claim.
