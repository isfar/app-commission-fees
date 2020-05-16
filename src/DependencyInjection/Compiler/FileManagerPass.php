<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FileManagerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $fileManagerId = 'file.file_manager';

        if (!$container->has($fileManagerId)) {
            return;
        }

        $definition = $container->findDefinition($fileManagerId);

        $fileReaders = $container->findTaggedServiceIds('file.reader');

        foreach ($fileReaders as $id => $tags) {
            $definition->addMethodCall(
                'addFileReader',
                [
                    $tags[0]['type'],
                    new Reference($id),
                ]
            );
        }
    }
}
