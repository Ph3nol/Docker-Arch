<?php

namespace Ph3\DockerArch\Application\Service;

use Symfony\Component\OptionsResolver\Options;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class MariaDBService extends MySQLService
{
    const NAME = 'mariadb';

    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver(): Options
    {
        $resolver = parent::getOptionsResolver();
        $resolver->setAllowedTypes('version', 'string');
        $resolver->setAllowedValues('version', ['5.5', '10.0', '10.1', '10.2', '10.3']);

        return $resolver;
    }
}
