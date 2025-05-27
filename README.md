# График отпусков

[![Build & Deploy](https://github.com/toatall/app-vacations/actions/workflows/deploy.yml/badge.svg?event=push)](https://github.com/toatall/app-vacations/actions/workflows/deploy.yml)

Приложение для демонстрации информации об отпусках.

Возможно переключение организации (в правом верхнем меню).

Главная страница представляет статистические данные об отпусках сотрудников.

Страница "Табель" показывает отпуска сгруппированные по отделам и месяцам. Каждую группу возможно развернуть для просмотра более детальной информации.

Страница "Поиск" помогает найти информацию по отпускам по сотруднику или по дате отпуска.

Страница "Организации" и позволяет управлять списком организаций (доступна только администраторам).

Страница "Журнал импорта отпусков" показывает историю результатов импорта данных об отпусках (доступна только администраторам).

![Главная](/screen_01.png)
![Табель](/screen_02.png)
![Поиск](/screen_03.png)

**Видео демонстрация**

<a href="https://vkvideo.ru/video_ext.php?oid=-166203809&id=456239017&hd=3&autoplay=1" target="_blank">Ссылка на VK Видео</a>


В качестве базового приложения использован https://github.com/tbreuss/pingcrm-yii2

## Установка через Docker (быстрая)

Скопировать репозиторий локально:

```sh
git clone https://github.com/toatall/app-vacations app-vacations
cd app-vacations
```

Установить composer, npm зависимости, скомпилировать assets:

```sh
composer install && npm ci && npm run css-dev && npm run dev
```

Запустить docker
```sh
docker-compose up
```

## Установка вручную

Скопировать репозиторий локально:

```sh
git clone https://github.com/toatall/app-vacations app-vacations
cd app-vacations
```

Установить composer зависимости:

```sh
composer install
```

Установить NPM зависимости:

```sh
npm ci
```

Создать базу данных, выполнить настройку подключения в файле `config/db.php`.

Запустить миграции:

```sh
php yii migrate
php yii migrate --migrationPath=@yii/rbac/migrations
```

Выполнить команды для наполнения БД (организации, пользователи, роли):

```sh
php yii db/seed
php yii roles/init
php yii roles/assign "admin" "admin"
```

Генерация случайных данных:


```sh
# Для windows:
php yii vacations/generate 100 8 runtime/vacations_0000.csv
php yii csv-loader runtime/vacations_0000.csv 0000 %date:~6,4% ";"
php yii vacations/generate 80 8 runtime/vacations_0001.csv
php yii csv-loader runtime/vacations_0001.csv 0001 %date:~6,4% ";"
php yii vacations/generate 70 8 runtime/vacations_0002.csv
php yii csv-loader runtime/vacations_0002.csv 0002 %date:~6,4% ";"
php yii vacations/generate 90 10 runtime/vacations_0003.csv
php yii csv-loader runtime/vacations_0003.csv 0003 %date:~6,4% ";"
del /q runtime/vacations.csv

# Для Linux:
php yii vacations/generate 100 8 runtime/vacations.csv;
php yii csv-loader runtime/vacations.csv 0000 $(date +%Y) ";";
php yii vacations/generate 80 8 runtime/vacations.csv;
php yii csv-loader runtime/vacations.csv 0001 $(date +%Y) ";";
php yii vacations/generate 70 8 runtime/vacations.csv;
php yii csv-loader runtime/vacations.csv 0002 $(date +%Y) ";";
php yii vacations/generate 90 10 runtime/vacations.csv;
php yii csv-loader runtime/vacations.csv 0003 $(date +%Y) ";";
rm runtime/vacations.csv;
```

Запустить npm:

```sh
npm run css-dev
npm run dev
```

Для разработки:
```sh
npm run watch
```

Запустить веб-сервер:

```sh
php yii serve
```

Все готово, перейти на страницу входа и ввести учетные данные:

- **Username:** admin@example.com
- **Password:** secret

## Запуск тестов

В файле `docker.yml` раскомментировать сервис `postgres-test`.

В файле `config/test_db.php` настроить подключение к БД.
```sh
...
$db['dsn'] = 'pgsql:host=postgres-test;port=5432;dbname=app-vacations-test';
...
```

Выполнить миграции:
```sh
php tests/bin/yii migrate --migrationPath=@yii/rbac/migrations --interactive=0;
php tests/bin/yii migrate --interactive=0;
```
или
```sh
cd tests
./migrates.sh
```

Для запуска тестирования выполнить команду:

```sh
vendor/bin/codecept run unit
```

## Требования

- `PHP >=7.4.0` <img src="https://upload.wikimedia.org/wikipedia/commons/2/27/PHP-logo.svg" height="20" /> <img src="https://www.yiiframework.com/image/design/logo/yii3_full_for_light.svg" height="20" />
- `Node.js & NPM` <img src="https://upload.wikimedia.org/wikipedia/commons/d/d9/Node.js_logo.svg" height="20" /> <img src="https://upload.wikimedia.org/wikipedia/commons/d/db/Npm-logo.svg" height="20" />
- `Postgres` <img src="https://upload.wikimedia.org/wikipedia/commons/2/29/Postgresql_elephant.svg" height="20" />

