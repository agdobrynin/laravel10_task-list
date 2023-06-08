## Task list

Демо проект - "Список задача".

- 🐘 Php 8.2 + Laravel 10
- 🦖 MariaDb
- 🐳 Docker + Laravel Sail

>Дописать как поднимать проект через Laravel Sail и начальная настройка проекта

## Установка зависимостей проекта через composer

Если на машине разработчика **не установлен** локально composer
то зависимости проекта можно установить так

```shell
docker run --rm --interactive --tty \
  -u "$(id -u):$(id -g)" \
  --volume $PWD:/app \
  composer install
```

⚠ если же на машине разработчика установлен **composer** и **php**
то для начала необходимо установить зависимости
проекта выполнив команду

```shell
composer install --ignore-platform-reqs --no-scripts
```

на этом подготовка к работе с Laravel Sail закончен.

### Запуск проекта
Поднять docker контейнеры с помощтю Laravel Sail
```shell
./vendor/bin/sail up -d
```
```shell
./vendor/bin/sail composer install
```
доступные команды по остановке или пересборке контейнеров можно узнать на странице
[Laravel Sail](https://laravel.com/docs/9.x/sail)
или выполните команду `./vendor/bin/sail` для получения краткой справки о доступных командах.

1.  Сгенерировать application key
    ```shell
    ./vendor/bin/sail artisan key:generate
    ```

2.  Выполинть миграции и заполинть таблицы тестовыми данными
    ```shell
    ./vendor/bin/sail artisan migrate --seed
    ```
