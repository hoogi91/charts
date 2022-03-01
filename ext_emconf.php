<?php
$EM_CONF[$_EXTKEY] = [
    'title'        => 'Charts',
    'description'  => 'Extension to create datasets and show them as line, bar, pie or doughnut chart in frontend',
    'category'     => 'fe',
    'author'       => 'Thorsten Hogenkamp',
    'author_email' => 'thorsten@hogenkamp-bocholt.de',
    'version'      => '2.0.1',
    'state'        => 'stable',
    'constraints'  => [
        'depends'  => [
            'typo3' => '10.4.0-11.5.99',
        ],
        'suggests' => [
            'spreadsheets' => '2.0.0-3.99.99',
        ],
    ],
    'autoload'     => [
        'psr-4' => [
            'Hoogi91\\Charts\\' => 'Classes',
        ],
    ],
];
