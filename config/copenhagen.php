<?php

$uploadFolderPath = '/uploads';
$uploadPath = public_path() . $uploadFolderPath;

return array(
    'uploadPath' => $uploadPath,
    'rooms' => array(
        'path' => $uploadPath. '/rooms',
        'url' => $uploadFolderPath . '/rooms',
        'image' => array(
            'sizes' => array(
                'small' => array(
                    'width' => 82, 
                    'height' => 82
                ),
                'thumbs' => array(
                    'width' => 200, 
                    'height' => 200
                ),
                'large' => array(
                    'width' => 555,  
                    'height' =>370
                )
            )
        )
    )
);