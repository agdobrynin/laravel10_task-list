## Task list

Демо проект - "Список задача".

- 🐘 Php 8.2 + Laravel 10
- 🦖 MariaDb
- 🐳 Docker (Docker compose) + Laravel Sail


### Сборка образов докера

Настроить переменные окружения (если требуется изменить их):

```shell
cp .env.example .env
```

Собрать docker контейнеры:

```shell
docker run --rm -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

на этом подготовка к работе с Laravel Sail закончена.

### Запуск проекта

Поднять docker контейнеры с помощью Laravel Sail
```shell
./vendor/bin/sail up -d
```

доступные команды по остановке или пересборке контейнеров можно узнать на странице
[Laravel Sail](https://laravel.com/docs/9.x/sail)
или выполните команду `./vendor/bin/sail` для получения краткой справки о доступных командах.


1.  Сгенерировать application key

    ```shell
    ./vendor/bin/sail artisan key:generate
    ```

2.  Выполнить миграции и заполнить таблицы тестовыми данными

    ```shell
    ./vendor/bin/sail artisan migrate --seed
    ```

### Доступные сайты в dev окружении

|                Host                | Назначение                                                   |
|:----------------------------------:|:-------------------------------------------------------------|
|          http://localhost          | сайт приложения                                              |
|       http://localhost:8080        | Adminer - интерфейс доступа к базе данных |
