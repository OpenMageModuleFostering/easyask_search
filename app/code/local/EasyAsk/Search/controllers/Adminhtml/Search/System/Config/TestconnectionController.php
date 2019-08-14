<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

 /**
 * Admin search test connection controller
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class EasyAsk_Search_Adminhtml_Search_System_Config_TestconnectionController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check for connection to server
     */
    public function pingAction()
    {
        Mage::log("EasyAsk_Search_Adminhtml_Search_System_Config_TestconnectionController pingAction ");
        
        if (!isset($_REQUEST['host']) || !($host = $_REQUEST['host'])
            || !isset($_REQUEST['port']) || !($port = (int)$_REQUEST['port'])
            || !isset($_REQUEST['dictionary']) || !($dictionary = $_REQUEST['dictionary'])
        ) {
            echo 0;
            die;
        }

        $pingUrl = 'http://' . $host . ':' . $port . '/EasyAsk/health.jsp?dxp=' . $dictionary;

        Mage::log("EasyAsk_Search_Adminhtml_Search_System_Config_TestconnectionController pingurl is " . $pingUrl);

        $ping = $this->url_get_header($pingUrl);
        
        Mage::log("EasyAsk_Search_Adminhtml_Search_System_Config_TestconnectionController ping is " . $ping);
        
        // result is false if there was a timeout
        // or if the HTTP status was not 200
        if (strpos($ping, '200') === false) {
            echo 0;
        } else {
            echo 1;
        }
    }
    
    function url_get_header ($Url) {
    	if (!function_exists('curl_init')){
    		die('CURL is not installed!');
    	}
    	$ch = curl_init($Url);
    	curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
    	curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    	curl_setopt($ch, CURLOPT_TIMEOUT,10);
    	$output = curl_exec($ch);
    	curl_close($ch);
    	
    	return $output;
    }
}
