<?php
namespace WebVision\WvPdfgen\Generator;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

/**
 * This class will generate PDFs based of the current URL.
 *
 * @author Daniel Siepmann <d.siepmann@web-vision.de>
 */
class Pdf
{
    /**
     * The configuration passed by TS.
     * @var array
     */
    protected $configuration = array();

    /**
     * Userfunction for TYPO3.
     *
     * Will generate the PDF for the current url and take $conf into account.
     *
     * @see readme.md
     *
     * @param string $content API by TYPO3, ignored by this function.
     * @param array $conf The configuration for this script. @see readme.md
     *
     * @return void
     */
    public function main($content, array $conf)
    {
        $this->configuration = $conf;

        // Generate PDF
        $this->generatePdf();

        // Redirect to PDF in file system?
        HttpUtility::redirect(
            $this->getPdfUrl(),
            HttpUtility::HTTP_STATUS_301
        );
    }

    /**
     * Generate the PDF and persist it in Filesystem
     *
     * @return WebVision\WvPdfgen\Generator\Pdf The current instance for chaining.
     */
    protected function generatePdf()
    {
        // Build command
        $command = escapeshellcmd($this->configuration['binary']) .
            ' ' . escapeshellarg($this->getUrlForGeneration()) .
            ' ' . escapeshellarg($this->getFileName());

        // Execute command
        exec($command);

        return $this;
    }

    /**
     * Get the file name to use for the pdf.
     *
     * @return string The full absolute path to the file.
     */
    protected function getFileName()
    {
        // Define / Generate paths
        $folderPath = PATH_site . 'typo3temp/gen_pdfs/';
        $fileName = md5($this->getUrlForGeneration()) . '.pdf';

        // Create folder if it doesn't exist yet. (=> TEMP folder)
        GeneralUtility::mkdir_deep($folderPath);

        return $folderPath . $fileName;
    }

    /**
     * Get url to the generated PDF.
     *
     * E.g. for redirect.
     *
     * @return string The url.
     */
    protected function getPdfUrl()
    {
        return $this->getDomain() . str_replace(PATH_site, '', $this->getFileName());
    }

    /**
     * Get url to use for PDF generation.
     *
     * This url will be generated as PDF.
     *
     * @return string The url
     */
    protected function getUrlForGeneration()
    {
        $urlToConvert = $this->getDomain() . $GLOBALS['TSFE']->siteScript;

        // Remove type parameter to generate the real url, not the PDF (= endless loop)
        $urlToConvert = str_ireplace('type=' . $this->configuration['typeNum'], '', $urlToConvert);

        // Remove url extension like ".pdf" as it's realurl rewriting the type.
        return str_ireplace($this->configuration['urlExtension'], '', $urlToConvert);
    }

    /**
     * Get the current active domain, with protocol.
     *
     * @return string
     */
    protected function getDomain()
    {
        return GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
    }
}
