<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Shop\Product;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class IndexPage extends SymfonyPage implements IndexPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_product_index';
    }

    public function countProductsItems(): int
    {
        $productsList = $this->getElement('products');

        $products = $productsList->findAll('css', '[data-test-product]');

        return count($products);
    }

    public function getFirstProductNameFromList(): string
    {
        $productsList = $this->getElement('products');

        return $productsList->find('css', '[data-test-product]:first-child [data-test-product-content] > a')->getText();
    }

    public function getLastProductNameFromList(): string
    {
        $productsList = $this->getElement('products');

        return $productsList->find('css', '[data-test-product]:last-child [data-test-product-content] > a')->getText();
    }

    public function search(string $name): void
    {
        $this->getDocument()->fillField('criteria_search_value', $name);
        $this->getDocument()->find('css', '[data-test-search]')->submit();
    }

    public function sort(string $orderNumber): void
    {
        $this->getDocument()->clickLink($orderNumber);
    }

    public function clearFilter(): void
    {
        $this->getElement('clear')->click();
    }

    public function isProductOnList(string $productName): bool
    {
        $element = $this->getDocument()->find('css', sprintf('[data-test-product-name="%s"]', $productName));

        return ($element !== null) ? true : false;
    }

    public function isEmpty(): bool
    {
        return false !== strpos($this->getDocument()->find('css', '[data-test-flash-message]')->getText(), 'There are no results to display');
    }

    public function getProductPrice(string $productName): string
    {
        $element = $this->getDocument()->find('css', sprintf('[data-test-product-name="%s"]', $productName));

        return $element->getParent()->find('css', '[data-test-product-price]')->getText();
    }

    public function isProductOnPageWithName(string $name): bool
    {
        return null !== $this->getDocument()->find('css', sprintf('[data-test-product-name="%s"]', $name));
    }

    public function hasProductsInOrder(array $productNames): bool
    {
        $productsList = $this->getElement('products');
        $products = $productsList->findAll('css', '[data-test-product-content] > [data-test-product-name]');

        foreach ($productNames as $key => $value) {
            if ($products[$key]->getText() !== $value) {
                return false;
            }
        }

        return true;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'clear' => '[data-test-clear]',
            'products' => '[data-test-products]',
        ]);
    }
}
