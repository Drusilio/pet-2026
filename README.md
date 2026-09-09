# Symfony Admin Starter

Локальное Symfony-приложение с MySQL и Sonata Admin. Запуск полностью через Docker.

## Требования

* Docker
* Docker Compose (плагин `docker compose` v2)

PHP, Composer, MySQL и Nginx устанавливать на хост не нужно: они входят в контейнеры.

## Запуск

Из корня репозитория:

```bash
docker compose build
docker compose up -d
```

Либо одной командой:

```bash
make up
```

Дождитесь, пока MySQL станет healthy (`docker compose ps`). PHP-контейнер стартует только после healthcheck MySQL.

## Установка зависимостей Composer

После первого запуска:

```bash
docker compose exec php composer install --no-interaction --prefer-dist
```

или:

```bash
make composer-install
```

`vendor/` в git не хранится. Команда нужна на чистой машине после клонирования репозитория.

## База данных

Контейнер MySQL создаёт базу `app` при старте (пользователь `app`, см. `.env`).

Если базу нужно создать вручную:

```bash
docker compose exec php php bin/console doctrine:database:create --if-not-exists
```

Подключение приложения идёт на сервис `mysql:3306`, не на `localhost`.

## Migration

```bash
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

или:

```bash
make migrate
```

Проверка статуса:

```bash
docker compose exec php php bin/console doctrine:migrations:status
make migrate-status
```

## Symfony

Приложение: [http://localhost:8080](http://localhost:8080)

Порт задаётся переменной `HTTP_PORT` в `.env` (по умолчанию `8080`).

## Sonata Admin

Админка: [http://localhost:8080/admin](http://localhost:8080/admin)

CRUD пользователей: [http://localhost:8080/admin/app/user/list](http://localhost:8080/admin/app/user/list)

Логин-форма на этом этапе не требуется: для `/admin` настроен `PUBLIC_ACCESS` и Sonata security handler `noop`. Полноценная авторизация с паролем не реализована намеренно (у сущности User нет поля password).

## Полезные команды

Вход в PHP-контейнер:

```bash
docker compose exec php bash
make bash
```

Логи:

```bash
docker compose logs -f
make logs
```

Очистка cache:

```bash
docker compose exec php php bin/console cache:clear
make cache-clear
```

Migration:

```bash
make migrate
```

Rollback последней migration:

```bash
docker compose exec php php bin/console doctrine:migrations:migrate prev --no-interaction
make migrate-rollback
```

Тесты:

```bash
docker compose exec php vendor/bin/phpunit
make test
```

Composer:

```bash
docker compose exec php composer install
docker compose exec php composer update
docker compose exec php composer validate --no-check-publish
```

Остановка:

```bash
docker compose down
make down
```

## Локальные секреты

Скопируйте `.env.example` в `.env.local` и измените пароли при необходимости. Файл `.env.local` не коммитится.

Значения в `.env` предназначены только для локальной разработки.
