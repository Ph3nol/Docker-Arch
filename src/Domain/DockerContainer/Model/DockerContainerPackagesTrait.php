<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
trait DockerContainerPackagesTrait
{
    /**
     * @var array
     */
    protected $packages = [];

    /**
     * @return array
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    /**
     * @param string $package
     *
     * @return self
     */
    public function addPackage(string $package): self
    {
        $this->packages[] = $package;

        return $this;
    }

    /**
     * @param array $packages
     *
     * @return self
     */
    public function setPackages(array $packages): self
    {
        $this->packages = $packages;

        return $this;
    }
}
