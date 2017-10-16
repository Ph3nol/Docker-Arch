<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\Service\Model\AbstractService;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class CustomService extends AbstractService
{
    const NAME = 'custom';

    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver(): Options
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['image', 'package_manager']);
        $resolver->setAllowedValues('package_manager', [
            DockerContainerInterface::PACKAGE_MANAGER_TYPE_APT,
            DockerContainerInterface::PACKAGE_MANAGER_TYPE_APTITUTDE,
            DockerContainerInterface::PACKAGE_MANAGER_TYPE_APK,
        ]);
        $resolver->setDefaults([
            'cli' => false,
            'vhost' => false,
            'web' => false,
        ]);

        return $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function isVhost(): bool
    {
        return true === $this->getOptions()['vhost'];
    }

    /**
     * {@inheritdoc}
     */
    public function isWeb(): bool
    {
        return true === $this->getOptions()['web'];
    }

    /**
     * {@inheritdoc}
     */
    public function isCli(): bool
    {
        return true === $this->getOptions()['cli'];
    }
}
