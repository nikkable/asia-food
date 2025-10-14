# Интеграция с 1C CommerceML

## 📋 Описание

Система интеграции с 1С Предприятие по протоколу CommerceML 2.05 для автоматической синхронизации каталога товаров, остатков и цен.

## 🏗️ Архитектура

```
context/Commerce1C/          # Доменная логика
├── interfaces/             # Интерфейсы сервисов
├── services/              # Бизнес-логика
├── enums/                 # Перечисления
├── parsers/               # XML парсеры
├── config/                # Конфигурация
└── Bootstrap.php          # Регистрация в DI

repositories/Commerce1C/     # Модели и репозитории
├── interfaces/             # Интерфейсы репозиториев
├── models/                # Модели данных
└── Commerce1CSyncRepository.php

backend/controllers/         # Контроллеры
├── CommerceMLController.php    # Основной endpoint
└── TestCommerceController.php  # Тестирование
```

## 🚀 Развертывание

### 1. Выполнить миграции БД

```bash
cd /app
php yii migrate
```

Это добавит поля `external_id` в таблицы `category` и `product`.

### 2. Настройка авторизации

В файле `backend/config/bootstrap.php` настроить данные для авторизации:

```php
$config = new Commerce1CConfig(
    username: 'your_1c_username',
    password: 'your_1c_password',
    sessionTtlMinutes: 60,
    maxFileSize: 2097152, // 2MB
    version: '2.05'
);
```

### 3. Проверить доступность endpoint

URL для 1C: `http://your-domain/1c/`

## 🧪 Тестирование

### Тестовые URL

1. **Тестирование авторизации:**
   ```
   GET /test-commerce/test-auth
   ```

2. **Тестирование инициализации:**
   ```
   GET /test-commerce/test-init?session_id={session_id}
   ```

3. **Пример XML каталога:**
   ```
   GET /test-commerce/sample-catalog-xml
   ```

4. **Пример XML предложений:**
   ```
   GET /test-commerce/sample-offers-xml
   ```

### Полный цикл тестирования с curl

```bash
# 1. Авторизация
curl -X GET "http://localhost:21080/1c/?type=catalog&mode=checkauth" \
  -u "admin:password123"

# 2. Инициализация (использовать session_id из шага 1)
curl -X GET "http://localhost:21080/1c/?type=catalog&mode=init&session_id=SESSION_ID"

# 3. Загрузка каталога
curl -X POST "http://localhost:21080/1c/?type=catalog&mode=file&filename=import0_1.xml&session_id=SESSION_ID" \
  -H "Content-Type: application/xml" \
  -d @catalog.xml

# 4. Импорт каталога
curl -X GET "http://localhost:21080/1c/?type=catalog&mode=import&filename=import0_1.xml&session_id=SESSION_ID"
```

## 📊 Протокол CommerceML

### 1. Авторизация (checkauth)
- **URL:** `/?type=catalog&mode=checkauth`
- **Метод:** GET/POST
- **Auth:** HTTP Basic Authentication
- **Ответ:** `success\nsession_id=xxx\nversion=2.05`

### 2. Инициализация (init)
- **URL:** `/?type=catalog&mode=init&session_id=xxx`
- **Метод:** GET/POST
- **Ответ:** `success\nprogress\nsession_id=xxx`

### 3. Загрузка файлов (file)
- **URL:** `/?type=catalog&mode=file&filename=import0_1.xml&session_id=xxx`
- **Метод:** POST
- **Body:** XML содержимое файла
- **Ответ:** `success`

### 4. Импорт данных (import)
- **URL:** `/?type=catalog&mode=import&filename=import0_1.xml&session_id=xxx`
- **Метод:** GET/POST
- **Ответ:** `success`

## 📄 Поддерживаемые файлы

### import0_1.xml - Каталог товаров
```xml
<?xml version="1.0" encoding="UTF-8"?>
<КоммерческаяИнформация ВерсияСхемы="2.05">
    <Классификатор>
        <Группы>
            <Группа>
                <Ид>cat-1</Ид>
                <Наименование>Категория</Наименование>
            </Группа>
        </Группы>
    </Классификатор>
    <Каталог>
        <Товары>
            <Товар>
                <Ид>prod-1</Ид>
                <Наименование>Товар</Наименование>
                <Группы><Ид>cat-1</Ид></Группы>
            </Товар>
        </Товары>
    </Каталог>
</КоммерческаяИнформация>
```

### offers0_1.xml - Предложения
```xml
<?xml version="1.0" encoding="UTF-8"?>
<КоммерческаяИнформация ВерсияСхемы="2.05">
    <ПакетПредложений>
        <Предложения>
            <Предложение>
                <Ид>prod-1</Ид>
                <Количество>10</Количество>
                <Цены>
                    <Цена>
                        <ЦенаЗаЕдиницу>100.00</ЦенаЗаЕдиницу>
                        <Валюта>RUB</Валюта>
                    </Цена>
                </Цены>
            </Предложение>
        </Предложения>
    </ПакетПредложений>
</КоммерческаяИнформация>
```

## 🔧 Настройка 1С

В 1С Предприятие настроить обмен с сайтом:

1. **URL сайта:** `http://your-domain/1c/`
2. **Логин:** `admin` (или настроенный)
3. **Пароль:** `password123` (или настроенный)
4. **Использовать сжатие:** Нет
5. **Версия протокола:** 2.05

## ⚠️ Важные моменты

1. **CSRF отключен** для CommerceMLController
2. **Сессии** имеют TTL 60 минут по умолчанию
3. **Максимальный размер файла:** 2MB по умолчанию
4. **Логирование ошибок** в стандартный лог Yii2
5. **external_id** должно быть уникальным для товаров и категорий

## 🐛 Отладка

### Логи ошибок
```bash
tail -f /app/backend/runtime/logs/app.log
```

### Проверка статуса
```bash
# Проверить доступность endpoint
curl -I http://localhost:21080/1c/

# Проверить авторизацию
curl -u "admin:password123" http://localhost:21080/1c/?type=catalog&mode=checkauth
```

### Типичные ошибки

1. **Invalid credentials** - неверный логин/пароль
2. **Invalid session** - истекла сессия, нужна повторная авторизация
3. **File too large** - файл превышает лимит maxFileSize
4. **Invalid XML structure** - некорректная структура XML

## 📈 Мониторинг

Система логирует:
- Успешные авторизации
- Ошибки парсинга XML
- Количество импортированных товаров/категорий
- Ошибки синхронизации с БД

## 🔄 Расширение функционала

Для добавления новых возможностей:

1. **Новые типы данных:** Расширить XML парсеры
2. **Дополнительные поля:** Добавить в модели и миграции
3. **Новые протоколы:** Создать новые сервисы в context/
4. **Кастомная логика:** Переопределить методы в Commerce1CSyncRepository
