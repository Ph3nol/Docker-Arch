<?php

namespace Ph3\DockerArch\Domain\Project\Model;

use Cocur\Slugify\Slugify;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerEnvsTrait;
use Ph3\DockerArch\Domain\Service\Model\ServiceCollection;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFilesPropertyTrait;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class Project implements ProjectInterface
{
    public const DOCKER_ENV_CONFIGURATION_PREFIX = 'DOCKER_CONFIG_';

    use TemplatedFilesPropertyTrait;
    use DockerContainerEnvsTrait;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $locale = 'en_US';

    /**
     * @var string
     */
    private $path = './';

    /**
     * @var string
     */
    private $logsPath = './.docker-arch/logs';

    /**
     * @var ServiceInterface[]
     */
    private $services;

    /**
     * @param string $label
     */
    public function __construct(string $label)
    {
        $this->label = $label;
        $this->services = new ServiceCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getLabel();
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return (new Slugify())->slugify($this->getLabel());
    }

    /**
     * {@inheritdoc}
     */
    public function getServices(): ServiceCollection
    {
        return $this->services;
    }

    /**
     * {@inheritdoc}
     */
    public function addService(ServiceInterface $service): ProjectInterface
    {
        $this->services->append($service);

        return $this;
    }

    /**
     * @return ServiceInterface[]
     */
    public function getDockerSynchedServices(): ServiceCollection
    {
        return $this->getServices()->getDockerSynchedServices();
    }

    /**
     * @return ServiceInterface[]
     */
    public function getCliServices(): ServiceCollection
    {
        return $this->getServices()->getCliServices();
    }

    /**
     * @param string $name
     *
     * @return ServiceInterface[]
     */
    public function getServicesForName(string $name): ServiceCollection
    {
        return $this->getServices()->getServicesForName($name);
    }

    /**
     * @param ServiceInterface $forService
     *
     * @return array
     */
    public function getLinksForService(ServiceInterface $forService): array
    {
        if (false === $forService->allowedLinksExpression()) {
            return [];
        }

        $links = [];
        foreach ($this->getServices() as $service) {
            if ($service->isSame($forService)) {
                continue;
            }

            if (false === (bool) preg_match('/'.$forService->allowedLinksExpression().'/i', $service->getName())) {
                continue;
            }

            $links[] = $service->getIdentifier();
        }

        return $links;
    }

    /**
     * @param ServiceInterface $forService
     *
     * @return array
     */
    public function getVolumesForService(ServiceInterface $forService): array
    {
        $volumes = [
            '/data/logs' => [
                'local' => $this->getIdentifier().'-logs',
                'remote' => '/data/logs',
                'type' => 'rw',
            ],
        ];

        foreach ($this->getServices() as $service) {
            if (true === $service->isDockerSynched()) {
                $volume = $this->getDockerSynchedServiceVolume($service);
                $volumes[$volume['remote']] = $volume;
            } else {
                $volume = $this->getClassicServiceVolume($service);
                $volumes[$volume['remote']] = $volume;
            }
        }

        $volumes += $forService->getDockerContainer()->getVolumes();

        return array_filter($volumes);
    }

    /**
     * @return string
     */
    public function getLogsPath(): string
    {
        return $this->logsPath;
    }

    /**
     * @param string $logsPath
     *
     * @return self
     */
    public function setLogsPath(string $logsPath)
    {
        $this->logsPath = $logsPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function generateEnvKey(string $key): string
    {
        return self::DOCKER_ENV_CONFIGURATION_PREFIX.$key;
    }

    /**
     * {@inheritdoc}
     */
    public function addEnv(string $key, string $value): self
    {
        $this->envs[$this->generateEnvKey($key)] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return self
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * @param string $user
     *
     * @return self
     */
    public function setUser(string $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return self
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param ServiceInterface $service
     *
     * @return array|null
     */
    private function getDockerSynchedServiceVolume(ServiceInterface $service): ?array
    {
        return [
            'local' => sprintf('%s-%s-sync', $this->getIdentifier(), $service->getIdentifier()),
            'remote' => '/apps/'.($service->getHost() ? : $service->getIdentifier()),
            'type' => 'nocopy',
        ];
    }

    /**
     * @param ServiceInterface $service
     *
     * @return array|null
     */
    private function getClassicServiceVolume(ServiceInterface $service): ?array
    {
        if (null === $path = $service->getPath()) {
            return null;
        }

        return [
            'local' => $path,
            'remote' => '/apps/'.($service->getHost() ? : $service->getIdentifier()),
            'type' => 'rw',
        ];
    }
}
