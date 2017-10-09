<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

use Ph3\DockerArch\Domain\Service\Model\DockerContainerNotFoundException;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class DockerContainer implements DockerContainerInterface
{
    const DOCKER_MAIN_NETWORK = 'main-network';

    use DockerContainerBasicPropertiesTrait;
    use DockerContainerPackagesTrait;
    use DockerContainerCommandsTrait;
    use DockerContainerEnvsTrait;
    use DockerContainerPortsTrait;
    use DockerContainerVolumesTrait;
    use DockerContainerNetworksTrait;
    use DockerContainerCopyEntriesTrait;

    /**
     * @var string
     */
    private $packageManager = DockerContainerInterface::PACKAGE_MANAGER_TYPE_APT;

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
        $this->init();
    }

    /**
     * @return void
     */
    private function init(): void
    {
        $service = $this->getService();

        $this
            ->setMaintainer('Docker Arch <https://github.com/Ph3nol/Docker-Arch>')
            ->addEnv('DOCKER_CONTAINER_NAME', $this->getService()->getIdentifier())
            ->addEnv('DEBIAN_FRONTEND', 'noninteractive');

        $this
            ->addPackage('openssh-client')
            ->addPackage('vim');

        if ($service->isWeb()) {
            $this
                ->addEnv('TERM', 'xterm-256color')
                ->addEnv('GIT_DISCOVERY_ACROSS_FILESYSTEM', 'true')
                ->addPackage('git')
                ->addPackage('less')
                ->addPackage('wget')
                ->addPackage('curl');
        }

        if (true === $this->isPackageManager(self::PACKAGE_MANAGER_TYPE_APK)) {
            $this
                ->addPackage('bash')
                ->addPackage('findutils')
                ->addPackage('ca-certificates')
                ->addPackage('openssl')
                ->addCommand('update-ca-certificates');
        }

        $this->addNetwork(self::DOCKER_MAIN_NETWORK);
        if (null !== $service->getHost() && 'localhost' !== $service->getHost()) {
            $this->addNetworkAlias(self::DOCKER_MAIN_NETWORK, $service->getHost());
        }

        $this->initLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function preExecute(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postExecute(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function execute(): void
    {
        $this->execute();
    }

    /**
     * @return string
     */
    public function getUserHomeDirectory(): string
    {
        return ('root' === $this->getUser()) ? '/root' : '/home/'.$this->getUser();
    }

    /**
     * @return string
     */
    public function getPackageManager(): string
    {
        return $this->packageManager;
    }

    /**
     * @param string $packageManager
     *
     * @return bool
     */
    public function isPackageManager($packageManager): bool
    {
        return ($packageManager === $this->getPackageManager());
    }

    /**
     * @param string $packageManager
     *
     * @return self
     */
    public function setPackageManager(string $packageManager): self
    {
        $this->packageManager = $packageManager;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return self
     */
    public function addEnvFromProject(string $key): self
    {
        $this->envs[$key] = '${' . $key . '}';

        return $this;
    }

    /**
     * @param string $envProperty
     * @param array  $port
     *
     * @return array
     */
    public function addEnvPort(string $envProperty, array $port): array
    {
        $service = $this->getService();
        $project = $service->getProject();

        $portKey = $service->generateEnvKey($envProperty.'_PORT');
        $project->addEnv($portKey, $port['from']);
        $port['from'] = '${' . $portKey . '}';
        $this->addPort($port);

        return $port;
    }

    /**
     * @return ServiceInterface
     */
    public function getService(): ServiceInterface
    {
        return $this->service;
    }

    /**
     * @param ServiceInterface $service
     *
     * @return DockerContainerInterface
     */
    public function getInstanceForService(ServiceInterface $service): DockerContainerInterface
    {
        $dockerContainerFqcn = str_replace('Service', 'DockerContainer', get_class($service));
        if (false === class_exists($dockerContainerFqcn)) {
            throw new DockerContainerNotFoundException($dockerContainerFqcn.' DockerContainer not found');
        }

        return new $dockerContainerFqcn($service);
    }

    /**
     * @param string $relativePath
     *
     * @return string
     */
    protected function getAbsoluteUserPath(string $relativePath): string
    {
        return str_replace('~/', $this->getUserHomeDirectory().'/', $relativePath);
    }

    /**
     * @return void
     */
    protected function applyDotfiles(): void
    {
        // Volumes.
        $this
            ->addVolume([
                'local' => '~/.ssh', 'remote' => $this->getAbsoluteUserPath('~/.ssh'), 'type' => 'ro'
            ])
            ->addVolume([
                'local' => '~/.gitconfig', 'remote' => $this->getAbsoluteUserPath('~/.gitconfig'), 'type' => 'ro'
            ])
            ->addVolume([
                'local' => '~/.gitignore', 'remote' => $this->getAbsoluteUserPath('~/.gitignore'), 'type' => 'ro'
            ]);
    }

    /**
     * @return void
     */
    protected function applyShellConfiguration(): void
    {
        if (true === $this->getService()->getOptions()['zsh']) {
            $this
                ->addPackage('zsh')
                ->addCommand('echo "source ~/.shell.config" >> '.$this->getAbsoluteUserPath('~/.zshrc'));
        } else {
            $this->addCommand('echo "source ~/.shell.config" >> '.$this->getAbsoluteUserPath('~/.bashrc'));
        }
        if (true === $this->getService()->getOptions()['zsh'] &&
            true === $this->getService()->getOptions()['custom_zsh']) {
            $this->addCommand(
                'curl https://cdn.rawgit.com/zsh-users/antigen/v1.4.1/bin/antigen.zsh > '.
                    $this->getAbsoluteUserPath('~/.antigen.zsh')
            );
        }
        $this->getService()->addTemplatedFile(new TemplatedFile(
            'dotfiles/.shell.config',
            'Service/Common/shell.config.twig'
        ));
        $this->addCopyEntry([
            'local' => 'dotfiles/.shell.config',
            'remote'=> $this->getAbsoluteUserPath('~/.shell.config'),
        ]);
    }

    /**
     * @return void
     */
    protected function applyWebServiceConfiguration(): void
    {
        if (null === $this->getWorkingDir()) {
            $this->setWorkingDir(sprintf(
                '/apps/%s',
                $this->getService()->getHost() ? : $this->getService()->getIdentifier()
            ));
        }
    }

    /**
     * @return void
     */
    private function initLocale(): void
    {
        $locale = $this->getService()->getProject()->getLocale();

        if (true === $this->isPackageManager(self::PACKAGE_MANAGER_TYPE_APT)) {
            $this
                // Packages.
                ->addPackage('locales')
                ->addPackage('zlib1g-dev')
                ->addPackage('libicu-dev')
                ->addPackage('g++')
                // Commands.
                ->addCommand(sprintf('echo "%s.UTF-8 UTF-8" > /etc/locale.gen', $locale))
                ->addCommand(sprintf('locale-gen %s.UTF-8', $locale))
                ->addCommand('dpkg-reconfigure locales')
                ->addCommand(sprintf('/usr/sbin/update-locale LANG=%s.UTF-8', $locale))
                // Envs.
                ->addEnv('LC_ALL', sprintf('%s.UTF-8', $locale));
        }
    }
}
