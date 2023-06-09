## Task list

Демо проект - "Список задача".

- 🐘 Php 8.2 + Laravel 10
- 🦖 MariaDb
- 🐳 Docker (Docker compose) + Laravel Sail


## Сборка образов докера

Настроить переменные окружения (если требуется изменить их):

```shell
cp .env.example .env
```

Собрать контейнеры docker через docker-composer:

```shell
docker-compose build
```

установить php зависимости проекта:


```shell
docker-compose run --rm laravel.test sh -c "composer install"
```

на этом подготовка к работе с Docker и Laravel Sail закончен.

## Запуск проекта

Поднять docker контейнеры с помощтю Laravel Sail

```shell
./vendor/bin/sail up -d
```

```shell
./vendor/bin/sail composer install
```

доступные команды по остановке или сборке контейнеров можно узнать на странице [Laravel Sail](https://laravel.com/docs/9.x/sail)
или выполните команду 

```shell
./vendor/bin/sail
```

для получения краткой справки о доступных командах.

1.  Сгенерировать application key

    ```shell
    ./vendor/bin/sail artisan key:generate
    ```

2.  Выполнить миграции и заполнить таблицы тестовыми данными

    ```shell
    ./vendor/bin/sail artisan migrate --seed
    ```
