<?php

namespace Cs\LiquibaseBundle\Config\Definition\Builder;

use Cs\LiquibaseBundle\Config\Definition\FileNode;
use Symfony\Component\Config\Definition\Builder\VariableNodeDefinition;

/**
 * Configuration tree builder file node definition class
 *
 * @author Cedric SOCQUET <cedric.socquet.pro@gmail.com>
 */
class FileNodeDefinition extends VariableNodeDefinition
{
    /**
     * {@inheritdoc}
     */
    protected function instantiateNode()
    {
        return new FileNode($this->name, $this->parent);
    }

}