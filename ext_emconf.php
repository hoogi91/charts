<?php
$EM_CONF[$_EXTKEY] = [
    'title'        => 'Charts',
    'description'  => 'Extension to create datasets and show them as line, bar, pie or doughnut chart in frontend',
    'category'     => 'fe',
    'author'       => 'Thorsten Hogenkamp',
    'author_email' => 'hoogi20@googlemail.com',
    'version'      => '1.0.1',
    'state'        => 'stable',
    'constraints'  => [
        'depends'  => [
            'typo3' => '8.7.0-9.99.99',
        ],
        'suggests' => [
            'spreadsheets' => '1.0.0-1.99.99',
        ],
    ],
    'autoload'     => [
        'psr-4' => [
            'Hoogi91\\Charts\\' => 'Classes',
        ],
    ],
];
