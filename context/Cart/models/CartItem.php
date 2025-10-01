<?php

namespace context\Cart\models;

use repositories\Product\models\Product;

class CartItem
{
    private $product;
    private $quantity;

    public function __construct(Product $product, $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function getId(): int
    {
        return $this->product->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getPrice(): float
    {
        return $this->product->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getCost(): float
    {
        return $this->getPrice() * $this->quantity;
    }

    public function plus($quantity): void
    {
        $this->quantity += $quantity;
    }

    public function changeQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }
}
