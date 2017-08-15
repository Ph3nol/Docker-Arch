<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\DockerContainerInflector;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\Service\Model\Service;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class PhpDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $service = $this->getService();

        $this
            ->setFrom(sprintf('php:%s', $service->getOptions()['version']))
            ->setWorkingDir(sprintf(
                '/apps/%s',
                $service->getHost() ? : $service->getIdentifier()
            ))
            ->applyShellConfiguration();

        // Volumes.
        if (true === $service->getOptions()['dotfiles']) {
            $this->applyDotfiles();
        }

        // Packages.
        $this
            ->addPackage('curl')
            ->addPackage('vim')
            ->addPackage('git');
        if ($service->getOptions()['zsh']) {
            $this->addPackage('zsh');
        }
        if ($service->getOptions()['extensions']['mysql'] ?? false) {
            $this
                ->addPackage('libmcrypt-dev')
                ->addPackage('mysql-client');
        }
        if (true === $service->getOptions()['composer']) {
            $this
                ->addPackage('zip')
                ->addPackage('unzip');
        }

        // Commands.
        $this->addOwnCommands();

        // Envs.
        if (true === $service->getOptions()['composer']) {
            $this->addEnv('PATH', '/root/.composer/vendor/bin:$PATH');
        }
    }

    /**
     * @return void
     */
    private function addOwnCommands(): void
    {
        $service = $this->getService();

        // PHP extensions part.
        $dockerPHPExtensions = ['mcrypt', 'pdo_mysql', 'bcmath'];
        foreach ($service->getOptions()['extensions'] as $extension) {
            if (true === in_array($extension, $dockerPHPExtensions)) {
                $this->addCommand('docker-php-ext-install '.$extension);
            }
        }

        // XDebug part.
        if (true === in_array('xdebug', $service->getOptions()['extensions'])) {
            $this->addCommand('pecl install -o -f xdebug');
            $this->addCommand('rm -rf /tmp/pear');
            $this->addCommand('echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20131226/xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini');
            $this->addCommand('echo "xdebug.remote_autostart = 1" >> /usr/local/etc/php/conf.d/xdebug.ini');
        }

        // Redis part.
        if (true === in_array('redis', $service->getOptions()['extensions'])) {
            $this->addCommand('pecl install redis');
            $this->addCommand('docker-php-ext-enable redis');
        }

        // Some configs.
        foreach ($service->getOptions()['config'] as $key => $value) {
            if (true === $service->getOptions()['cliOnly']) {
                $this->addCommand('echo "'.$key.' = '.$value.'" >> /usr/local/etc/php/conf.d/php.ini');
            } else {
                $this->addCommand('echo "php_admin_value['.$key.'] = '.$value.'" >> /usr/local/etc/php-fpm.d/www.conf');
            }
        }

        // Composer part.
        if (true === $service->getOptions()['composer']) {
            $this->addCommand('curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/bin');
        }
    }
}
