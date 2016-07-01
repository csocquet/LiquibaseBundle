<?php

namespace Cs\LiquibaseBundle\Config\Definition\Builder;

use Cs\LiquibaseBundle\Config\Definition\FileNode;
use Symfony\Component\Config\Definition\Builder\VariableNodeDefinition;

class FileNodeDefinition extends VariableNodeDefinition
{
    protected function instantiateNode()
    {
        return new FileNode($this->name, $this->parent);
    }

}