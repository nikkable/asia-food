<?php

namespace backend\controllers;

use context\Commerce1C\interfaces\CommerceProcessorInterface;
use context\Commerce1C\models\CommerceRequest;
use context\Commerce1C\enums\CommerceTypeEnum;
use context\Commerce1C\enums\CommerceModeEnum;
use yii\web\Controller;
use yii\web\Response;
use Yii;

/**
 * Контроллер для тестирования CommerceML интеграции
 */
class TestCommerceController extends Controller
{
    private CommerceProcessorInterface $commerceProcessor;

    public function init()
    {
        parent::init();
        $this->commerceProcessor = Yii::$container->get(CommerceProcessorInterface::class);
    }

    /**
     * Тестирование авторизации
     */
    public function actionTestAuth()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Симулируем HTTP Basic Auth
        $_SERVER['PHP_AUTH_USER'] = 'admin';
        $_SERVER['PHP_AUTH_PW'] = 'password123';

        $request = new CommerceRequest(
            CommerceTypeEnum::CATALOG,
            CommerceModeEnum::CHECKAUTH
        );

        $response = $this->commerceProcessor->processRequest($request);

        return [
            'status' => $response->getStatus(),
            'message' => $response->getMessage(),
            'http_code' => $response->getHttpCode()
        ];
    }

    /**
     * Тестирование инициализации
     */
    public function actionTestInit()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Получаем session_id из предыдущего теста
        $sessionId = Yii::$app->request->get('session_id');
        $_GET['session_id'] = $sessionId;

        $request = new CommerceRequest(
            CommerceTypeEnum::CATALOG,
            CommerceModeEnum::INIT
        );

        $response = $this->commerceProcessor->processRequest($request);

        return [
            'status' => $response->getStatus(),
            'message' => $response->getMessage(),
            'http_code' => $response->getHttpCode()
        ];
    }

    /**
     * Показывает пример XML для каталога
     */
    public function actionSampleCatalogXml()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return '<?xml version="1.0" encoding="UTF-8"?>
<КоммерческаяИнформация ВерсияСхемы="2.05">
    <Классификатор>
        <Ид>commerce-catalog-example</Ид>
        <Наименование>Каталог продуктов</Наименование>
        <Группы>
            <Группа>
                <Ид>cat-asian-food</Ид>
                <Наименование>Азиатская кухня</Наименование>
                <Описание>Блюда азиатской кухни</Описание>
            </Группа>
            <Группа>
                <Ид>cat-sushi</Ид>
                <Наименование>Суши и роллы</Наименование>
                <Описание>Свежие суши и роллы</Описание>
            </Группа>
        </Группы>
    </Классификатор>
    <Каталог>
        <Ид>catalog-1</Ид>
        <ИдКлассификатора>commerce-catalog-example</ИдКлассификатора>
        <Наименование>Каталог</Наименование>
        <Товары>
            <Товар>
                <Ид>product-california-roll</Ид>
                <Наименование>Калифорния ролл</Наименование>
                <Описание>Классический ролл с крабом и авокадо</Описание>
                <Артикул>CAL-001</Артикул>
                <Группы>
                    <Ид>cat-sushi</Ид>
                </Группы>
            </Товар>
            <Товар>
                <Ид>product-pad-thai</Ид>
                <Наименование>Пад тай</Наименование>
                <Описание>Тайская рисовая лапша с креветками</Описание>
                <Артикул>PAD-001</Артикул>
                <Группы>
                    <Ид>cat-asian-food</Ид>
                </Группы>
            </Товар>
        </Товары>
    </Каталог>
</КоммерческаяИнформация>';
    }

    /**
     * Показывает пример XML для предложений
     */
    public function actionSampleOffersXml()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return '<?xml version="1.0" encoding="UTF-8"?>
<КоммерческаяИнформация ВерсияСхемы="2.05">
    <ПакетПредложений>
        <Ид>offers-1</Ид>
        <Наименование>Предложения</Наименование>
        <ТипыЦен>
            <ТипЦены>
                <Ид>base-price</Ид>
                <Наименование>Розничная</Наименование>
                <Валюта>RUB</Валюта>
            </ТипЦены>
        </ТипыЦен>
        <Предложения>
            <Предложение>
                <Ид>product-california-roll</Ид>
                <Количество>15</Количество>
                <Цены>
                    <Цена>
                        <ИдТипаЦены>base-price</ИдТипаЦены>
                        <ЦенаЗаЕдиницу>350.00</ЦенаЗаЕдиницу>
                        <Валюта>RUB</Валюта>
                    </Цена>
                </Цены>
            </Предложение>
            <Предложение>
                <Ид>product-pad-thai</Ид>
                <Количество>8</Количество>
                <Цены>
                    <Цена>
                        <ИдТипаЦены>base-price</ИдТипаЦены>
                        <ЦенаЗаЕдиницу>280.00</ЦенаЗаЕдиницу>
                        <Валюта>RUB</Валюта>
                    </Цена>
                </Цены>
            </Предложение>
        </Предложения>
    </ПакетПредложений>
</КоммерческаяИнформация>';
    }
}
