<?php

namespace Ph3\DockerArch\Domain\Service\Model;

use Cocur\Slugify\Slugify;
use Ph3\DockerArch\Application\Service\CliInterface;
use Ph3\DockerArch\Application\Service\CustomService;
use Ph3\DockerArch\Application\Service\VhostInterface;
use Ph3\DockerArch\Application\Service\WebInterface;
use Ph3\DockerArch\Domain\DockerContainer\Exception\NotFoundException as DockerContainerNotFoundException;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFilesPropertyTrait;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
abstract class AbstractService implements ServiceInterface
{
    use TemplatedFilesPropertyTrait;

    /**
     * @var ProjectInterface
     */
    private $project;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var DockerContainerInterface
     */
    private $dockerContainer;

    /**
     * @var boolean
     */
    private $dockerSync = false;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var array
     */
    private $uiAccesses = [];

    /**
     * @param ProjectInterface $project
     * @param array            $options
     */
    public function __construct(ProjectInterface $project, array $options = [])
    {
        $this->project = $project;
        $this->options = $this->getOptionsResolver()->resolve($options);
        $this->identifier = (new Slugify())->slugify($this->getName(), '-');
        $this->setDockerContainer(
            DockerContainer::getInstanceForService($this)
        );
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getIdentifier() ?? '';
    }

    /**
     * @return ProjectInterface
     */
    public function getProject(): ProjectInterface
    {
        return $this->project;
    }

    /**
     * @param ServiceInterface $service
     *
     * @return boolean
     */
    public function isSame(ServiceInterface $service): bool
    {
        return ($service->getIdentifier() === $this->getIdentifier());
    }

    /**
     * @return boolean
     */
    public function isCustom(): bool
    {
        return $this instanceof CustomService;
    }

    /**
     * @return ServiceInterface
     */
    public function withDockerSync(): self
    {
        $this->dockerSync = true;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDockerSynched(): bool
    {
        return $this->dockerSync;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return Options
     */
    public function getOptionsResolver(): Options
    {
        return new OptionsResolver();
    }

    /**
     * @param string $path
     *
     * @return ServiceInterface
     */
    public function setPath($path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $host
     *
     * @return ServiceInterface
     */
    public function setHost($host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @return DockerContainerInterface
     */
    public function getDockerContainer(): DockerContainerInterface
    {
        return $this->dockerContainer;
    }

    /**
     * @param DockerContainerInterface $dockerContainer
     *
     * @return self
     */
    public function setDockerContainer(DockerContainerInterface $dockerContainer): self
    {
        $this->dockerContainer = $dockerContainer;

        return $this;
    }

    /**
     * @return array
     */
    public function allowedLinksFqcns(): array
    {
        return [];
    }

    /**
     * @return boolean
     */
    public function isVhost(): bool
    {
        return $this instanceof VhostInterface;
    }

    /**
     * @return boolean
     */
    public function isWeb(): bool
    {
        return $this instanceof WebInterface;
    }

    /**
     * @return boolean
     */
    public function isCli(): bool
    {
        return $this instanceof CliInterface;
    }

    /**
     * @param string $identifier
     *
     * @return self
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function generateEnvKey(string $key): string
    {
        $host = (new Slugify())->slugify($this->getHost(), '_');

        return sprintf(
            '%s%s',
            $this->getHost() ? strtoupper($host).'_' : '',
            $key
        );
    }

    /**
     * @param array $access
     *
     * @return self
     */
    public function addUIAccess(array $access): self
    {
        $this->uiAccesses[] = array_merge([
            'label' => null,
            'uri' => null,
            'host' => $this->getHost() ? : 'localhost',
            'port' => null,
        ], $access);

        return $this;
    }

    /**
     * @return array
     */
    public function getUIAccesses(): array
    {
        return $this->uiAccesses;
    }

    /**
     * {@inheritdoc}
     */
    public static function getInstanceForParentService(ServiceInterface $service): ServiceInterface
    {
        $instance = new static($service->getProject());

        $identifier = sprintf('%s-for-%s', $instance->getIdentifier(), $service->getIdentifier());
        $identifier = (new Slugify())->slugify($identifier, '-');

        $instance
            ->setParentService($service)
            ->setIdentifier($identifier);

        return $instance;
    }
}
