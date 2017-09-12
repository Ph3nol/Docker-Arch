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
     * @param string $volume
     *
     * @return self
     */
    public function addVolumeFromString(string $volume): self
    {
        $volume = explode(':', $volume);
        $this->addVolume([
            'local' => $volume[0],
            'remote' => $volume[1],
            'type' => $volume[2] ?? 'ro',
        ]);

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
