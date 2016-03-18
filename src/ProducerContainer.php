<?php
/**
 *
 * This file is part of Producer for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Producer;

use Producer\Repo\RepoInterface;

/**
 *
 * @package producer/producer
 *
 */
class ProducerContainer
{
    protected $homedir;
    protected $workdir;
    protected $stdout;
    protected $stderr;

    public function __construct(
        $homedir,
        $workdir,
        $stdout = STDOUT,
        $stderr = STDERR
    ) {
        $this->homedir = $homedir;
        $this->workdir = $workdir;
        $this->logger = new Stdlog($stdout, $stderr);
    }

    public function newCommand($name)
    {
        $class = "Producer\Command\\" . ucfirst($name);
        if (! class_exists($class)) {
            throw new Exception("Command '$name' not found.");
        }

        $homefs = $this->newFsio($this->homedir);
        $config = $this->newConfig($homefs);
        $workfs = $this->newFsio($this->workdir);
        $repo = $this->newRepo($workfs);
        $api = $this->newApi($repo->getOrigin(), $config);

        return new $class($this->logger, $repo, $api);
    }

    protected function newFsio($dir)
    {
        return new Fsio($dir);
    }

    protected function newConfig(Fsio $fsio)
    {
        return new Config($fsio);
    }

    public function newRepo($fsio)
    {
        if ($fsio->isDir('.git')) {
            return new Repo\Git($fsio, $this->logger);
        };

        if ($fsio->isDir('.hg')) {
            return new Repo\Hg($fsio, $this->logger);
        }

        throw new Exception("Could not find .git or .hg files.");
    }

    public function newApi($origin, $config)
    {
        switch (true) {
            case (strpos($origin, 'github.com') !== false):
                return $this->newApiGithub($origin, $config);
            case (strpos($origin, 'gitlab.com') !== false):
                return $this->newApiGitlab($origin, $config);
            case (strpos($origin, 'bitbucket.org') !== false):
                return $this->newApiBitbucket($origin, $config);
            default:
                throw new Exception("Producer will not work with {$origin}.");
        }
    }

    protected function newApiGithub($origin, Config $config)
    {
        return new Api\Github(
            $origin,
            $config->get('github_username'),
            $config->get('github_token')
        );
    }

    protected function newApiGitlab($origin, Config $config)
    {
        return new Api\Gitlab(
            $origin,
            $config->get('gitlab_token')
        );
    }

    protected function newApiBitbucket($origin, Config $config)
    {
        return new Api\Bitbucket(
            $origin,
            $config->get('bitbucket_username'),
            $config->get('bitbucket_password')
        );
    }
}
