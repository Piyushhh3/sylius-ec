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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\Request;

final class EnabledWithinProductAssociationExtensionSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider);
    }

    public function it_is_a_constraint_validator()
    {
        $this->shouldHaveType(QueryCollectionExtensionInterface::class);
        $this->shouldHaveType(QueryItemExtensionInterface::class);
    }

    function it_does_nothing_if_current_resource_is_not_a_product(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->shouldNotBeCalled();
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, TaxonInterface::class, new Get(name: Request::METHOD_GET));
    }

    function it_does_nothing_if_current_user_is_an_admin_user(
        SectionProviderInterface $sectionProvider,
        AdminApiSection $adminApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductInterface::class, new Get(name: Request::METHOD_GET));
    }

    function it_filters_products_by_available_associations(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        Expr $expr,
        Expr\Comparison $comparison,
        Andx $andx,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $queryNameGenerator->generateJoinAlias('association')->shouldBeCalled()->willReturn('association');
        $queryNameGenerator->generateJoinAlias('associatedProduct')->shouldBeCalled()->willReturn('associatedProduct');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->addSelect('o')->shouldBeCalled()->willReturn($queryBuilder);

        $queryBuilder->addSelect('association')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->leftJoin('o.associations', 'association')->shouldBeCalled()->willReturn($queryBuilder);

        $expr->andX(Argument::type(Expr\Comparison::class), Argument::type(Expr\Comparison::class))->willReturn($andx);
        $expr->eq('associatedProduct.enabled', 'true')->shouldBeCalled()->willReturn($comparison);
        $expr->eq('association.owner', 'o')->shouldBeCalled()->willReturn($comparison);
        $queryBuilder->expr()->willReturn($expr->getWrappedObject());
        $queryBuilder->leftJoin('association.associatedProducts', 'associatedProduct', 'WITH', Argument::type(Andx::class))->shouldBeCalled()->willReturn($queryBuilder);

        $queryBuilder->andWhere('o.associations IS EMPTY OR associatedProduct.id IS NOT NULL')->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductInterface::class, new Get(name: Request::METHOD_GET));
    }
}
