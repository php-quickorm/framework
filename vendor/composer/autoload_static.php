<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3dcce667708e66b2aabbed3c392db095
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Model\\' => 6,
        ),
        'C' => 
        array (
            'Controller\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Model\\' => 
        array (
            0 => __DIR__ . '/../..' . '/App/Model',
        ),
        'Controller\\' => 
        array (
            0 => __DIR__ . '/../..' . '/App/Controller',
        ),
    );

    public static $classMap = array (
        'System\\Database' => __DIR__ . '/../..' . '/App/System/Database.php',
        'System\\DatabaseDriver\\pdo_mysql' => __DIR__ . '/../..' . '/App/System/DatabaseDriver/pdo_mysql.php',
        'System\\Model' => __DIR__ . '/../..' . '/App/System/Model.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3dcce667708e66b2aabbed3c392db095::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3dcce667708e66b2aabbed3c392db095::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3dcce667708e66b2aabbed3c392db095::$classMap;

        }, null, ClassLoader::class);
    }
}
