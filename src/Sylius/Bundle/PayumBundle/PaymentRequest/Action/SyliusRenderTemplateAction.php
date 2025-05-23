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

namespace Sylius\Bundle\PayumBundle\PaymentRequest\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\RenderTemplate;
use Sylius\Bundle\PayumBundle\PaymentRequest\Context\PaymentRequestContextInterface;

/** @experimental */
final class SyliusRenderTemplateAction implements ActionInterface
{
    public function __construct(private PaymentRequestContextInterface $payumApiContext)
    {
    }

    public function execute($request): void
    {
        /** @var RenderTemplate $request */
        RequestNotSupportedException::assertSupports($this, $request);

        $paymentRequest = $this->payumApiContext->getPaymentRequest();

        $details = $paymentRequest->getResponseData();
        $paymentRequest->setResponseData(array_merge($details, $request->getParameters()));

        $request->setResult('');
    }

    public function supports($request): bool
    {
        return $this->payumApiContext->isEnabled() && $request instanceof RenderTemplate;
    }
}
