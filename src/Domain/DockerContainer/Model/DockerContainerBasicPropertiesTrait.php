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
     * @return string
     */
    public function getEntryPoint(): ?string
    {
        return $this->entryPoint;
    }

    /**
     * @param string $entryPoint
     *
     * @return self
     */
    public function setEntryPoint(string $entryPoint): self
    {
        $this->entryPoint = $entryPoint;

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
}
