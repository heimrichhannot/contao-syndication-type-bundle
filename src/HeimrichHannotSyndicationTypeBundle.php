<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle;

use HeimrichHannot\SyndicationTypeBundle\DependencyInjection\Compiler\SyndicationTypePass;
use HeimrichHannot\SyndicationTypeBundle\DependencyInjection\SyndicationTypeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotSyndicationTypeBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new SyndicationTypeExtension();
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SyndicationTypePass());
    }
}
