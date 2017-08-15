<?php

namespace Ph3\DockerArch\Domain\Service\Model;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFileInterface;
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
    private $localPath;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param ProjectInterface $project
     * @param array            $options
     */
    public function __construct(ProjectInterface $project, array $options = [])
    {
        $this->project = $project;
        $this->options = $this->getOptionsResolver()->resolve($options);
        $this->initIdentifier();
    }

    /**
     * @return ProjectInterface
     */
    public function getProject(): ProjectInterface
    {
        return $this->project;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        preg_match('/(\w+)Service$/i', get_called_class(), $matches);


        return lcfirst($matches[1]);
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
     * @param string $localPath
     *
     * @return ServiceInterface
     */
    public function setLocalPath($localPath): self
    {
        $this->localPath = $localPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocalPath(): ?string
    {
        return $this->localPath;
    }

    /**
     * @param string $host
     *
     * @return ServiceInterface
     */
    public function setHost($host): self
    {
        $this->host = $host;
        $this->initIdentifier();

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
     * @return void
     */
    private function initIdentifier(): void
    {
        if ($hostKey = $this->getHost()) {
            $hostKey = str_replace(['.', '_'], '-', $hostKey);
            $this->identifier = sprintf('%s-%s', $this->getName(), $hostKey);
        } else {
            $this->identifier = sprintf('%s-%s', $this->getName(), uniqid());
        }
    }
}
