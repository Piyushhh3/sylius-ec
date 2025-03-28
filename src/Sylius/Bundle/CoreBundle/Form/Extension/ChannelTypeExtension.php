<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryChoiceType;
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddBaseCurrencySubscriber;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\ChannelFormSubscriber;
use Sylius\Bundle\CoreBundle\Form\Type\ChannelPriceHistoryConfigType;
use Sylius\Bundle\CoreBundle\Form\Type\ShopBillingDataType;
use Sylius\Bundle\CoreBundle\Form\Type\TaxCalculationStrategyChoiceType;
use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonAutocompleteChoiceType;
use Sylius\Component\Core\Model\Scope;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('locales', LocaleChoiceType::class, [
                'label' => 'sylius.form.channel.locales',
                'required' => false,
                'multiple' => true,
            ])
            ->add('defaultLocale', LocaleChoiceType::class, [
                'label' => 'sylius.form.channel.locale_default',
                'required' => true,
                'placeholder' => null,
            ])
            ->add('currencies', CurrencyChoiceType::class, [
                'label' => 'sylius.form.channel.currencies',
                'required' => false,
                'multiple' => true,
            ])
            ->add('countries', CountryChoiceType::class, [
                'label' => 'sylius.form.channel.countries',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('defaultTaxZone', ZoneChoiceType::class, [
                'required' => false,
                'label' => 'sylius.form.channel.tax_zone_default',
                'zone_scope' => Scope::TAX,
            ])
            ->add('taxCalculationStrategy', TaxCalculationStrategyChoiceType::class, [
                'label' => 'sylius.form.channel.tax_calculation_strategy',
            ])
            ->add('contactEmail', EmailType::class, [
                'label' => 'sylius.form.channel.contact_email',
                'required' => false,
            ])
            ->add('contactPhoneNumber', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.channel.contact_phone_number',
            ])
            ->add('skippingShippingStepAllowed', CheckboxType::class, [
                'label' => 'sylius.form.channel.skipping_shipping_step_allowed',
                'required' => false,
            ])
            ->add('skippingPaymentStepAllowed', CheckboxType::class, [
                'label' => 'sylius.form.channel.skipping_payment_step_allowed',
                'required' => false,
            ])
            ->add('accountVerificationRequired', CheckboxType::class, [
                'label' => 'sylius.form.channel.account_verification_required',
                'required' => false,
            ])
            ->add('shippingAddressInCheckoutRequired', ChoiceType::class, [
                'label' => 'sylius.form.channel.shipping_address_in_checkout_required',
                'choices' => [
                    'sylius.ui.billing_address' => false,
                    'sylius.ui.shipping_address' => true,
                ],
                'placeholder' => false,
                'multiple' => false,
                'expanded' => true,
                'required' => false,
            ])
            ->add('shopBillingData', ShopBillingDataType::class, [
                'label' => 'sylius.form.channel.shop_billing_data',
            ])
            ->add('menuTaxon', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.form.channel.menu_taxon',
            ])
            ->add('channelPriceHistoryConfig', ChannelPriceHistoryConfigType::class, [
                'label' => false,
                'required' => false,
            ])
            ->addEventSubscriber(new AddBaseCurrencySubscriber())
            ->addEventSubscriber(new ChannelFormSubscriber())
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ChannelType::class];
    }
}
