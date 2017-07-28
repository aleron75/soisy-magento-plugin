<?php
/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Gennaro Vietri <gennaro.vietri@bitbull.it>
*/
class Bitbull_Soisy_Helper_Log
    extends Mage_Core_Helper_Abstract
    implements Bitbull_Soisy_Log_LoggerInterface
{
    const LOG_FILENAME = 'soisy_payment.log';

    const XML_PATH_FORCE_LOG = 'payment/soisy/force_log';
    const XML_PATH_DEBUG_MODE = 'payment/soisy/debug_mode';

    public function isForceLogEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_FORCE_LOG, $store);
    }

    public function isDebugModeEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DEBUG_MODE, $store);
    }

    /**
     * Retrieve Tooso Log File
     *
     * @return string
     */
    public function getLogFile()
    {
        return self::LOG_FILENAME;
    }

    /**
     * Logging facility
     *
     * @param string $message
     * @param string $level
    */
    public function log($message, $level = null)
    {
        $forceLog = $this->isForceLogEnabled();

        Mage::log($message, $level, $this->getLogFile(), $forceLog);
    }

    public function logException(Exception $e)
    {
        if ($e instanceof Bitbull_Soisy_Exception) {
            $validationMessages = $e->getValidationMessages();

            if ($validationMessages) {
                $this->log($validationMessages, Zend_Log::ERR);
            }
        }

        Mage::logException($e);
    }
    
    /**
     * @param string $message
    */
    public function debug($message)
    {
        $debugMode = $this->isDebugModeEnabled();

        if ($debugMode) {
            $this->log($message, Zend_Log::DEBUG);
        }
    }
}