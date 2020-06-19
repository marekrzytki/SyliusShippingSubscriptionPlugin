<?php

declare(strict_types=1);

namespace BitBag\SyliusShippingSubscriptionPlugin\Operator;

use BitBag\SyliusShippingSubscriptionPlugin\Factory\ShippingSubscriptionFactory;
use BitBag\SyliusShippingSubscriptionPlugin\Model\ShippingSubscriptionInterface;
use BitBag\SyliusShippingSubscriptionPlugin\Repository\ShippingSubscriptionRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Setono\SyliusGiftCardPlugin\Model\ProductInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Webmozart\Assert\Assert;

class OrderShippingSubscriptionOperator
{
    /** @var ShippingSubscriptionFactory */
    private $shippingSubscriptionFactory;

    /** @var ObjectManager */
    private $manager;

    /** @var ShippingSubscriptionRepositoryInterface */
    private $shippingSubscriptionRepository;

    public function __construct(
        ShippingSubscriptionFactory $shippingSubscriptionFactory,
        ShippingSubscriptionRepositoryInterface $shippingSubscriptionRepository,
        ObjectManager $manager
    ) {
        $this->shippingSubscriptionFactory = $shippingSubscriptionFactory;
        $this->manager = $manager;
        $this->shippingSubscriptionRepository = $shippingSubscriptionRepository;
    }

    public function create(OrderInterface $order): void
    {
        $items = self::getOrderItemsThatAreShippingSubscriptions($order);

        if (count($items) === 0) {
            return;
        }

        foreach ($items as $item) {
            /** @var OrderItemUnitInterface $unit */
            foreach ($item->getUnits() as $unit) {
                $shippingSubscription = $this->shippingSubscriptionFactory->createFromOrderItemUnit($unit);

                $this->manager->persist($shippingSubscription);
            }
        }

        $this->manager->flush();
    }

    public function enable(OrderInterface $order): void
    {
        $shippingSubscriptions = $this->getShippingSubscriptions($order);

        if (count($shippingSubscriptions) === 0) {
            return;
        }

        foreach ($shippingSubscriptions as $shippingSubscription) {
            $shippingSubscription->enable();
        }

        $this->manager->flush();
    }

    public function disable(OrderInterface $order): void
    {
        $shippingSubscriptions = $this->getShippingSubscriptions($order);

        if (count($shippingSubscriptions) === 0) {
            return;
        }

        foreach ($shippingSubscriptions as $shippingSubscription) {
            $shippingSubscription->disable();
        }

        $this->manager->flush();
    }

    /**
     * @return Collection|OrderItemInterface[]
     */
    private static function getOrderItemsThatAreShippingSubscriptions(OrderInterface $order): Collection
    {
        return $order->getItems()->filter(static function (OrderItemInterface $item): bool {
            /** @var ProductInterface|null $product */
            $product = $item->getProduct();

            Assert::isInstanceOf($product, ProductInterface::class);

            return $product->isShippingSubscription();
        });
    }

    /**
     * @return ShippingSubscriptionInterface[]
     */
    private function getShippingSubscriptions(OrderInterface $order): array
    {
        $shippingSubscriptions = [];

        $items = self::getOrderItemsThatAreShippingSubscriptions($order);
        foreach ($items as $item) {
            /** @var OrderItemUnitInterface $unit */
            foreach ($item->getUnits() as $unit) {
                $subscription = $this->shippingSubscriptionRepository->findOneByOrderItemUnit($unit);
                if (null === $subscription) {
                    continue;
                }

                $shippingSubscriptions[] = $subscription;
            }
        }

        return $shippingSubscriptions;
    }
}
