<?php
$EM_CONF[$_EXTKEY] = [
    'title'        => 'Charts',
    'description'  => 'Extension to create datasets and show them as line, bar, pie or doughnut chart in frontend',
    'category'     => 'fe',
    'author'       => 'Thorsten Hogenkamp',
    'author_email' => 'thorsten@hogenkamp-bocholt.de',
    'version'      => '1.1.3',
    'state'        => 'stable',
    'constraints'  => [
        'depends'  => [
            'typo3' => '8.7.0-10.9.99',
        ],
        'suggests' => [
            'spreadsheets' => '1.0.0-2.99.99',
        ],
    ],
    'autoload'     => [
        'psr-4' => [
            'Hoogi91\\Charts\\' => 'Classes',
        ],
    ],
];
