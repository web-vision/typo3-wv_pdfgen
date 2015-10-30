<?php
$EM_CONF[$_EXTKEY] = array (
    'title' => 'PDF generation for pages',
    'description' => 'Will generate a PDF version of any page using wkhtmltopdf.',
    'category' => 'frontend',
    'version' => '1.0.0',
    'state' => 'beta',
    'createDirs' => '',
    'clearcacheonload' => false,
    'author' => 'Daniel Siepmann, Justus Moroni',
    'author_email' => 'd.siepmann@web-vision.de, j.moroni@web-vision.de',
    'author_company' => 'web-vision GmbH',
    'constraints' => array (
        'depends' => array (
            'typo3' => '6.0.0-7.9.99',
        ),
        'conflicts' => array (
        ),
        'suggests' => array (
        ),
    ),
);
