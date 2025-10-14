#!/bin/bash

# Скрипт для тестирования интеграции с 1C CommerceML
# Использование: ./test_commerce1c.sh [BASE_URL]

BASE_URL=${1:-"http://localhost:21080"}
USERNAME="admin"
PASSWORD="password123"

echo "🔄 Тестирование интеграции с 1C CommerceML"
echo "URL: $BASE_URL"
echo "======================================"

# Функция для извлечения session_id из ответа
extract_session_id() {
    echo "$1" | grep -o 'session_id=[^[:space:]]*' | cut -d'=' -f2
}

# 1. Тестирование авторизации
echo "1. 🔐 Тестирование авторизации..."
auth_response=$(curl -s -u "$USERNAME:$PASSWORD" "$BASE_URL/1c/?type=catalog&mode=checkauth")
echo "Ответ: $auth_response"

if [[ $auth_response == success* ]]; then
    echo "✅ Авторизация успешна"
    SESSION_ID=$(extract_session_id "$auth_response")
    echo "Session ID: $SESSION_ID"
else
    echo "❌ Ошибка авторизации"
    exit 1
fi

echo ""

# 2. Тестирование инициализации
echo "2. 🚀 Тестирование инициализации..."
init_response=$(curl -s "$BASE_URL/1c/?type=catalog&mode=init&session_id=$SESSION_ID")
echo "Ответ: $init_response"

if [[ $init_response == success* ]]; then
    echo "✅ Инициализация успешна"
else
    echo "❌ Ошибка инициализации"
    exit 1
fi

echo ""

# 3. Создание тестового XML каталога
echo "3. 📄 Создание тестового XML каталога..."
cat > /tmp/test_catalog.xml << 'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<КоммерческаяИнформация ВерсияСхемы="2.05">
    <Классификатор>
        <Ид>test-catalog</Ид>
        <Наименование>Тестовый каталог</Наименование>
        <Группы>
            <Группа>
                <Ид>test-cat-1</Ид>
                <Наименование>Тестовая категория</Наименование>
                <Описание>Описание тестовой категории</Описание>
            </Группа>
        </Группы>
    </Классификатор>
    <Каталог>
        <Ид>test-catalog-1</Ид>
        <ИдКлассификатора>test-catalog</ИдКлассификатора>
        <Наименование>Каталог</Наименование>
        <Товары>
            <Товар>
                <Ид>test-product-1</Ид>
                <Наименование>Тестовый товар</Наименование>
                <Описание>Описание тестового товара</Описание>
                <Артикул>TEST-001</Артикул>
                <Группы>
                    <Ид>test-cat-1</Ид>
                </Группы>
            </Товар>
        </Товары>
    </Каталог>
</КоммерческаяИнформация>
EOF

echo "✅ Тестовый XML каталог создан"

# 4. Загрузка каталога
echo "4. 📤 Загрузка каталога..."
file_response=$(curl -s -X POST \
    -H "Content-Type: application/xml; charset=utf-8" \
    --data-binary @/tmp/test_catalog.xml \
    "$BASE_URL/1c/?type=catalog&mode=file&filename=import0_1.xml&session_id=$SESSION_ID")

echo "Ответ: $file_response"

if [[ $file_response == success* ]]; then
    echo "✅ Файл каталога загружен"
else
    echo "❌ Ошибка загрузки каталога"
    exit 1
fi

echo ""

# 5. Импорт каталога
echo "5. 📊 Импорт каталога..."
import_response=$(curl -s "$BASE_URL/1c/?type=catalog&mode=import&filename=import0_1.xml&session_id=$SESSION_ID")
echo "Ответ: $import_response"

if [[ $import_response == success* ]]; then
    echo "✅ Каталог импортирован"
else
    echo "❌ Ошибка импорта каталога"
    exit 1
fi

echo ""

# 6. Создание тестового XML предложений
echo "6. 💰 Создание тестового XML предложений..."
cat > /tmp/test_offers.xml << 'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<КоммерческаяИнформация ВерсияСхемы="2.05">
    <ПакетПредложений>
        <Ид>test-offers</Ид>
        <Наименование>Тестовые предложения</Наименование>
        <ТипыЦен>
            <ТипЦены>
                <Ид>base-price</Ид>
                <Наименование>Розничная</Наименование>
                <Валюта>RUB</Валюта>
            </ТипЦены>
        </ТипыЦен>
        <Предложения>
            <Предложение>
                <Ид>test-product-1</Ид>
                <Количество>25</Количество>
                <Цены>
                    <Цена>
                        <ИдТипаЦены>base-price</ИдТипаЦены>
                        <ЦенаЗаЕдиницу>199.99</ЦенаЗаЕдиницу>
                        <Валюта>RUB</Валюта>
                    </Цена>
                </Цены>
            </Предложение>
        </Предложения>
    </ПакетПредложений>
</КоммерческаяИнформация>
EOF

echo "✅ Тестовый XML предложений создан"

# 7. Загрузка предложений
echo "7. 📤 Загрузка предложений..."
offers_file_response=$(curl -s -X POST \
    -H "Content-Type: application/xml; charset=utf-8" \
    --data-binary @/tmp/test_offers.xml \
    "$BASE_URL/1c/?type=catalog&mode=file&filename=offers0_1.xml&session_id=$SESSION_ID")

echo "Ответ: $offers_file_response"

if [[ $offers_file_response == success* ]]; then
    echo "✅ Файл предложений загружен"
else
    echo "❌ Ошибка загрузки предложений"
    exit 1
fi

echo ""

# 8. Импорт предложений
echo "8. 💾 Импорт предложений..."
offers_import_response=$(curl -s "$BASE_URL/1c/?type=catalog&mode=import&filename=offers0_1.xml&session_id=$SESSION_ID")
echo "Ответ: $offers_import_response"

if [[ $offers_import_response == success* ]]; then
    echo "✅ Предложения импортированы"
else
    echo "❌ Ошибка импорта предложений"
    exit 1
fi

echo ""

# Очистка временных файлов
rm -f /tmp/test_catalog.xml /tmp/test_offers.xml

echo "🎉 Все тесты выполнены успешно!"
echo "======================================"
echo "Сводка:"
echo "✅ Авторизация"
echo "✅ Инициализация"  
echo "✅ Загрузка каталога"
echo "✅ Импорт каталога"
echo "✅ Загрузка предложений"
echo "✅ Импорт предложений"
echo ""
echo "Интеграция с 1C CommerceML работает корректно! 🚀"
