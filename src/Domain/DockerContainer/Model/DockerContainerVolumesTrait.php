<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
trait DockerContainerVolumesTrait
{
    /**
     * @var array
     */
    protected $volumes = [];

    /**
     * @return array
     */
    public function getVolumes(): array
    {
        return $this->volumes;
    }

    /**
     * @param array $volume
     *
     * @return self
     */
    public function addVolume(array $volume): self
    {
        $this->volumes[$volume['remote']] = $volume;

        return $this;
    }

    /**
     * @param array $volumes
     *
     * @return self
     */
    public function setVolumes(array $volumes): self
    {
        $this->volumes = $volumes;

        return $this;
    }
}
