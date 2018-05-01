<?php

class Product
{
    public static function countProductLine($productId)
    {
      return OrderLine::find()->select('SUM(quantity) as quantity')
                              ->joinWith('order')
                              ->where("(order.status = '" . Order::STATUS_PENDING . "' OR order.status = '" . Order::STATUS_PROCESSING . "' OR order.status = '" . Order::STATUS_WAITING_ACCEPTANCE . "') AND order_line.product_id = $productId")
                              ->scalar();
    }

    public static function countShoppingCart($productId)
    {
      return BlockedStock::find()->select('SUM(quantity) as quantity')
                                 ->joinWith('shoppingCart')
                                 ->where("blocked_stock.product_id = $productId AND blocked_stock_date > '" . date('Y-m-d H:i:s') . "' AND (shopping_cart_id IS NULL OR shopping_cart.status = '" . ShoppingCart::STATUS_PENDING . "')")
                                 ->scalar();
    }

    public static function stockOrderLine($productId, $cacheDuration)
    {
      if($cache)
          return (OrderLine::getDb()->cache(function ($db) use ($productId) {  countProductLine($productId)  }, $cacheDuration));

      else
          return countProductLine($productId);
    }


    public static function stockShoppingCart($productId, $cacheDuration)
    {
      if($cache)
          return(BlockedStock::getDb()->cache(function ($db) use ($productId) { CountShoppingCart($productId) }, $cacheDuration));

      else
          return CountShoppingCart($productId);
    }


    public static function stock($productId, $quantityAvailable, $cache = false, $cacheDuration = 60, $securityStockConfig = null)
    {
      // Obtenemos el stock bloqueado por pedidos en curso
      $ordersQuantity = stockOrderLine($productId, $cacheDuration);
      // Obtenemos el stock bloqueado
      $blockedStockQuantity = stockShoppingCart($productId, $cacheDuration);
      // Calculamos las unidades disponibles
      if ($quantityAvailable >= 0) {
          $quantityAvailable = $quantityAvailable - @$ordersQuantity - @$blockedStockQuantity;
          if (!empty($securityStockConfig)) {
              $quantityAvailable = ShopChannel::applySecurityStockConfig($quantityAvailable, @$securityStockConfig->mode, @$securityStockConfig->quantity);
              }
          $quantityAvailable = $quantityAvailable > 0 ? $quantityAvailable : 0;
          }
      return $quantityAvailable;
    }

}
