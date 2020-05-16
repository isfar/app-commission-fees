<?php

declare(strict_types=1);

namespace Isfar\CommissionTask;

use Isfar\CommissionTask\DependencyInjection\Compiler\FileManagerPass;
use Isfar\CommissionTask\DependencyInjection\Compiler\TransactionMapperPass;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ServiceContainerFactory
{
    public function create(string $configPath): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader(
            $containerBuilder,
            new FileLocator($configPath)
        );

        $loader->load('services.yaml');

        $containerBuilder
            ->addCompilerPass(new FileManagerPass())
            ->addCompilerPass(new TransactionMapperPass())
            ->compile()
        ;

        return $containerBuilder;
    }
}
