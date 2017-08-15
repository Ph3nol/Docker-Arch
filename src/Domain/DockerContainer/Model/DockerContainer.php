<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerBasicPropertiesTrait;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerPackagesTrait;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerCommandsTrait;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerEnvsTrait;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerPortsTrait;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerVolumesTrait;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerCopyEntriesTrait;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class DockerContainer implements DockerContainerInterface
{
    use DockerContainerBasicPropertiesTrait;
    use DockerContainerPackagesTrait;
    use DockerContainerCommandsTrait;
    use DockerContainerEnvsTrait;
    use DockerContainerPortsTrait;
    use DockerContainerVolumesTrait;
    use DockerContainerCopyEntriesTrait;

    /**
     * @var ServiceInterface
     */
    private $service;

    /**
     * @param ServiceInterface $service
     */
    public function __construct(ServiceInterface $service)
    {
        $this->service = $service;
        $this->setMaintainer('Docker Arch <https://github.com/Ph3nol/Docker-Arch>');
        $this->init();
    }

    /**
     * @return ServiceInterface
     */
    public function getService(): ServiceInterface
    {
        return $this->service;
    }

    /**
     * @return void
     */
    protected function applyDotfiles(): void
    {
        // Volumes.
        $this
            ->addVolume(['local' => '~/.ssh', 'remote' => '/root/.ssh', 'type' => 'ro'])
            ->addVolume(['local' => '~/.gitconfig', 'remote' => '/root/.ssh', 'type' => 'ro'])
            ->addVolume(['local' => '~/.gitconfig', 'remote' => '/root/.gitconfig', 'type' => 'ro'])
            ->addVolume(['local' => '~/.gitignore', 'remote' => '/root/.gitignore', 'type' => 'ro'])
            ->addVolume(['local' => '~/.composer', 'remote' => '/root/.composer', 'type' => 'ro']);
    }

    /**
     * @return void
     */
    protected function applyShellConfiguration(): void
    {
        // Commands.
        if (true === $this->getService()->getOptions()['zsh']) {
            $this
                ->addCommand('echo "zsh" > /root/.bashrc')
                ->addCommand('echo "\nsource /root/.shell.config" > /root/.zshrc')
                ->addCommand('chsh -s /bin/zsh');
        } else {
            $this->addCommand('echo "\nsource /root/.shell.config" > /root/.bashrc');
        }
        if (true === $this->getService()->getOptions()['zsh'] &&
            true === $this->getService()->getOptions()['customZsh']) {
            $this->addCommand('curl https://cdn.rawgit.com/zsh-users/antigen/v1.4.1/bin/antigen.zsh > /root/antigen.zsh');
        }

        // Templated files.
        $this->getService()->addTemplatedFile(new TemplatedFile(
            'dotfiles/.shell.config',
            'Service/Common/.shell.config.twig'
        ));
    }
}
