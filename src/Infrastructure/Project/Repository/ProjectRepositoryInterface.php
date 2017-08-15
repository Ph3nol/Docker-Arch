<?php

namespace Ph3\DockerArch\Infrastructure\Project\Repository;

use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
interface ProjectRepositoryInterface
{
    /**
     * @return ProjectInterface
     */
    public function getProject(): ProjectInterface;
}
