<?php

namespace Uss\Component\Kernel\System;

class ResourcePathMapper
{
    const HEAD_RESOURCE = 'head_resource';
    const HEAD_JAVASCRIPT = 'head_javascript';
    const BODY_JAVASCRIPT = 'body_javascript';

    const BLOCK_VENDORS = [

        self::HEAD_RESOURCE => [
            'bootstrap' => 'css/bootstrap.min.css',
            'bootstrap-icon' => 'vendor/bootstrap-icons/bootstrap-icons.min.css',
            'animate' => 'css/animate.min.css',
            'glightbox' => "vendor/glightbox/glightbox.min.css",
            'toastify' => 'vendor/toastify/toastify.min.css',
            'font-size' => "css/font-size.min.css",
            'main-css' => 'css/main.css'
        ],

        self::BODY_JAVASCRIPT => [
            'vue' => 'js/vue.global.js',
            'jquery' => 'js/jquery-3.7.1.min.js',
            'bootstrap-bundle' => 'js/bootstrap.bundle.min.js',
            'bootbox' => 'js/bootbox.all.min.js',
            'glightbox' => "vendor/glightbox/glightbox.min.js",
            'toastify' => 'vendor/toastify/toastify-js.js',
            'notiflix-loading' => 'vendor/notiflix/notiflix-loading-aio-3.2.6.min.js',
            'notiflix-block' => 'vendor/notiflix/notiflix-block-aio-3.2.6.min.js',
            'main-js' => 'js/main.js'
        ]

    ];
}