<?php
/**
 * @author      Nick Dilßner <nd@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2017 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Kirchbergerknorr_UrlCacheControl_Mage_Core_Model_Design_Package extends Mage_Core_Model_Design_Package
{
    /**
     * parameter _type
     */
    const SKIN = 'skin';

    /**
     * add modified timestamp to url
     *
     * {@inheritdoc}
     */
    public function getSkinUrl($file = null, array $params = array())
    {
        if (empty($params['_type'])) {
            // not sure if this is really the best, because that means if i would set a type, this method also get other url's the skin
            $params['_type'] = self::SKIN;
        }

        $this->updateParamDefaults($params);
        $filename = $this->getFilename($file, $params);

        return $this->addModifiedParam(
            parent::getSkinUrl($file, $params),
            $filename
        );
    }

    /**
     * add modified timestamp to url
     *
     * {@inheritdoc}
     */
    public function getMergedCssUrl($files)
    {
        // <COPY - really complete different to JS? why is css more secure or complicated then js?
        $isSecure = Mage::app()->getRequest()->isSecure();
        $mergerDir = $isSecure ? 'css_secure' : 'css';
        $targetDir = $this->_initMergerDir($mergerDir);
        if (!$targetDir) {
            return '';
        }

        // base hostname & port
        $baseMediaUrl = Mage::getBaseUrl('media', $isSecure);
        $hostname = parse_url($baseMediaUrl, PHP_URL_HOST);
        $port = parse_url($baseMediaUrl, PHP_URL_PORT);
        if (false === $port) {
            $port = $isSecure ? 443 : 80;
        }

        // merge into target file
        $targetFilename = md5(implode(',', $files) . "|{$hostname}|{$port}") . '.css';
        // COPY>

        $filename = $targetDir . DS . $targetFilename;
        return $this->addModifiedParam(
            parent::getMergedCssUrl($files),
            $filename
        );
    }

    /**
     * add modified timestamp to url
     *
     * {@inheritdoc}
     */
    public function getMergedJsUrl($files)
    {
        // <COPY
        $targetFilename = md5(implode(',', $files)) . '.js';
        $targetDir = $this->_initMergerDir('js');
        if (!$targetDir) {
            return '';
        }
        // COPY>

        $filename = $targetDir . DS . $targetFilename;
        return $this->addModifiedParam(
            parent::getMergedJsUrl($files),
            $filename
        );
    }

    /**
     * add modified timestamp of existing file to url
     *
     * @param   string  $url        url to add
     * @param   string  $filename   path to file of url
     *
     * @return  string              url with modified timestamp of file, if exists
     */
    protected function addModifiedParam($url, $filename)
    {
        // TODO: validate if filemtime is also usable with url and if this is not calling the web-server
        // file_exists and filemtime uses the same cache, so it is better to validate instead of using @filemtime
        if (file_exists($filename) && is_file($filename) && ($time = filemtime($filename)) !== false) {
            return $url . ((strpos($url, '?') === false) ? '?' : '&' ) . $time;
        }
        return $url;
    }
}