<?php

namespace Cs\LiquibaseBundle\DependencyInjection;

use Cs\LiquibaseBundle\Config\Definition\Builder\FileNodeDefinition;
use Cs\LiquibaseBundle\Config\Definition\Builder\JarFileNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /** @var NodeBuilder */
    private $nodeBuilder;

    /** @var array */
    private $bundleDrivers = [
        [ 'name' => 'mysql', 'class' => 'com.mysql.jdbc.Driver', 'jar' => 'mysql-connector-java-5.1.39-bin.jar' ],
        [ 'name' => 'postgresql', 'class' => null, 'jar' => null ],
        [ 'name' => 'oracle', 'class' => null, 'jar' => null ],
        [ 'name' => 'mssql', 'class' => null, 'jar' => null ],
        [ 'name' => 'sybase', 'class' => null, 'jar' => null ],
        [ 'name' => 'asany', 'class' => null, 'jar' => null ],
        [ 'name' => 'db2', 'class' => null, 'jar' => null ],
        [ 'name' => 'derby', 'class' => null, 'jar' => null ],
        [ 'name' => 'hsqldb', 'class' => null, 'jar' => null ],
        [ 'name' => 'h2', 'class' => null, 'jar' => null ],
        [ 'name' => 'informix', 'class' => null, 'jar' => null ],
        [ 'name' => 'firebird', 'class' => null, 'jar' => null ],
        [ 'name' => 'sqlite', 'class' => null, 'jar' => null ],
    ];

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
                ->append($this->getDatabaseConfigTreeDefinition())
                ->append($this->getDriversTreeDefinition());
        
        return $builder;
    }

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
                    ->values(array_map(
                        function (array $driver) { return $driver['name']; },
                        $this->bundleDrivers
                    ))
                ->end()
                ->scalarNode('host')
                    ->defaultValue('localhost')
                ->end()
                ->scalarNode('port')
                    ->defaultNull()
                ->end()
                ->scalarNode('name')
                    ->isRequired()
                ->end()
                ->scalarNode('user')
                    ->isRequired()
                ->end()
                ->scalarNode('password')
                    ->isRequired()
                ->end()
            ->end();

        return $rootNode;
    }

    protected function getDriversTreeDefinition()
    {
        $builder  = new TreeBuilder();
        $rootNode = $builder->root('drivers', 'array', $this->getNodeBuilder());

        $rootNode->addDefaultsIfNotSet();

        foreach ($this->bundleDrivers as $driver) {
            $rootNode->append($this->getDriverNode(
                $driver['name'],
                $driver['class'],
                $driver['jar'] ? realpath(__DIR__ . '/../Resources/liquibase/drivers/' . $driver['jar']) : null
            ));
        }

        return $rootNode;
    }

    protected function getDriverNode($name, $class = null, $path = null)
    {
        $builder  = new TreeBuilder();
        $rootNode = $builder->root($name, 'array', $this->getNodeBuilder());

        if ($class !== null && $path !== null) {
            $rootNode->addDefaultsIfNotSet();
        }
        $rootNode
            ->children()
                ->scalarNode('class')
                    ->isRequired()
                    ->defaultValue($class)
                ->end()
                ->node('path', 'jar_file')
                    ->isRequired()
                    ->defaultValue($path)
                ->end()
            ->end();

        return $rootNode;
    }
}