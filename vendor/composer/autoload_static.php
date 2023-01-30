<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit73c2e791ec4e0318120cc2864aa47415
{
    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'Joy2362\\PhpTimezone\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Joy2362\\PhpTimezone\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit73c2e791ec4e0318120cc2864aa47415::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit73c2e791ec4e0318120cc2864aa47415::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit73c2e791ec4e0318120cc2864aa47415::$classMap;

        }, null, ClassLoader::class);
    }
}
