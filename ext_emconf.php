<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'PDF generation for pages',
    'description' => 'Will generate a PDF version of any page using wkhtmltopdf.',
    'category' => 'frontend',
    'version' => '1.0.2',
    'state' => 'beta',
    'clearcacheonload' => false,
    'author' => 'Daniel Siepmann, Justus Moroni',
    'author_email' => 'd.siepmann@web-vision.de, j.moroni@web-vision.de',
    'author_company' => 'web-vision GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '6.0.0-7.9.99',
        ],
    ],
];
