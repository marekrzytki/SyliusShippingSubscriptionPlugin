<?php

declare(strict_types=1);

namespace BitBag\SyliusShippingSubscriptionPlugin\Form\Extension;

use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductVariantTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subscriptionLength', NumberType::class, [
                'label' => 'bitbag_sylius_shipping_subscription.form.shipping.length',
                'required' => false,
            ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductVariantType::class];
    }
}
