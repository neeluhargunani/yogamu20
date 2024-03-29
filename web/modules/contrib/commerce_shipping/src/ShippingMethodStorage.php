<?php

namespace Drupal\commerce_shipping;

use Drupal\commerce\CommerceContentEntityStorage;
use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Drupal\commerce_shipping\Event\FilterShippingMethodsEvent;
use Drupal\commerce_shipping\Event\ShippingEvents;

/**
 * Defines the shipping method storage.
 */
class ShippingMethodStorage extends CommerceContentEntityStorage implements ShippingMethodStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadMultipleForShipment(ShipmentInterface $shipment) {
    $query = $this->getQuery();
    $query->accessCheck();
    $store_condition = $query->orConditionGroup()
      ->notExists('stores')
      ->condition('stores', [$shipment->getOrder()->getStoreId()], 'IN');
    $query
      ->condition($store_condition)
      ->condition('status', TRUE);
    $result = $query->execute();
    if (empty($result)) {
      return [];
    }

    $shipping_methods = $this->loadMultiple($result);
    // Allow the list of shipping methods to be filtered via code.
    $event = new FilterShippingMethodsEvent($shipping_methods, $shipment);
    $this->eventDispatcher->dispatch($event, ShippingEvents::FILTER_SHIPPING_METHODS);
    $shipping_methods = $event->getShippingMethods();
    // Evaluate conditions for the remaining ones.
    foreach ($shipping_methods as $shipping_method_id => $shipping_method) {
      if (!$shipping_method->applies($shipment)) {
        unset($shipping_methods[$shipping_method_id]);
      }
    }
    uasort($shipping_methods, [$this->entityType->getClass(), 'sort']);

    return $shipping_methods;
  }

}
