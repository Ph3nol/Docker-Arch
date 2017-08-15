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
     * @param integer $from
     * @param integer $to
     *
     * @return self
     */
    public function addPort(int $from, int $to): self
    {
        $this->ports[$from] = $to;

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
