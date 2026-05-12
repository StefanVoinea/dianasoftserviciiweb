<?php

return array(


    'pdf' => array(
        'enabled' => true,
        // 'binary'  => '/usr/local/bin/wkhtmltopdf',
                // 'binary'  => '/home/forge/wkhtmltox/bin/wkhtmltopdf',
                //  'binary'   =>base_path('vendor\wemersonjanuario\wkhtmltopdf-windows\bin\64bit'),
                //'binary'  => '/usr/bin/xvfb-run -a -s "-screen 0 1024x768x24" /usr/local/bin/wkhtmltopdf "$@"',
                //'binary' => '/tmp/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64',
                //  'binary' => '/usr/bin/xvfb-run -a -s "-screen 0 1024x768x24" /tmp/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64  "$@"',
         	    //'binary' => '/usr/local/bin/wkhtmltopdf-amd64',
        'binary' => '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf"',
        'timeout' => 3600,
        'options' => ['encoding'=>'UTF-8'],
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        // 'binary'  => '/usr/bin/xvfb-run -a -s "-screen 0 1024x768x24" /tmp/vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64 "$@"',
        'binary'  => '"C:\Program Files\wkhtmltopdf\bin\wkhtmltoimage"',
                //'binary' => '/home/forge/wkhtmltox/bin/wkhtmltoimage',
                // 'binary' => '/tmp/vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64',
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),


);