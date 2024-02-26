<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\ReferableFinder;

use PerfectDrive\Referable\Interfaces\ReferableInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;

/**
 * This class is based on the ModelFinder class from the Spatie package laravel-model-info
 *
 * @copyright Spatie: https://github.com/spatie/laravel-model-info
 */
class ReferableFinder
{
    /**
     * @return Collection<int, class-string<object>>
     */
    public static function all(
        ?string $directory = null,
        ?string $basePath = null,
        ?string $baseNamespace = null,
    ): Collection {
        $directory ??= app_path();
        $basePath ??= base_path();
        $baseNamespace ??= '';

        return collect(static::getFilesRecursively($directory))
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
     * @return array<int, string>
     */
    protected static function getFilesRecursively(string $path): array
    {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $files = [];

        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }
            $files[] = $file->getPathname();
        }

        return $files;
    }
}
