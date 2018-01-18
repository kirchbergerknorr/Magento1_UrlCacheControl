<?php
/**
 * @author      Nick Dilßner <nd@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2018 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Kirchbergerknorr_UrlCacheControl_Model_Design_Package extends Mage_Core_Model_Design_Package
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
            $params['_type'] = self::SKIN;
        }

        // <kk: - optimized with getFilename() (contains updateParamDefaults(), but without referenced params - that means all changes happens in parent, except 'type')
        //         and excluded $params['_default'], because updateParamDefaults() also handles that
        $filename = $this->getFilename($file, $params);
        // kk>

        // <kk: - after copied collecting of real file-path is done and after parent processing, add last-modified timestamp as url parameter
        return $this->addModifiedParam(
            parent::getSkinUrl($file, $params),
            $filename
        );
        // kk>
    }

    /**
     * add modified timestamp to url
     *
     * {@inheritdoc}
     */
    public function getMergedCssUrl($files)
    {
        $isSecure = Mage::app()->getRequest()->isSecure();
        // <kk: - really complete different to JS? why is css more secure or complicated then js?
        $mergerDir = $isSecure ? 'css_secure' : 'css';
        // kk>
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

        // <kk: - after copied collecting of real file-path is done and after parent processing, add last-modified timestamp as url parameter
        //        all the other stuff will handle parent method
        $filename = $targetDir . DS . $targetFilename;
        return $this->addModifiedParam(
            parent::getMergedCssUrl($files),
            $filename
        );
        // kk>
    }

    /**
     * add modified timestamp to url
     *
     * {@inheritdoc}
     */
    public function getMergedJsUrl($files)
    {
        $targetFilename = md5(implode(',', $files)) . '.js';
        $targetDir = $this->_initMergerDir('js');
        if (!$targetDir) {
            return '';
        }

        // <kk: - after copied collecting of real file-path is done and after parent processing, add last-modified timestamp as url parameter
        $filename = $targetDir . DS . $targetFilename;
        return $this->addModifiedParam(
            parent::getMergedJsUrl($files),
            $filename
        );
        // kk>
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