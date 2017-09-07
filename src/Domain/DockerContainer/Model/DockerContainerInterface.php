<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
interface DockerContainerInterface
{
    public const PACKAGE_MANAGER_TYPE_APT = 'apt';
    public const PACKAGE_MANAGER_TYPE_APTITUTDE = 'aptitude';
    public const PACKAGE_MANAGER_TYPE_APK = 'apk';

    /**
     * @return void
     */
    public function init(): void;
}
