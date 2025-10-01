<?php

namespace context\Cart\interfaces;

use context\Cart\models\Cart;

interface CartServiceInterface
{
    public function getCart(): Cart;
}
