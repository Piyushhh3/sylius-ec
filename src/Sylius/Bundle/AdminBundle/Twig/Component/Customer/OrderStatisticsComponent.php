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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Customer;

use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProviderInterface;
use Sylius\Component\Core\Customer\Statistics\PerChannelCustomerStatistics;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class OrderStatisticsComponent
{
    use HookableLiveComponentTrait;
    use DefaultActionTrait;
    use TemplatePropTrait;

    #[LiveProp]
    public ?string $customerId = null;

    /**
     * @param CustomerRepositoryInterface<CustomerInterface> $customerRepository
     */
    public function __construct(
        protected readonly CustomerRepositoryInterface $customerRepository,
        protected readonly CustomerStatisticsProviderInterface $statisticsProvider,
    ) {
    }

    /**
     * @return PerChannelCustomerStatistics[]
     */
    #[ExposeInTemplate(name: 'statistics')]
    public function getStatistics(): array
    {
        $customer = $this->customerRepository->find($this->customerId);
        if (null === $customer) {
            return [];
        }

        return $this->statisticsProvider->getCustomerStatistics($customer)->getPerChannelsStatistics();
    }
}
