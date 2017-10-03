<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\Architect;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class NginxDockerContainer extends DockerContainer
{
    /**
     * @var array
     */
    private $vhostsServicesByHost = [];

    /**
     * {@inheritdoc}
     */
    public function getPackageManager(): string
    {
        return DockerContainerInterface::PACKAGE_MANAGER_TYPE_APK;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(): void
    {
        $this->setFrom('nginx:1-alpine');
        $this->applyWebServiceConfiguration();

        $service = $this->getService();
        $project = $service->getProject();

        // Templated files.
        $service->addTemplatedFile(new TemplatedFile(
            'conf.d/000-default.conf',
            'Service/Nginx/configs/default.conf.twig'
        ));
        $hasGeneratedVhosts = $this->addVhostsTemplatedFiles();

        // Commands
        $this->addCommand('rm /etc/nginx/conf.d/default.conf');

        // Copy entries.
        if (true === $hasGeneratedVhosts) {
            $this->addCopyEntry([
                'local' => 'conf.d/*',
                'remote' => '/etc/nginx/conf.d/',
            ]);
        }

        // Volumes.
        $project->addEnv('NGINX_LOGS_LOCATION', Architect::GLOBAL_ABSOLUTE_TMP_DIRECTORY.'/logs/nginx');
        $this
            ->addVolume([
                'local' => '${NGINX_LOGS_LOCATION}',
                'remote' => '/var/log/nginx',
                'type' => 'rw',
            ]);

        // Networks.
        $this->addNetwork(
            self::DOCKER_MAIN_NETWORK,
            array_filter(array_keys($this->vhostsServicesByHost), function (string $host): bool {
                return ('localhost' !== $host);
            })
        );

        // Ports.
        $this->addEnvPort('NGINX', ['from' => '8080', 'to' => '80']);
    }

    /**
     * {@inheritdoc}
     */
    public function postEecute(): void
    {
        // UI.
        $port = reset($this->getService()->getDockerContainer()->getPorts());
        foreach ($this->vhostsServicesByHost as $vhostService) {
            if (null === $vhostService->getHost()) {
                continue;
            }

            $this->getService()->addUIAccess([
                'host' => $vhostService->getHost(),
                'port' => $port['from'],
                'label' => 'Web Access ('.$vhostService->getHost().')',
            ]);
        }
    }

    /**
     * @return boolean
     */
    private function addVhostsTemplatedFiles(): bool
    {
        $dockerContainerService = $this->getService();

        $this->vhostsServicesByHost = [];
        foreach ($dockerContainerService->getProject()->getVhostServices() as $service) {
            if (false === $service->isCliOnly() && null !== $service->getHost()) {
                $this->vhostsServicesByHost[$service->getHost()] = $service;
            }
        }

        $hasGeneratedVhosts = false;
        $vhostIndex = 0;
        foreach ($this->vhostsServicesByHost as $service) {
            $appType = $service->getOptions()['app_type'] ?? null;
            $templatePath = sprintf(
                'Service/Nginx/vhosts/%s%s.conf.twig',
                $service->getName(),
                $appType ? '-'.$appType : null
            );
            $filePath = sprintf(
                'conf.d/%s-%s%s.vhost.conf',
                str_pad($vhostIndex + 10, 3, '0', STR_PAD_LEFT),
                $service->getIdentifier(),
                $appType ? '-'.$appType : null
            );

            $dockerContainerService->addTemplatedFile(new TemplatedFile($filePath, $templatePath, [
                'forService' => $service,
            ]));

            ++$vhostIndex;
            $hasGeneratedVhosts = true;
        }

        return $hasGeneratedVhosts;
    }
}
