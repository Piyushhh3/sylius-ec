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

namespace Sylius\Behat\Page\Shop\ProductReview;

use Sylius\Behat\Page\Shop\Page;
use Webmozart\Assert\Assert;

class CreatePage extends Page implements CreatePageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_product_review_create';
    }

    public function titleReview(?string $title): void
    {
        $this->waitForElementUpdate('title');
        $this->getElement('title')->setValue($title);
    }

    public function setComment(?string $comment): void
    {
        $this->waitForElementUpdate('comment');
        $this->getElement('comment')->setValue($comment);
    }

    public function setAuthor(string $author): void
    {
        $this->waitForElementUpdate('author');
        $this->getElement('author')->setValue($author);
    }

    public function rateReview(int $rate): void
    {
        $this->waitForElementUpdate('rating');
        $this->getElement('rating_option', ['%value%' => $rate])->getParent()->click();
    }

    public function submitReview(): void
    {
        $this->waitForElementUpdate('add');
        $this->getElement('add')->press();
    }

    public function getRateValidationMessage(): string
    {
        return $this->getValidationMessageFor('rating');
    }

    public function getTitleValidationMessage(): string
    {
        return $this->getValidationMessageFor('title');
    }

    public function getCommentValidationMessage(): string
    {
        return $this->getValidationMessageFor('comment');
    }

    public function getAuthorValidationMessage(): string
    {
        return $this->getValidationMessageFor('author');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add' => '[data-test-add]',
            'author' => '[data-test-author-email]',
            'comment' => '[data-test-comment]',
            'rating' => '[data-test-rating]',
            'rating_option' => '[data-test-rating-option="%value%"]',
            'title' => '[data-test-title]',
        ]);
    }

    protected function getValidationMessageFor(string $element): string
    {
        $errorElement = $this->getElement($element)->getParent()->find('css', '[data-test-validation-error]');
        Assert::notNull($errorElement);

        return $errorElement->getText();
    }
}
