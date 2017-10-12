<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Cocur\Slugify\Slugify;
use Ph3\DockerArch\Application\Architect;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;
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

        // Ports.
        $this->addEnvPort('NGINX', ['from' => '8080', 'to' => '80']);
    }

    /**
     * {@inheritdoc}
     */
    public function postExecute(): void
    {
        $service = $this->getService();

        // UI.
        $port = reset($service->getDockerContainer()->getPorts());
        foreach ($this->vhostsServicesByHost as $vhostService) {
            if (null === $vhostService->getHost()) {
                continue;
            }

            $service->addUIAccess([
                'host' => $vhostService->getHost(),
                'port' => $port['from'],
                'label' => 'Web Access ('.$vhostService->getHost().')',
            ]);
        }

        // Main Network Aliases.
        $isServiceNetworkAliasable = function (ServiceInterface $service) {
            return (null !== $service->getHost() && 'localhost' !== $service->getHost());
        };
        if (true === $isServiceNetworkAliasable($service)) {
            $this->addNetworkAlias(self::DOCKER_MAIN_NETWORK, $service->getHost());
        }
        foreach ($this->vhostsServicesByHost as $vhostService) {
            if ($isServiceNetworkAliasable($vhostService)) {
                $this->addNetworkAlias(self::DOCKER_MAIN_NETWORK, $vhostService->getHost());
            }
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
            if (null !== $service->getHost()) {
                $this->vhostsServicesByHost[$service->getHost()] = $service;
            }
        }

        $hasGeneratedVhosts = false;
        $vhostIndex = 0;
        foreach ($this->vhostsServicesByHost as $service) {
            $appType = $service->getOptions()['app_type'] ?? null;
            preg_match('/(\w+)Service$/i', get_class($service), $matches);
            $vhostFileName = (new Slugify())->slugify($matches[1], '-');
            $templatePath = sprintf(
                'Service/Nginx/vhosts/%s%s.conf.twig',
                $vhostFileName,
                $appType ? '-'.$appType : null
            );
            $filePath = sprintf(
                'conf.d/%s-%s%s.vhost.conf',
                str_pad($vhostIndex + 10, 3, '0', STR_PAD_LEFT),
                $vhostFileName,
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
