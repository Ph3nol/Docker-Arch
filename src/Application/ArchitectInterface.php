<?php

namespace Ph3\DockerArch\Application;

use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;

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
    public function build($projectPath): void;
}
