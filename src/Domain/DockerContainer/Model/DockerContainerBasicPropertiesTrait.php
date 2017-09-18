<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
trait DockerContainerBasicPropertiesTrait
{
    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $maintainer;

    /**
     * @var string
     */
    protected $workingDir;

    /**
     * @var string
     */
    protected $entryPoint;

    /**
     * @var string
     */
    protected $cmd;

    /**
     * @var string
     */
    protected $user;

    /**
     * @return string
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * @param string $from
     *
     * @return self
     */
    public function setFrom(string $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getWorkingDir(): ?string
    {
        return $this->workingDir;
    }

    /**
     * @param string $workingDir
     *
     * @return self
     */
    public function setWorkingDir(string $workingDir): self
    {
        $this->workingDir = $workingDir;

        return $this;
    }

    /**
     * @return string|array
     */
    public function getEntryPoint()
    {
        return $this->entryPoint;
    }

    /**
     * @param string|array $entryPoint
     *
     * @return self
     */
    public function setEntryPoint($entryPoint): self
    {
        if (true === is_string($entryPoint)) {
            $entryPoint = [$entryPoint];
        }

        $this->entryPoint = $entryPoint;

        return $this;
    }

    /**
     * @return string|array
     */
    public function getCmd()
    {
        return $this->cmd;
    }

    /**
     * @param string|array $cmd
     *
     * @return self
     */
    public function setCmd($cmd): self
    {
        if (true === is_string($cmd)) {
            $cmd = [$cmd];
        }

        $this->cmd = $cmd;

        return $this;
    }

    /**
     * @return string
     */
    public function getMaintainer(): ?string
    {
        return $this->maintainer;
    }

    /**
     * @param string $maintainer
     *
     * @return self
     */
    public function setMaintainer(string $maintainer): self
    {
        $this->maintainer = $maintainer;

        return $this;
    }

    /**
     * @return string
     */
    public function getUser(): string
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
}
