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

namespace spec\Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Attribute\ShipmentIdAware;
use Sylius\Bundle\ApiBundle\Command\ShipmentIdAwareInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\HttpFoundation\Request;

final class ObjectUriVariablesAwareContextBuilderSpec extends ObjectBehavior
{
    function let(
        SerializerContextBuilderInterface $decoratedContextBuilder,
    ): void {
        $this->beConstructedWith(
            $decoratedContextBuilder,
            ShipmentIdAware::class,
            'shipmentId',
            ShipmentIdAwareInterface::class,
            ShipmentInterface::class,
        );
    }

    function it_does_nothing_if_there_is_no_input_class(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn([])
        ;

        $this->createFromRequest($request, true, [])->shouldReturn([]);
    }

    function it_does_nothing_if_input_class_is_no_supported_attribute(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => \stdClass::class]])
        ;

        $this
            ->createFromRequest($request, true, [])
            ->shouldReturn(['input' => ['class' => \stdClass::class]])
        ;
    }

    function it_does_nothing_if_there_is_no_uri_variable(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
        HttpOperation $operation,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, ['operation' => $operation])
            ->willReturn(['input' => ['class' => ShipmentIdAwareInterface::class]])
        ;
        $operation->getUriVariables()->willReturn([]);

        $this
            ->createFromRequest($request, true, ['operation' => $operation])
            ->shouldReturn(['input' => ['class' => ShipmentIdAwareInterface::class]])
        ;
    }

    function it_does_nothing_if_there_is_different_uri_variable(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
        HttpOperation $operation,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, ['operation' => $operation])
            ->willReturn(['input' => ['class' => ShipmentIdAwareInterface::class]])
        ;
        $uriVariable = new Link(fromClass: 'stdClass');
        $operation->getUriVariables()->willReturn([$uriVariable]);

        $this
            ->createFromRequest($request, true, ['operation' => $operation])
            ->shouldReturn(['input' => ['class' => ShipmentIdAwareInterface::class]])
        ;
    }

    function it_set_shipment_id_as_a_constructor_argument(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
        HttpOperation $operation,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, ['operation' => $operation])
            ->willReturn(['input' => ['class' => ShipmentIdAwareInterface::class], 'uri_variables' => ['shipmentId' => '123']])
        ;
        $uriVariable = new Link(fromClass: ShipmentInterface::class, parameterName: 'shipmentId');
        $operation->getUriVariables()->willReturn([$uriVariable]);

        $this
            ->createFromRequest($request, true, ['operation' => $operation])
            ->shouldReturn([
                'input' => ['class' => ShipmentIdAwareInterface::class],
                'uri_variables' => ['shipmentId' => '123'],
                'default_constructor_arguments' => [
                    ShipmentIdAwareInterface::class => ['shipmentId' => '123'],
                ],
            ])
        ;
    }
}
