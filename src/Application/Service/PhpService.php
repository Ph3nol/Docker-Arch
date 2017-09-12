<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\Service\Model\AbstractService;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class PhpService extends AbstractService
{
    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver(): Options
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'app_type' => null,
            'dotfiles' => false,
            'cli_only' => false,
            'zsh' => true,
            'custom_zsh' => false,
            'powerline' => false,
            'composer' => true,
            'extensions' => [],
            'config' => [],
        ]);
        $resolver->setRequired(['version']);
        $resolver->setAllowedTypes('version', 'string');
        $resolver->setAllowedValues('version', ['5.6', '7.0', '7.1', '7.2']);
        $resolver->setNormalizer('version', function (Options $options, $value) {
            $dockerVersionSuffix = (true === $options['cli_only']) ? '-cli' : '-fpm';

            return $value.$dockerVersionSuffix;
        });
        $resolver->setNormalizer('config', function (Options $options, $value) {
            $defaultValue = [
                'display_errors' => 'on',
                'memory_limit' => '256M',
                'log_errors' => 'on',
            ];

            if (false === $options['cli_only']) {
                $defaultValue['error_log'] = '/var/log/fpm-php.www.log';
            }

            return array_merge($defaultValue, $value);
        });

        return $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function allowedLinksExpression(): ?string
    {
        return '(mysql|mariadb|redis|rabbitmq)';
    }

    /**
     * {@inheritdoc}
     */
    public function isWebService(): bool
    {
        return true;
    }
}
