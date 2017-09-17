<?php

namespace Ph3\DockerArch\Application;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
interface ArchitectInterface
{
    /**
     * @param string $projectPath
     *
     * @return void
     */
    public function generate($projectPath): void;
}
