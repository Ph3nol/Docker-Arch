<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\Architect;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class MariaDBDockerContainer extends MySQLDockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function execute(): void
    {
        parent::execute();

        $this->setFrom(sprintf('mariadb:%s', $this->getService()->getOptions()['version']));

        $this->getService()->getProject()
            ->addEnv('MYSQL_DATA_LOCATION', Architect::GLOBAL_ABSOLUTE_TMP_DIRECTORY.'/data/mariadb');
    }
}
