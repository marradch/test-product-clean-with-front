# 🛍️ Products

Содержит RESTful API для управления товарами, категориями и их атрибутами. Реализовано на PHP 8.3 с использованием PSR-7, DTO, Symfony валидации и PostgreSQL.
На главной странице находится каталог товаров с использованием Bootstrap-5 и jquery.

### 🐳 Запуск проекта

```bash
git clone git@github.com:marradch/test-product-clean.git
cd test-product-clean
docker-compose up -d --build
````

Приложение будет доступно по адресу:
**[http://localhost](http://localhost)**

## 🗃️ Миграции БД

Выполнить запрос из файла перед началом работы
```bash
/database/database.sql
```

## 🌐 API эндпоинты

### ➕ POST /products

Создание нового продукта.

```json
{
  "name": "iPhone 15",
  "price": 999.99,
  "category_id": 1,
  "status": "available",
  "attributes": {
    "color": "black",
    "storage": "128GB"
  }
}
```

### 📋 GET /products

Получить список всех продуктов, с возможностью фильтрации.

Пример:

```
GET /products?category_id=2&price_min=100&price_max=500
```

### 📦 GET /products/{id}

Получить продукт по ID.

```
GET /products/5
```

### ✏️ PATCH /products/{id}

Частичное обновление продукта.

```json
{
  "price": 899.99,
  "status": "archived"
}
```

### ❌ DELETE /products/{id}

Удаление продукта.

```
DELETE /products/5
```

## ⚙️ Переменные окружения

Файл `.env` должен содержать:

```
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_NAME=test_laravel
DB_USER=postgres
DB_PASS=123456
```

## 📁 Структура проекта

```
├── database/              # SQL-файлы для схемы
├── public/                # index.php – вход в приложение
├── src/
│   ├── Controller/        # Контроллеры API
│   ├── DTO/               # DTO-запросы и ответы
│   ├── Entity/            # Сущности (Product и др.)
│   ├── Repository/        # Работа с БД
│   └── Enum/              # Перечисления (ENUM)
├── tests/
│   ├── Integration/
│   └── Unit/
├── composer.json
├── docker-compose.yml
└── README.md
```

## 🧱 Стек технологий

* PHP 8.3
* PostgreSQL
* PSR-7 (Laminas Diactoros)
* Symfony Validator
* Composer
* Docker
* Bootstrap 5
* jquery

## ⚙️ Тестовое 2

Загрузить таблицу

```bash
/database/categories-2.sql
```

перейти по http://localhost/test2.php
