<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\Service\Model\AbstractService;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class RabbitMQService extends AbstractService
{
    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver(): Options
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'version' => '3',
            'with_management' => true,
        ]);
        $resolver->setAllowedValues('version', ['3']);
        $resolver->setNormalizer('version', function (Options $options, $value) {
            if (true === $options['with_management']) {
                return $value.'-management';
            }

            return $value;
        });

        return $resolver;
    }
}
