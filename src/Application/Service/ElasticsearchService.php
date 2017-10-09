<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\Service\Model\AbstractService;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class ElasticsearchService extends AbstractService
{
    const NAME = 'elasticsearch';

    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver(): Options
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'version' => '5.6',
            'with_management' => true,
        ]);
        $resolver->setAllowedValues('version', ['6.0', '5.6', '5.5', '5.4', '5.3', '5.2', '5.1', '5.0']);

        return $resolver;
    }
}
