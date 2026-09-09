# Development notes

## Runtime versions

| Component | Version |
| --- | --- |
| PHP | 8.5.10 (`>=8.5` in `composer.json`) |
| Symfony | 7.4.18 LTS (`7.4.*`) |
| MySQL | 8.4.11 (image `mysql:8.4`) |
| Doctrine ORM | 3.7.0 |
| Doctrine DBAL | 4.4.4 |
| Doctrine Bundle | 3.3.1 |
| Doctrine Migrations Bundle | 4.0.1 |
| Sonata Admin Bundle | 4.43.0 |
| Sonata Doctrine ORM Admin | 4.21.0 |
| Sonata Entity Audit | 1.23.1 |
| Sonata Intl Bundle | 3.3.0 |
| PHPUnit | 12.5.34 |
| Composer | 2.x (image `composer:2`) |
| Nginx | 1.27 (image `nginx:1.27-alpine`) |

## Docker architecture

Three services in `compose.yaml`:

1. **php** — `php:8.5-fpm-bookworm` plus project Dockerfile (`docker/php/Dockerfile`). Runs PHP-FPM, Composer and Symfony Console. Waits for MySQL healthcheck before start.
2. **nginx** — reverse proxy, document root `public/`, FastCGI to `php:9000`.
3. **mysql** — official MySQL 8.4 with healthcheck (`mysqladmin ping`).

PHP extensions in the image:

* bundled: PDO, json, dom, libxml, mbstring, opcache, xml
* installed: pdo_mysql, intl, zip, bcmath, gmp, sockets, pcntl, soap, xsl, gd, amqp

`extra_hosts` maps `mysql` and `php` to `host-gateway` and publishes `3306` / `9000`. Containers therefore reach MySQL and PHP-FPM through the published ports using the service hostnames required by the app (`mysql:3306`). Keep `MYSQL_PORT=3306` unless you also change `DATABASE_URL`.

Makefile targets: `up`, `down`, `build`, `bash`, `logs`, `composer-install`, `migrate`, `migrate-status`, `migrate-rollback`, `cache-clear`, `test`, `about`, `schema-validate`.

## Main bundles

Enabled in `config/bundles.php` because they are required by Composer packages or by the web stack:

* Framework, Twig, Security, Monolog, Maker (dev), Debug (dev), WebProfiler (dev/test)
* Doctrine + Migrations
* Sonata Admin, Doctrine ORM Admin, Entity Audit, Intl, plus Sonata Form/Block/Doctrine/Exporter/Twig
* Nelmio CORS, JMS Serializer, Knp Menu, Knp Paginator
* Webpack Encore, Stimulus, UX Turbo, Twig Extra
* Mobile Detect

Webpack Encore is installed and configured with `strict_mode: false`. Frontend assets are optional; the application does not require a compiled `public/build` to boot. Node/React/Vue are not added.

## Sonata Admin

* Dashboard: `/admin/dashboard` (`/admin` redirects there)
* User CRUD: `/admin/app/user/`
* Security: `sonata.admin.security.handler.noop` and `PUBLIC_ACCESS` on `^/admin`
* User entity is registered for entity audit (`User_audit` + `revisions`)

## User entity

`App\Entity\User` fields only: `id`, `createdAt`, `role`, `username`, `gender`.

* `createdAt` is set in the constructor and on `PrePersist`; it is not in the Sonata form
* `username` is unique
* Table name: `User`

## Composer compatibility adjustments

Original constraints were adapted so Composer can resolve a Symfony 7.4 + PHP 8.5 lockfile. Nothing was dropped from the requested package list.

### Doctrine DBAL `^3.0` → `^4.0`

`doctrine/doctrine-bundle` 3.3 (pulled by Symfony 7.4 Flex) requires `doctrine/dbal ^4.0`. DBAL 3 also currently matches a Packagist security advisory, so Composer 2.x blocks it unless advisories are ignored. Closest compatible constraint: `^4.0` (locked `4.4.4`).

ORM 3.7 accepts `^3.8.2 \|\| ^4`; with Bundle 3.x the 4.x line is the one that installs.

### Doctrine persistence `^3.2` → `^3.2 \|\| ^4.0`

Bundle 3.x needs persistence 4. The original `^3.2` cannot satisfy that. Dual constraint keeps 3.x allowed if a future downgrade path appears; lockfile uses 4.x.

### Doctrine Bundle / Migrations Bundle

Requested `doctrine/doctrine-bundle ^2.0` and `doctrine/doctrine-migrations-bundle ^3.0`. Symfony 7.4 `orm-pack` installs bundle `^3.3` and migrations bundle `^4.0`. These are the current supported combinations with ORM 3 + DBAL 4. Versions were not forced down to 2.x/3.x because that conflicts with the Flex 7.4 recipes and DBAL 4.

### PHPUnit `^13` (Flex test-pack) → `^12.5`

`codeception/codeception ^5.0` (kept in `require` as in the source list) does not resolve with PHPUnit 13: `codeception/stub` 4.3 + PHPUnit 13 conflict on `sebastian/diff`. PHPUnit 12.5 is the newest major that Codeception 5.3.5 installs cleanly. `symfony/phpunit-bridge` remains `7.4.*`.

PHPUnit lives in `require-dev` (not `require`); Codeception stays in `require` as requested.

### Unchanged but notable

* `arthurkushman/php-wss: >=1.3` — original unbound constraint; Composer warns, package installs (`2.1.0`).
* `doctrine/annotations` and `j7mbo/twitter-api-php` install but are abandoned upstream.
* `neutron/sphinxsearch-api`, `tarantool/*`, `mindbox/sdk`, `j7mbo/twitter-api-php` installed with requested constraints on PHP 8.5.
* `captainhook/captainhook-phar` plugin is listed in `allow-plugins` as `false` so install does not rewrite git hooks automatically.

### Symfony packages kept from the webapp skeleton

The source list did not name every Symfony component needed for a web+Sonata app (form, security, validator, translation, asset, …). Those are present via `symfony/framework-bundle` / Flex webapp and Sonata. Extra Flex packages that help the skeleton but were not in the source list: `symfony/asset-mapper`, `symfony/stimulus-bundle`, `symfony/ux-turbo`, `symfony/notifier`. They are compatible with 7.4 and do not replace Webpack Encore.

## Tests

`vendor/bin/phpunit` uses `tests/PhpUnit` only. Codeception suites under `tests/` are separate and are not run by PHPUnit, so empty/unused Cest folders do not fail the build.
