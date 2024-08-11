<?php
$EM_CONF[$_EXTKEY] = [
    'title'        => 'Charts',
    'description'  => 'Extension to create datasets and show them as line, bar, pie or doughnut chart in frontend',
    'category'     => 'fe',
    'author'       => 'Thorsten Hogenkamp',
    'author_email' => 'thorsten@hogenkamp-bocholt.de',
    'version'      => '3.0.2',
    'state'        => 'stable',
    'constraints'  => [
        'depends'  => [
            'typo3' => '11.5.0-12.4.99',
        ],
        'suggests' => [
            'spreadsheets' => '3.3.0-4.99.99',
        ],
    ],
    'autoload'     => [
        'psr-4' => [
            'Hoogi91\\Charts\\' => 'Classes',
        ],
    ],
];
