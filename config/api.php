<?php

return [
    // 阿里云短信
    'aliSMS' => [
        // 开启阿里云短信
        'isopen' => false,
        'accessKeyId' => '',
        'accessSecret' => '',
        'regionId' => 'cn-hangzhou',
        'product' => 'Dysmsapi',
        'version' => '2017-05-25',
        'SignName' => '',
        'TemplateCode' => '',
        'expire' => 60
    ],
    // token的过期时间， 如果为 0 则代表永不失效，单位是秒
    'token_expire' => 0,
];