<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\DockerContainerInflector;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\Service\Model\Service;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class NodejsDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $service = $this->getService();

        $this
            ->setFrom(sprintf('node:%s', $service->getOptions()['version']))
            ->setWorkingDir(sprintf(
                '/apps/%s',
                $service->getHost() ? : $service->getIdentifier()
            ))
            ->setEntryPoint('/root/entrypoint.sh')
            ->applyShellConfiguration();

        // Volumes.
        if (true === $service->getOptions()['dotfiles']) {
            $this->applyDotfiles();
        }

        // Packages.
        $this
            ->addPackage('curl')
            ->addPackage('vim')
            ->addPackage('git');
        if ($service->getOptions()['zsh']) {
            $this->addPackage('zsh');
        }
        if ($service->getOptions()['supervisor']) {
            $this->addPackage('supervisor');
        }

        // Commands.
        $this->addCommand('chmod +x /root/entrypoint.sh');
        if (true === $service->getOptions()['bower']) {
            $this->addCommand('npm install -g bower');
            $this->addCommand('echo \'{ "allow_root": true }\' > /root/.bowerrc');
        }
        if (true === $service->getOptions()['gulp']) {
            $this->addCommand('npm install -g gulp-cli');
        }

        // Copy entries.
        $this->addCopyEntry(['local' => 'entrypoint.sh', 'remote' => '/root/entrypoint.sh']);

        // Templated files.
        $service->addTemplatedFile(new TemplatedFile(
            'entrypoint.sh',
            'Service/Nodejs/entrypoint.sh.twig'
        ));
    }
}
