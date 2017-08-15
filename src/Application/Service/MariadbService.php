<?php

namespace Ph3\DockerArch\Application\Service;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class MariadbService extends MysqlService
{
    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver(): Options
    {
        $resolver = parent::getOptionsResolver();
        $resolver->setAllowedValues('version', ['5.5', '10.0', '10.1', '10.2', '10.3']);

        return $resolver;
    }
}
