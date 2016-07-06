<?php

namespace Cs\LiquibaseBundle\Config\Definition;

use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

/**
 * Configuration JAR file node class
 *
 * @author Cedric SOCQUET <cedric.socquet.pro@gmail.com>
 */
class JarFileNode extends FileNode
{
    /**
     * {@inheritdoc}
     */
    protected function validateType($value)
    {
        parent::validateType($value);

        $info = new \SplFileInfo(realpath($value));

        if ($info->getExtension() != 'jar') {
            $ex = new InvalidTypeException(sprintf(
                'Invalid value for path "%s", ".jar" extension expected but ".%s" given',
                $this->getPath(),
                $info->getExtension()
            ));

            if ($hint = $this->getInfo()) {
                $ex->addHint($hint);
            }

            $ex->setPath($this->getPath());

            throw $ex;
        }
    }
}