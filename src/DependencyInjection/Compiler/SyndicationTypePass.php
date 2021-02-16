<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\DependencyInjection\Compiler;

use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SyndicationTypePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(SyndicationTypeCollection::class)) {
            return;
        }

        $definition = $container->findDefinition(SyndicationTypeCollection::class);

        $taggedServices = $container->findTaggedServiceIds('huh.syndication_type.type');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addType', [new Reference($id)]);
        }
    }
}
