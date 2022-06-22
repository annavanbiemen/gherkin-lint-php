<?php

namespace DTL\BehatLint\Model;

use FilesystemIterator;
use GlobIterator;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

class FeatureFinder
{
    public function __construct(private string $cwd)
    {
    }

    /**
     * @return array<int,FeatureFile>
     */
    public function find(string $path): array
    {
        $path = $this->makeAbsolute($path);

        if (!file_exists($path)) {
            throw new RuntimeException(sprintf(
                'Provided path "%s" does not exist',
                $path
            ));
        }

        if (is_file($path)) {
            return [
                new FeatureFile(
                    $path,
                    Path::makeRelative($path, $this->cwd)
                )
            ];
        }
        $finder = new Finder();
        $finder->in($path)->name('*.feature');

        foreach ($finder as $info) {
        if (!$info instanceof SplFileInfo) {
            continue;
        }
        $files[] = new FeatureFile(
            $info->getPathname(),
            Path::makeRelative($info->getPathname(), $this->cwd)
        );
        }

        return $files;
    }

    private function makeAbsolute(string $path): string
    {
        return Path::makeAbsolute($path, $this->cwd);
    }
}