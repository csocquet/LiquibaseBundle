<?php

namespace Cs\LiquibaseBundle\DependencyInjection;

use Cs\LiquibaseBundle\Config\Definition\Builder\FileNodeDefinition;
use Cs\LiquibaseBundle\Config\Definition\Builder\JarFileNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Configuration class
 *
 * @author Cedric SOCQUET <cedric.socquet.pro@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /** @var NodeBuilder */
    private $nodeBuilder;

    /** @var array */
    private $supportedDb = [
        'mysql', 'sqlite', 'postgresql', 'oracle', 'mssql', 'sybase',
        'asany', 'db2', 'derby', 'hsqldb', 'h2', 'informix', 'firebird',
    ];

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
        $this->nodeBuilder = new NodeBuilder();
        $this->nodeBuilder->setNodeClass('file', FileNodeDefinition::class);
        $this->nodeBuilder->setNodeClass('jar_file', JarFileNodeDefinition::class);
    }

    /** @return NodeBuilder */
    protected function getNodeBuilder()
    {
        return $this->nodeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder  = new TreeBuilder();
        $rootNode = $builder->root('cs_liquibase', 'array', $this->getNodeBuilder());

        $rootNode
            ->children()
                ->node('liquibase_jar_path', 'jar_file')
                    ->info('The file path to liquibase JAR archive')
                    ->defaultValue(realpath(__DIR__ . '/../Resources/liquibase/liquibase.jar'))
                ->end()
                ->node('changelog_path', 'file')
                    ->info('')
                    ->defaultValue('%kernel.root_dir%/db/changelog/db.changelog-master.xml')
                ->end()
            ->end()
            ->fixXmlConfig('driver', 'drivers')
            ->append($this->getDatabaseConfigTreeDefinition())
            ->append($this->getDriversTreeDefinition());
        
        return $builder;
    }

    /**
     * Generate database configuration tree builder
     *
     * @return ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function getDatabaseConfigTreeDefinition()
    {
        $builder  = new TreeBuilder();
        $rootNode = $builder->root('database', 'array', $this->getNodeBuilder());

        $rootNode
            ->isRequired()
            ->addDefaultsIfNotSet()
            ->children()
                ->enumNode('type')
                    ->isRequired()
                    ->values($this->supportedDb)
                ->end()
                ->scalarNode('jdbc_dsn')
                    ->isRequired()
                ->end()
                ->scalarNode('user')
                    ->defaultNull()
                ->end()
                ->scalarNode('password')
                    ->defaultNull()
                ->end()
            ->end();

        return $rootNode;
    }

    /**
     * Generate drivers configuration tree builder
     *
     * @return ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function getDriversTreeDefinition()
    {
        $builder  = new TreeBuilder();
        $rootNode = $builder->root('drivers', 'array', $this->getNodeBuilder());

        $rewriteDriversArrayFn = function (array $drivers) {
            $newDriversArray = [];
            foreach ($drivers as $key => $driver) {
                if (is_string($key) && !empty($key) && !isset($driver['db_type'])) {
                    $driver['db_type'] = $key;
                }
                $newDriversArray[] = $driver;
            }
            return $newDriversArray;
        };

        $rootNode
            ->beforeNormalization()
                ->always($rewriteDriversArrayFn)
            ->end()
            ->prototype('array')
                ->children()
                    ->enumNode('db_type')
                        ->values($this->supportedDb)
                        ->isRequired()
                    ->end()
                    ->scalarNode('class')
                        ->isRequired()
                    ->end()
                    ->node('path', 'jar_file')
                        ->isRequired()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }
}