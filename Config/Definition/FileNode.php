<?php

namespace Cs\LiquibaseBundle\Config\Definition;

use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Config\Definition\VariableNode;

class FileNode extends VariableNode
{
    /**
     * {@inheritdoc}
     */
    protected function validateType($value)
    {
        $info = new \SplFileInfo(realpath($value));

        if (!$info->isFile()) {
            $ex = new InvalidTypeException(sprintf(
                'Invalid value for path "%s", "%s" is not a valid file path.',
                $this->getPath(),
                $value
            ));

            if ($hint = $this->getInfo()) {
                $ex->addHint($hint);
            }

            $ex->setPath($this->getPath());

            throw $ex;
        }
    }
}