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
    public const PROJECT_CONFIG_FILENAME = '/.docker-arch.yml';
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
     * @var string
     */
    private $tmpBuildDir;

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
        $this->initProject($projectPath);

        $this->tmpBuildDir = $this->projectPath.self::PROJECT_TMP_CONFIG_DIRECTORY;
        $this->fs->remove($this->tmpBuildDir);

        $this->generateMainProjectFiles();
        $this->dumpMainProjectFiles();
        $this->generateAndDumpProjectServicesFiles();

        $projectDir = $this->projectPath.self::PROJECT_CONFIG_DIRECTORY;

        // Backup some resources.
        $dotEnvFilePath = $projectDir.'/.env';
        $initialDotEnvContent = null;
        if (file_exists($dotEnvFilePath)) {
            $initialDotEnvContent = file_get_contents($dotEnvFilePath);
        }

        // Export Docker configuration and clean.
        $this->fs->remove($projectDir);
        $this->fs->mirror($this->tmpBuildDir, $projectDir);
        $this->fs->remove($this->tmpBuildDir);

        // Recreate backuped resources.
        if (null !== $initialDotEnvContent) {
            $this->fs->dumpFile($dotEnvFilePath, $initialDotEnvContent);
        } else {
            $this->fs->dumpFile($dotEnvFilePath, file_get_contents($projectDir.'/.env.dist'));
        }

        $this->generateUI($projectDir);
    }

    /**
     * @param string $projectPath
     *
     * @return void
     */
    private function initProject($projectPath): void
    {
        $this->projectPath = $projectPath;

        try {
            $this->persister = new Persister($this->projectPath);
        } catch (NoPersisterFileException $e) {
            $this->persister = Persister::init($this->projectPath);
        }

        $this->project = (new ProjectRepository($this->persister))->getProject();
        $this->project->setPath($this->projectPath);
    }

    /**
     * @return void
     */
    private function generateMainProjectFiles(): void
    {
        $this->project->addTemplatedFile(new TemplatedFile('.gitignore', 'Base/gitignore.twig'));
        $this->project->addTemplatedFile(new TemplatedFile('.env.dist', 'Base/env.dist.twig'));
        $this->project->addTemplatedFile(new TemplatedFile('Makefile', 'Base/Makefile.twig'));
        $this->project->addTemplatedFile(new TemplatedFile('docker-compose.yml', 'Base/docker-compose.yml.twig'));
        $this->project->addTemplatedFile(new TemplatedFile('do', 'Base/do.twig', [], 0755));
        if (0 < count($this->project->getDockerSynchedServices())) {
            $this->project->addTemplatedFile(new TemplatedFile('docker-sync.yml', 'Base/docker-sync.yml.twig'));
        }
    }

    /**
     * @return void
     */
    private function dumpMainProjectFiles(): void
    {
        foreach ($this->project->getTemplatedFiles() as $templatedFile) {
            $fileContent = $this->getTemplatedFileGenerator()->render($templatedFile->getViewPath(), array_merge(
                ['project' => $this->project],
                $templatedFile->getParameters()
            ));
            $this->fs->dumpFile($this->tmpBuildDir.'/'.$templatedFile->getRemotePath(), $fileContent);
            if ($chmod = $templatedFile->getChmod()) {
                $this->fs->chmod($this->tmpBuildDir.'/'.$templatedFile->getRemotePath(), $chmod);
            }
        }
    }

    /**
     * @return void
     */
    private function generateAndDumpProjectServicesFiles(): void
    {
        foreach ($this->project->getServices() as $service) {
            $serviceTmpBuildDir = sprintf('%s/%s', $this->tmpBuildDir, $service->getIdentifier());

            $service->addTemplatedFile(new TemplatedFile('Dockerfile', 'Base/Service/Dockerfile.twig'));

            foreach ($service->getTemplatedFiles() as $templatedFile) {
                $fileContent = $this->getTemplatedFileGenerator()->render($templatedFile->getViewPath(), array_merge(
                    ['project' => $this->project, 'service' => $service],
                    $templatedFile->getParameters()
                ));
                $this->fs->dumpFile($serviceTmpBuildDir.'/'.$templatedFile->getRemotePath(), $fileContent);
                if ($chmod = $templatedFile->getChmod()) {
                    $this->fs->chmod($serviceTmpBuildDir.'/'.$templatedFile->getRemotePath(), $chmod);
                }
            }
        }
    }

    /**
     * @param string $projectDir
     *
     * @return void
     */
    private function generateUI(string $projectDir): void
    {
        $indexContent = $this->getTemplatedFileGenerator()->render('UI/index.html.twig', [
            'projectDir' => $projectDir,
            'project' => $this->project,
            'dotEnvFileContent' => file_get_contents($projectDir.'/.env'),
        ]);

        $this->fs->dumpFile($projectDir.'/index.html', $indexContent);
    }
}
