<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\ReferableFinder;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PerfectDrive\Referable\Interfaces\ReferableInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;

/**
 * This class is based on the ModelFinder class from the Spatie package: laravel-model-info
 *
 * @copyright Spatie: https://github.com/spatie/laravel-model-info
 */
class ReferableFinder
{
    /**
     * @param  array<int, string>|null  $directories
     * @return Collection<int, class-string<object>>
     */
    public static function all(
        ?array $directories = null,
        ?string $basePath = null,
        ?string $baseNamespace = null,
    ): Collection {
        $directories ??= config('referable.directories', [app_path()]);
        $basePath ??= config('referable.base_path', base_path());
        $baseNamespace ??= config('referable.base_namespace', '');

        if (! is_array($directories)) {
            $directories = [app_path()];
        }

        if (! is_string($basePath)) {
            $basePath = base_path();
        }

        if (! is_string($baseNamespace)) {
            $baseNamespace = '';
        }

        return collect(static::getFilesRecursively($directories))
            ->map(fn (string $class) => new SplFileInfo($class))
            ->map(fn (SplFileInfo $file) => self::fullQualifiedClassNameFromFile($file, $basePath, $baseNamespace))
            ->map(function (string $class) {
                if (! class_exists($class)) {
                    return null;
                }

                return new ReflectionClass($class);
            })
            ->filter()
            ->filter(fn (ReflectionClass $class) => ! $class->isAbstract())
            ->filter(fn (ReflectionClass $class) => $class->implementsInterface(ReferableInterface::class))
            ->map(fn (ReflectionClass $reflectionClass) => $reflectionClass->getName())
            ->values();
    }

    protected static function fullQualifiedClassNameFromFile(
        SplFileInfo $file,
        string $basePath,
        string $baseNamespace
    ): string {
        return Str::of($file->getRealPath())
            ->replaceFirst($basePath, '')
            ->replaceLast('.php', '')
            ->trim(DIRECTORY_SEPARATOR)
            ->ucfirst()
            ->replace(
                [DIRECTORY_SEPARATOR, 'App\\'],
                ['\\', app()->getNamespace()],
            )
            ->prepend($baseNamespace.'\\')
            ->toString();
    }

    /**
     * @param  array<int, string>  $paths
     * @return array<int, string>
     */
    protected static function getFilesRecursively(array $paths): array
    {
        $files = [];

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

            foreach ($rii as $file) {
                if (! $file instanceof SplFileInfo || $file->isDir()) {
                    continue;
                }

                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
