<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc5f82e5ab6ffd60e4763748c52e22b47
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Clasp\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Clasp\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc5f82e5ab6ffd60e4763748c52e22b47::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc5f82e5ab6ffd60e4763748c52e22b47::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
