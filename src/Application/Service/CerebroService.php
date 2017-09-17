<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\Service\Model\AbstractService;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class CerebroService extends AbstractService implements HasParentServiceInterface
{
    use ParentServiceTrait;

    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver(): Options
    {
        return new OptionsResolver();
    }

    /**
     * {@inheritdoc}
     */
    public static function getInstanceForParentService(ServiceInterface $service): ServiceInterface
    {
        $instance = parent::getInstanceForParentService($service);

        return $instance;
    }
}
