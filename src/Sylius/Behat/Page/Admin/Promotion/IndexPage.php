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

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Promotion\Model\PromotionInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function getUsageNumber(PromotionInterface $promotion): int
    {
        $usage = $this->getPromotionFieldsWithHeader($promotion, 'usage');

        return (int) $usage->find('css', '[data-test-used]')->getText();
    }

    public function isAbleToManageCouponsFor(PromotionInterface $promotion): bool
    {
        $actions = $this->getPromotionFieldsWithHeader($promotion, 'actions');

        return $actions->hasLink('List coupons');
    }

    public function isCouponBasedFor(PromotionInterface $promotion): bool
    {
        $coupons = $this->getPromotionFieldsWithHeader($promotion, 'couponBased');
        $isCouponBased = $coupons->find('css', '[data-test-status-enabled]');

        return $isCouponBased !== null;
    }

    public function specifyFilterType(string $field, string $type): void
    {
        $this->getDocument()->fillField(sprintf('criteria_%s_type', $field), $type);
    }

    public function specifyFilterValue(string $field, string $value): void
    {
        $this->getDocument()->fillField(sprintf('criteria_%s_value', $field), $value);
    }

    public function chooseArchival(string $isArchival): void
    {
        $this->getElement('filter_archival')->selectOption($isArchival);
    }

    public function isArchivalFilterEnabled(): bool
    {
        return '1' === $this->getElement('filter_archival')->getValue();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_archival' => '#criteria_archival',
        ]);
    }

    protected function getPromotionFieldsWithHeader(PromotionInterface $promotion, string $header): NodeElement
    {
        return $this->getCellForResource($header, ['code' => $promotion->getCode()]);
    }
}
