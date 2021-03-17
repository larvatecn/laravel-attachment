<?php

return [
    //存储磁盘
    'disk' => 'public',

    //允许上传的文件类型
    'file_types' => [
        'image' => '/^(gif|png|jpe?g|svg|webp)$/i',
        'html' => '/^(htm|html)$/i',
        'office' => '/^(docx?|xlsx?|pptx?|pps|potx?)$/i',
        'gdocs' => '/^(docx?|xlsx?|pptx?|pps|potx?|rtf|ods|odt|pages|ai|dxf|ttf|tiff?|wmf|e?ps)$/i',
        'text' => '/^(txt|md|csv|nfo|ini|json|php|js|css|ts|sql)$/i',
        'video' => '/^(og?|mp4|webm|mp?g|mov|3gp)$/i',
        'audio' => '/^(og?|mp3|mp?g|wav)$/i',
        'pdf' => '/^(pdf)$/i',
        'flash' => '/^(swf)$/i',
    ],

    'directory' => [
        'image' => 'images',
        'media' => 'medias',
        'file' => 'files',
    ],
];