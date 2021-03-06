<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\ResourceRepositoryBundle\Form\Type;

use FSi\Bundle\DemoBundle\Entity\Resource;
use FSi\Bundle\ResourceRepositoryBundle\Exception\ResourceFormTypeException;
use FSi\Bundle\ResourceRepositoryBundle\Form\Type\ResourceType;
use FSi\Bundle\ResourceRepositoryBundle\Repository\MapBuilder;
use FSi\Bundle\ResourceRepositoryBundle\Repository\Resource\Type\TextType;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceTypeSpec extends ObjectBehavior
{
    function let(MapBuilder $map)
    {
        $this->beConstructedWith($map, Resource::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceType::class);
    }

    function it_is_form_type()
    {
        $this->shouldBeAnInstanceOf(AbstractType::class);
    }

    function it_is_called_resource()
    {
        $this->getName()->shouldReturn('resource');
    }

    function it_configures_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Resource::class])->shouldBeCalled();
        $resolver->setRequired(['resource_key'])->shouldBeCalled();
        $this->configureOptions($resolver);
    }

    function it_should_throw_exception_during_build_form_when_resource_key_is_invalid(MapBuilder $map, FormBuilder $builder)
    {
        $map->hasResource('resources.invalid_resource')->willReturn(false);

        $this->shouldThrow(
            new ResourceFormTypeException('"resources.invalid_resource" is not a valid resource key')
        )->duringBuildForm($builder, ['resource_key' => 'resources.invalid_resource']);
    }

    function it_add_form_builder_specified_by_resource_definition(
        MapBuilder $map,
        FormBuilder $builder,
        TextType $resource,
        FormFactory $factory,
        FormBuilder $textBuilder
    ) {
        $map->hasResource('resources.resource_text')->willReturn(true);
        $map->getResource('resources.resource_text')->shouldBeCalled()->willReturn($resource);
        $builder->getFormFactory()->willReturn($factory);
        $resource->getFormBuilder($factory)->shouldBeCalled()->willReturn($textBuilder);
        $builder->add($textBuilder)->shouldBeCalled();
        $this->buildForm($builder, ['resource_key' => 'resources.resource_text']);
    }
}
