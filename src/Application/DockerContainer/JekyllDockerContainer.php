<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class JekyllDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function getPackageManager(): string
    {
        return DockerContainerInterface::PACKAGE_MANAGER_TYPE_APK;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(): void
    {
        $service = $this->getService();

        $this->setFrom('jekyll/jekyll:latest');

        $this->setWorkingDir($this->getMainPath());

        // EntryPoint.
        $service
            ->addTemplatedFile(new TemplatedFile(
                'entrypoint.sh',
                'Service/Jekyll/entrypoint.sh.twig'
            ));
        $this
            ->addCopyEntry([
                'local' => 'entrypoint.sh',
                'remote' => '/root/entrypoint.sh',
            ])
            ->addCommand('chmod +x /root/entrypoint.sh')
            ->setEntryPoint(['/root/entrypoint.sh']);

        // Ports.
        $this->addEnvPort('JEKYLL', ['from' => '4004', 'to' => '4000']);
    }

    /**
     * {@inheritdoc}
     */
    public function postExecute(): void
    {
        // UI.
        $port = reset($this->getService()->getDockerContainer()->getPorts());
        $this->getService()->addUIAccess([
            'url' => 'localhost',
            'port' => $port['from'],
            'label' => 'Website (from Watching)',
        ]);
    }
}
