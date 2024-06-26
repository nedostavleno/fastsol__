<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticIniteb1d0f9084edb4e3345d039216e8ba66
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/App',
        ),
    );

    public static $prefixesPsr0 = array (
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticIniteb1d0f9084edb4e3345d039216e8ba66::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticIniteb1d0f9084edb4e3345d039216e8ba66::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticIniteb1d0f9084edb4e3345d039216e8ba66::$prefixesPsr0;
            $loader->classMap = ComposerStaticIniteb1d0f9084edb4e3345d039216e8ba66::$classMap;

        }, null, ClassLoader::class);
    }
}
