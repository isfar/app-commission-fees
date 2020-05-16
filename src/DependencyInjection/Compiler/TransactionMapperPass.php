<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TransactionMapperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $transactionMapperId = 'transaction.transaction_mapper';

        if (!$container->has($transactionMapperId)) {
            return;
        }

        $definition = $container->findDefinition($transactionMapperId);

        $mappers = $container->findTaggedServiceIds('transaction.mapper');

        foreach ($mappers as $id => $tags) {
            $definition->addMethodCall(
                'addMapper',
                [
                    $tags[0]['type'],
                    new Reference($id),
                ]
            );
        }
    }
}
