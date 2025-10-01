<?php

namespace context\Cart\models;

use repositories\Product\models\Product;
use yii\base\Component;
use yii\web\Session;

class Cart extends Component
{
    private $items;
    private $session;

    public function __construct(Session $session, $config = [])
    {
        $this->session = $session;
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        $this->loadItems();
    }

    public function add(Product $product, $quantity): void
    {
        if (isset($this->items[$product->id])) {
            $this->items[$product->id]->plus($quantity);
        } else {
            $this->items[$product->id] = new CartItem($product, $quantity);
        }
        $this->saveItems();
    }

    public function set($productId, $quantity): void
    {
        if (isset($this->items[$productId])) {
            $this->items[$productId]->changeQuantity($quantity);
            if ($this->items[$productId]->getQuantity() <= 0) {
                unset($this->items[$productId]);
            }
            $this->saveItems();
        }
    }

    public function remove($productId): void
    {
        if (array_key_exists($productId, $this->items)) {
            unset($this->items[$productId]);
            $this->saveItems();
        }
    }

    public function clear(): void
    {
        $this->items = [];
        $this->saveItems();
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getAmount(): int
    {
        return count($this->items);
    }

    public function getTotalCost(): float
    {
        $cost = 0;
        foreach ($this->items as $item) {
            $cost += $item->getCost();
        }
        return $cost;
    }

    private function loadItems(): void
    {
        $this->items = $this->session->get('cart', []);
    }

    private function saveItems(): void
    {
        $this->session->set('cart', $this->items);
    }
}
