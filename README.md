Дипломная работа по PHP нетология
=====================
###### Котюков Евгении 

#### ***Ссылки***
 * [Рабочий вариант](http://university.netology.ru/user_data/kotyukov/public/)
 * [Документация](https://docs.google.com/document/d/1gTYMyydpTCT1yFtbhDMr9WHZNE7R-U2D6vuReLN9xJY/edit?usp=sharing)
 * [Телеграм-бот](http://t.me/phpGraduateWorkBot)

### Установка

1. Скачать проект 
2. Распаковать в дерикторию сайта
3. Через терминал войти в дерикторию сайта и ввести команду пример для (ubuntu):
```bash
cd /path/to/site

composer update

sudo chmod -R 777 /path/to/site # для того что бы запустить сатй
```
4. Создать базу данных для сайта
5. В файле ./engine/Config/Database.php прописать данные для подключения к СУБД
```php
return [
    'host'         => 'localhost', // Ваш хост
    'charset'      => 'utf8',      // Кодировка, не обязательно
    'dbName'       => 'global',    // Имя БД которую создали
    'userName'     => 'root',      // Логин
    'userPassword' => 'root'       // Пароль
];
```
6. Загрузать дамп 'faq.sql' в базу данных
7. Настроить хостинг что бы грузил с дериктории path/to/site/public/, если не получится то доступ к сайту можно получить по этому пути [domen.ru/public/](#)
8. Войти на сайт через браузер, единная точка входа ./public/index.php
9. Войти в панель администратора и настроит сайт 
 * Доступ к панели администратора можно получить по ссылке [domen.ru/?/admin](#)
 * логин: admin 
 * пароль: admin

