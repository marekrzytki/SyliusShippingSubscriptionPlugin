<?php

declare(strict_types=1);

namespace BitBag\SyliusShippingSubscriptionPlugin\Repository;

use BitBag\SyliusShippingSubscriptionPlugin\Entity\ShippingSubscriptionInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderItemUnitInterface;

class ShippingSubscriptionRepository extends EntityRepository implements ShippingSubscriptionRepositoryInterface
{
    public function findOneByCode(string $code): ?ShippingSubscriptionInterface
    {
        /** @var ShippingSubscriptionInterface $subscription */
        $subscription = $this->findOneBy([
            'code' => $code,
        ]);

        return $subscription;
    }

    public function findOneByOrderItemUnit(OrderItemUnitInterface $orderItemUnit): ?ShippingSubscriptionInterface
    {
        /** @var ShippingSubscriptionInterface|null $subscription */
        $subscription = $this->findOneBy([
            'orderItemUnit' => $orderItemUnit,
        ]);

        return $subscription;
    }
}
