<?php

namespace context\Commerce1C\interfaces;

use repositories\Commerce1C\models\CommerceRequest;
use repositories\Commerce1C\models\CommerceResponse;

interface CommerceProcessorInterface
{
    /**
     * Обрабатывает входящий запрос CommerceML
     */
    public function processRequest(CommerceRequest $request): CommerceResponse;
}
