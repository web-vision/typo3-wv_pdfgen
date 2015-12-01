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

use WebVision\WvPdfgen\Utility\UrlUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * This class will generate PDFs based of the current URL.
 *
 * @author Daniel Siepmann <d.siepmann@web-vision.de>
 * @author Justus Moroni <j.moroni@web-vision.de>
 */
class Pdf
{
    const CLI_PARAMETERS_KEY = 'cliParameters';

    /**
     * The configuration passed by TS.
     * @var array
     */
    protected $configuration = array();

    /**
     * The ContentObjectRenderer from TYPO3 which is added automatically.
     * @var ContentObjectRenderer
     */
    public $cObj;

    /**
     * @var UrlUtility
     */
    protected $urlUtility;

    /**
     * Userfunction for TYPO3. Which will generate the PDF for the current url
     * and take $conf into account.
     *
     * Will create an HTTP redirect instead pointing to the generated PDF.
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
        $this->urlUtility = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager')
            ->get('WebVision\WvPdfgen\Utility\UrlUtility');
        $this->processConfiguration($conf);

        if(!$this->urlUtility->urlAvailable($this->getUrlForGeneration())) {
            $GLOBALS['TSFE']->pageNotFoundAndExit();
        }
        $this->generatePdf();

        // Redirect to PDF in file system
        HttpUtility::redirect(
            $this->getPdfUrl(),
            HttpUtility::HTTP_STATUS_301
        );
    }

    /**
     * Generate the PDF and persist it in Filesystem
     *
     * @return Pdf
     */
    protected function generatePdf()
    {
        $command = $this->configuration['binary'] .
            $this->getCliParameter() .
            ' ' . $this->getUrlForGeneration() .
            ' ' . $this->getFileName();

        exec(escapeshellcmd($command));

        return $this;
    }

    /**
     * Get the file name to use for the pdf.
     *
     * @return string The full absolute path to the file.
     */
    protected function getFileName()
    {
        $folderPath = PATH_site . 'typo3temp/gen_pdfs/';
        $fileName = md5($this->getUrlForGeneration()) . '.pdf';

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
        return $this->urlUtility->getDomain() . str_replace(PATH_site, '', $this->getFileName());
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
        $urlToConvert = $this->urlUtility->filterUrl(
            $this->urlUtility->getDomain() . $GLOBALS['TSFE']->siteScript,
            $this->configuration['parameterWhitelist']
        );

        // Remove type parameter to generate the real url, not the PDF (= endless loop)
        $urlToConvert = str_ireplace('type=' . $this->configuration['typeNum'], '', $urlToConvert);

        // Remove url extension like ".pdf" as it's realurl rewriting the type.
        return str_ireplace($this->configuration['urlExtension'], '', $urlToConvert);
    }

    /**
     * Get cli parameter to include while PDF generation.
     *
     * @return string
     */
    protected function getCliParameter()
    {
        $cliParameter = '';

        foreach ($this->configuration[static::CLI_PARAMETERS_KEY] as $key => $value) {
            $cliParameter .= ' --' . $key . ' ' . $value;
        }

        return $cliParameter;
    }

    /**
     * Will parse the given configuration and save the processed configuration.
     *
     * Empty options will be ignored.
     *
     * @param array $configuration The original configuration passed in.
     *
     * @return Pdf
     */
    protected function processConfiguration(array $configuration)
    {
        $configuration = array_filter($configuration);

        // Process only the 1st level of configuration
        foreach ($configuration as $key => $value) {
            if (strpos($key, '.')) {
                continue;
            }

            $this->configuration[$key] = $value;

            if($key === 'parameterWhitelist') {
                $this->configuration[$key] = GeneralUtility::trimExplode(',', $value);
            }
        }

        // Process only the cli parameter configuration
        $this->configuration[static::CLI_PARAMETERS_KEY] = array();
        foreach ((array) $configuration[static::CLI_PARAMETERS_KEY . '.'] as $key => $value) {
            // Don't process sub array with further configuration, this is done
            // by stdWrap
            if (strpos($key, '.') === (strlen($key) - 1)) {
                continue;
            }

            $this->configuration[static::CLI_PARAMETERS_KEY][$key] = $value;

            // Process stdWrap if sub array exists.
            if (isset($configuration[$key . '.']) && is_array($configuration[$key . '.'])) {
                $this->configuration[static::CLI_PARAMETERS_KEY][$key] = trim(
                    $this->cObj->stdWrap($configuration[$key], $configuration[$key . '.'])
                );
            }
        }

        return $this;
    }
}
