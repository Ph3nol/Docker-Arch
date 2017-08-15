<?php

namespace Ph3\DockerArch\Infrastructure\Project\Repository;

use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;
use Ph3\DockerArch\Infrastructure\Common\AbstractRepository;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\ProjectDataTransformer;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class ProjectRepository extends AbstractRepository implements ProjectRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProject(): ProjectInterface
    {
        $projectData = $this->getPersister()->read();

        return (new ProjectDataTransformer())->toModel($projectData);
    }
}
