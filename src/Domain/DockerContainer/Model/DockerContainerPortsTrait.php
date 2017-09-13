<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
trait DockerContainerPortsTrait
{
    /**
     * @var array
     */
    protected $ports = [];

    /**
     * @return array
     */
    public function getPorts(): array
    {
        return $this->ports;
    }

    /**
     * @param array $port
     *
     * @return self
     */
    public function addPort(array $port): self
    {
        $this->ports[$port['from']] = $port;

        return $this;
    }

    /**
     * @param array $ports
     *
     * @return self
     */
    public function setPorts(array $ports): self
    {
        $this->ports = $ports;

        return $this;
    }
}
