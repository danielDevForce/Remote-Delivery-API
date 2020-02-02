<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5409acd78a29cf68a51a71b0a3f1ac78
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Automattic\\WooCommerce\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Automattic\\WooCommerce\\' => 
        array (
            0 => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5409acd78a29cf68a51a71b0a3f1ac78::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5409acd78a29cf68a51a71b0a3f1ac78::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}