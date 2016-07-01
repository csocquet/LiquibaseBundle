<?php

namespace Cs\LiquibaseBundle\Config\Definition\Builder;

use Cs\LiquibaseBundle\Config\Definition\JarFileNode;

class JarFileNodeDefinition extends FileNodeDefinition
{
    protected function instantiateNode()
    {
        return new JarFileNode($this->name, $this->parent);
    }

}