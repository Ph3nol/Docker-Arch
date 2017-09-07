<?php

namespace Ph3\DockerArch\Application;

use Ph3\DockerArch\Application\DockerGenerator\DockerComposeGenerator;
use Ph3\DockerArch\Application\DockerGenerator\DockerSyncGenerator;
use Ph3\DockerArch\Application\DockerGenerator\MainScriptGenerator;
use Ph3\DockerArch\Application\DockerGenerator\MakefileGenerator;
use Ph3\DockerArch\Application\TemplatedFileGeneratorInterface;
use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;
use Ph3\DockerArch\Infrastructure\Common\Persistence\Exception\NoPersisterFileException;
use Ph3\DockerArch\Infrastructure\Common\Persistence\Persister;
use Ph3\DockerArch\Infrastructure\Common\Persistence\PersisterInterface;
use Ph3\DockerArch\Infrastructure\Project\Repository\ProjectRepository;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class Architect implements ArchitectInterface
{
    public const PROJECT_NAME = 'Docker Arch';
    public const PROJECT_URL = 'https://github.com/ph3nol/docker-arch';
    public const PROJECT_CONFIG_FILENAME = '/.docker-arch.json';
    public const PROJECT_CONFIG_DIRECTORY = '/.docker-arch';
    public const PROJECT_TMP_CONFIG_DIRECTORY = '/.docker-arch.tmp';
    public const GLOBAL_ABSOLUTE_TMP_DIRECTORY = '/tmp/.docker-arch';

    /**
     * @var TemplatedFileGeneratorInterface
     */
    protected $templatedFileGenerator;

    /**
     * @var string
     */
    private $projectPath;

    /**
     * @var PersisterInterface
     */
    private $persister;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @param TemplatedFileGeneratorInterface $templatedFileGenerator
     */
    public function __construct(TemplatedFileGeneratorInterface $templatedFileGenerator)
    {
        define('PROJECT_ROOT_DIR', __DIR__.'/../..');
        define('PROJECT_APP_DIR', PROJECT_ROOT_DIR.'/app');
        define('PROJECT_SRC_DIR', PROJECT_ROOT_DIR.'/src');

        $this->templatedFileGenerator = $templatedFileGenerator;
        $this->fs = new Filesystem();
    }

    /**
     * @return TemplatedFileGeneratorInterface
     */
    public function getTemplatedFileGenerator(): TemplatedFileGeneratorInterface
    {
        return $this->templatedFileGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($projectPath): void
    {
        $project = $this->initProject($projectPath);

        $tmpBuildDir = $this->projectPath.self::PROJECT_TMP_CONFIG_DIRECTORY;
        $this->fs->remove($tmpBuildDir);

        // Project files.
        $project->addTemplatedFile(new TemplatedFile('.gitignore', 'Base/.gitignore.twig'));
        $project->addTemplatedFile(new TemplatedFile('.env.dist', 'Base/.env.dist.twig'));
        $project->addTemplatedFile(new TemplatedFile('Makefile', 'Base/Makefile.twig'));
        $project->addTemplatedFile(new TemplatedFile('docker-compose.yml', 'Base/docker-compose.yml.twig'));
        $project->addTemplatedFile(new TemplatedFile('do', 'Base/do.twig', [], 0755));
        if (0 < count($project->getDockerSynchedServices())) {
            $project->addTemplatedFile(new TemplatedFile('docker-sync.yml', 'Base/docker-sync.yml.twig'));
        }

        // Project files dump.
        foreach ($project->getTemplatedFiles() as $templatedFile) {
            $fileContent = $this->getTemplatedFileGenerator()->render($templatedFile->getViewPath(), array_merge(
                ['project' => $project],
                $templatedFile->getParameters()
            ));
            $this->fs->dumpFile($tmpBuildDir.'/'.$templatedFile->getRemotePath(), $fileContent);
            if ($chmod = $templatedFile->getChmod()) {
                $this->fs->chmod($tmpBuildDir.'/'.$templatedFile->getRemotePath(), $chmod);
            }
        }

        // Project services files, with dump.
        foreach ($project->getServices() as $service) {
            $serviceTmpBuildDir = sprintf('%s/%s', $tmpBuildDir, $service->getIdentifier());

            $service->addTemplatedFile(new TemplatedFile('Dockerfile', 'Base/Service/Dockerfile.twig'));

            foreach ($service->getTemplatedFiles() as $templatedFile) {
                $fileContent = $this->getTemplatedFileGenerator()->render($templatedFile->getViewPath(), array_merge(
                    ['project' => $project, 'service' => $service],
                    $templatedFile->getParameters()
                ));
                $this->fs->dumpFile($serviceTmpBuildDir.'/'.$templatedFile->getRemotePath(), $fileContent);
                if ($chmod = $templatedFile->getChmod()) {
                    $this->fs->chmod($serviceTmpBuildDir.'/'.$templatedFile->getRemotePath(), $chmod);
                }
            }
        }

        $projectDir = $this->projectPath.self::PROJECT_CONFIG_DIRECTORY;

        // Backup some resources.
        $dotEnvFilePath = $projectDir.'/.env';
        $initialDotEnvContent = null;
        if (file_exists($dotEnvFilePath)) {
            $initialDotEnvContent = file_get_contents($dotEnvFilePath);
        }

        // Export Docker configuration and clean.
        $this->fs->remove($projectDir);
        $this->fs->mirror($tmpBuildDir, $projectDir);
        $this->fs->remove($tmpBuildDir);

        // Recreate backuped resources.
        if (null !== $initialDotEnvContent) {
            $this->fs->dumpFile($dotEnvFilePath, $initialDotEnvContent);
        } else {
            $this->fs->dumpFile($dotEnvFilePath, file_get_contents($projectDir.'/.env.dist'));
        }
    }

    /**
     * @param string $projectPath
     *
     * @return ProjectInterface
     */
    private function initProject($projectPath): ?ProjectInterface
    {
        $this->projectPath = $projectPath;

        try {
            $this->persister = new Persister($this->projectPath);
        } catch (NoPersisterFileException $e) {
            $this->persister = Persister::init($this->projectPath);
        }

        $project = (new ProjectRepository($this->persister))->getProject();
        $project->setPath($this->projectPath);

        return $project;
    }
}
