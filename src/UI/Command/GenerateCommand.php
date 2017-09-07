<?php

namespace Ph3\DockerArch\UI\Command;

use Ph3\DockerArch\Application\Architect;
use Ph3\DockerArch\Application\TemplatedFileGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class GenerateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('generate')
            ->setDescription('Generate Docker configuration.')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                sprintf(
                    'Project local path, containing %s configuration file.',
                    Architect::PROJECT_CONFIG_FILENAME
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templatedFileGenerator = new TemplatedFileGenerator();
        $architect = new Architect($templatedFileGenerator);
        $architect->generate($input->getArgument('path') ? : getcwd());
    }
}
