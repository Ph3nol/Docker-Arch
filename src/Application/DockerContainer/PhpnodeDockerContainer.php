<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Alexis NIVON <anivon@alexisnivon.fr>
 */
class PhpnodeDockerContainer extends PhpDockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function execute(): void
    {
        parent::execute();

        $this
            ->addPackage('libffi-dev')
            ->addPackage('ruby-dev')
            ->addPackage('libc-dev')
            ->addPackage('ruby')
            ->addPackage('g++')
            ->addPackage('make')
            ->addPackage('nodejs');

        $this->addCommand('gem install sass compass --no-ri --no-rdoc');
    }
}
