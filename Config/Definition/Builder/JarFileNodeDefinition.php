<?php

namespace Cs\LiquibaseBundle\Config\Definition\Builder;

use Cs\LiquibaseBundle\Config\Definition\JarFileNode;

/**
 * Configuration tree builder JAR file node definition class
 *
 * @author Cedric SOCQUET <cedric.socquet.pro@gmail.com>
 */
class JarFileNodeDefinition extends FileNodeDefinition
{
    /**
     * {@inheritdoc}
     */
    protected function instantiateNode()
    {
        return new JarFileNode($this->name, $this->parent);
    }

}