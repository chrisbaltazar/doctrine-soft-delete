<?php

declare(strict_types=1);

namespace Database\SoftDelete\DependencyInjection;

use Database\SoftDelete\Doctrine\ORM\DoctrineSoftDeleteFilter;
use Database\SoftDelete\Doctrine\Schema\SoftDeleteSchemaManagerFactory;
use Database\SoftDelete\Doctrine\Schema\SoftDeleteSchemaProvider;
use Doctrine\Migrations\Provider\SchemaProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SoftDeleteExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        if (!$container->hasParameter('doctrine.dbal.schema_manager_factory')) {
            $container->setParameter(
                'doctrine.dbal.schema_manager_factory',
                SoftDeleteSchemaManagerFactory::class,
            );
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'filters' => [
                    'soft_delete' => [
                        'class' => DoctrineSoftDeleteFilter::class,
                        'enabled' => true,
                    ],
                ],
            ],
        ]);

        $container->prependExtensionConfig('doctrine_migrations', [
            'services' => [
                SchemaProvider::class => SoftDeleteSchemaProvider::class,
            ],
        ]);
    }
}