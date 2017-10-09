<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\Service\Model\AbstractService;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class MySQLService extends AbstractService
{
    const NAME = 'mysql';

    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver(): Options
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['version']);
        $resolver->setAllowedTypes('version', 'string');
        $resolver->setAllowedValues('version', ['5.5', '5.6', '5.7', '8.0']);

        return $resolver;
    }
}
