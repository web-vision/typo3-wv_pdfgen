<?php
namespace WebVision\WvPdfgen\Utility;

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

/**
 * Utility to ease work with urls.
 *
 * @author Daniel Siepmann <d.siepmann@web-vision.de>
 */
class UrlUtility
{
    /**
     * Check whether the given URL is available (=returns 200).
     *
     * @param string $urlToCheck
     *
     * @return bool
     */
    public function urlAvailable($urlToCheck)
    {
        $responseHeader = array();
        GeneralUtility::getUrl(
            $urlToCheck,
            2,
            array(),
            $responseHeader
        );

        return ($responseHeader['http_code'] === 200);
    }

    /**
     * Remove all parameter from query, except if they are white listed.
     *
     * @param string $urlToFilter The url to filter.
     * @param array $parameterWhitelist The list of parameter which are white listed.
     *
     * @return string
     */
    public function filterUrl($urlToFilter, array $parameterWhitelist)
    {
        $filteredUrl = substr($urlToFilter, 0, strpos($urlToFilter, '?'));
        $parsedUrl = parse_url($urlToFilter);
        if(!isset($parsedUrl['query'])) {
            return $urlToFilter;
        }

        $urlQuery = array_filter(
            explode('&', $parsedUrl['query']),
            function ($queryParameter) use ($parameterWhitelist) {
                list($parameterName) = explode('=', $queryParameter);
                if(!in_array($parameterName, $parameterWhitelist)) {
                    return false;
                }
                return true;
            }
        );

        if(count($urlQuery) > 0) {
            $filteredUrl .= '?' . implode('&', $urlQuery);
        }

        return $filteredUrl;
    }

    /**
     * Get the current active domain, with protocol.
     *
     * @return string
     */
    public function getDomain()
    {
        return GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
    }
}
