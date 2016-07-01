<?php

namespace Cs\LiquibaseBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class CsLiquibaseExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $conf = $this->processConfiguration(new Configuration(), $configs);
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }
}

