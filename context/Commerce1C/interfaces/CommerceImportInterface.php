<?php

namespace context\Commerce1C\interfaces;

use repositories\Commerce1C\models\CommerceRequest;
use repositories\Commerce1C\models\CommerceResponse;

interface CommerceImportInterface
{
    /**
     * Обрабатывает инициализацию импорта
     */
    public function initialize(CommerceRequest $request): CommerceResponse;
    
    /**
     * Сохраняет файл в сессии
     */
    public function saveFile(CommerceRequest $request): CommerceResponse;
    
    /**
     * Импортирует каталог товаров
     */
    public function importCatalog(CommerceRequest $request): CommerceResponse;
    
    /**
     * Импортирует остатки и цены
     */
    public function importOffers(CommerceRequest $request): CommerceResponse;
}
