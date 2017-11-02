<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
interface DockerContainerInterface
{
    const PACKAGE_MANAGER_TYPE_APT = 'apt';
    const PACKAGE_MANAGER_TYPE_APTITUDE = 'aptitude';
    const PACKAGE_MANAGER_TYPE_APK = 'apk';

    /**
     * @return void
     */
    public function preExecute(): void;

    /**
     * @return void
     */
    public function execute(): void;

    /**
     * @return void
     */
    public function postExecute(): void;
}
