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
     * @param bool   $condition
     *
     * @return self
     */
    public function addPackage(string $package, bool $condition = true): self
    {
        if (true === $condition) {
            $this->packages[] = $package;
        }

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
