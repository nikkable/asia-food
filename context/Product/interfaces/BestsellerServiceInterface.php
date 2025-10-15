<?php

namespace context\Product\interfaces;

/**
 * Интерфейс сервиса для работы с хитами продаж
 */
interface BestsellerServiceInterface
{
    public function getBestsellers(int $limit = 20): array;
}
