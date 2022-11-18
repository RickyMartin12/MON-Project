<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita6df77e260244bbbbec954406c5b71e3
{
    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'PHPExcel' => 
            array (
                0 => __DIR__ . '/..' . '/phpoffice/phpexcel/Classes',
            ),
        ),
        'I' => 
        array (
            'Imagick' => 
            array (
                0 => __DIR__ . '/..' . '/calcinai/php-imagick/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInita6df77e260244bbbbec954406c5b71e3::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
