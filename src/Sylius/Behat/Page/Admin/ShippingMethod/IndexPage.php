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

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Resource\Model\ResourceInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function chooseArchival(string $isArchival): void
    {
        if (!$this->areFiltersVisible()) {
            $this->toggleFilters();
        }

        $this->getElement('filter_archival')->selectOption($isArchival);
    }

    public function isArchivalFilterEnabled(): bool
    {
        $archival = $this->getDocument()->find('css', 'button:contains("Restore")');

        return null !== $archival;
    }

    public function archiveShippingMethod(string $name): void
    {
        $actions = $this->getActionsForResource(['name' => $name]);
        $archiveRestoreModal = $actions->find('css', '[data-test-modal="archive-restore"]');
        $archiveRestoreModal->find('css', '[data-test-trigger-button="Archive"]')->press();
        $archiveRestoreModal->find('css', '[data-test-confirm-button]')->press();
    }

    public function restoreShippingMethod(string $name): void
    {
        $actions = $this->getActionsForResource(['name' => $name]);
        $archiveRestoreModal = $actions->find('css', '[data-test-modal="archive-restore"]');
        $archiveRestoreModal->find('css', '[data-test-trigger-button="Restore"]')->press();
        $archiveRestoreModal->find('css', '[data-test-confirm-button]')->press();
    }

    public function isShippingMethodDisabled(ShippingMethodInterface $shippingMethod): bool
    {
        $this->open();

        return null !== $this->getRowFor($shippingMethod)->find('css', '[data-test-status-disabled]');
    }

    public function isShippingMethodEnabled(ShippingMethodInterface $shippingMethod): bool
    {
        $this->open();

        return null !== $this->getRowFor($shippingMethod)->find('css', '[data-test-status-enabled]');
    }

    protected function getRowFor(ResourceInterface $shippingMethod): NodeElement
    {
        return $this->getElement('row', ['%resourceId%' => $shippingMethod->getId()]);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_archival' => '#criteria_archival',
            'row' => '[data-test-row][data-test-resource-id="%resourceId%"]',
        ]);
    }
}
