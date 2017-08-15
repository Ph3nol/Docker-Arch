<?php

namespace Ph3\DockerArch\Domain\TemplatedFile\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class TemplatedFile implements TemplatedFileInterface
{
    /**
     * @var string
     */
    protected $remotePath;

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $chmod;

    /**
     * @param string  $remotePath
     * @param string  $viewPath
     * @param array   $parameters
     * @param integer $chmod
     */
    public function __construct(string $remotePath, string $viewPath, array $parameters = [], int $chmod = null)
    {
        $this->remotePath = $remotePath;
        $this->viewPath = $viewPath;
        $this->parameters = $parameters;
        $this->chmod = $chmod;
    }

    /**
     * @return string
     */
    public function getRemotePath(): string
    {
        return $this->remotePath;
    }

    /**
     * @param string $remotePath
     *
     * @return self
     */
    public function setRemotePath(string $remotePath): self
    {
        $this->remotePath = $remotePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    /**
     * @param string $viewPath
     *
     * @return self
     */
    public function setViewPath(string $viewPath): self
    {
        $this->viewPath = $viewPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param string $parameters
     *
     * @return self
     */
    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return integer
     */
    public function getChmod(): ?int
    {
        return $this->chmod;
    }

    /**
     * @param integer $chmod
     *
     * @return self
     */
    public function setChmod(int $chmod)
    {
        $this->chmod = $chmod;

        return $this;
    }
}
