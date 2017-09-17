<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\Architect;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class MariadbDockerContainer extends MysqlDockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->setFrom(sprintf('mariadb:%s', $this->getService()->getOptions()['version']));

        $this->getService()->getProject()
            ->addEnv('MYSQL_DATA_LOCATION', Architect::GLOBAL_ABSOLUTE_TMP_DIRECTORY.'/data/mariadb');
    }
}
