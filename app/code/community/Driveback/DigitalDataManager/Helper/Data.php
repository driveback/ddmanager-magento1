<?php

class Driveback_DigitalDataManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_DIGITAL_DATA_MANAGER_ENABLED = 'driveback_ddm/settings/enabled';
    const XML_PATH_DIGITAL_DATA_PROJECT_ID      = 'driveback_ddm/settings/project_id';

    /**
     * @return bool
     */
    public function digitalDataManagerEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DIGITAL_DATA_MANAGER_ENABLED);
    }

    /**
     * @return null|string
     */
    public function getProjectId()
    {
        return Mage::getStoreConfig(self::XML_PATH_DIGITAL_DATA_PROJECT_ID);
    }

    /**
     * @return Driveback_DigitalDataManager_Model_Ddl
     */
    public function getDdl()
    {
        return Mage::getSingleton('driveback_ddm/ddl');
    }

    /**
     * @return string
     */
    public function getRequestPath()
    {
        $r = Mage::app()->getRequest();
        $path = array($r->getRouteName(), $r->getControllerName(), $r->getActionName());
        return implode('_', $path);
    }

    /**
     * @return bool
     */
    public function isHomePage()
    {
        return 'cms_index_index' == $this->getRequestPath();
    }

    /**
     * @return bool
     */
    public function isContentPage()
    {
        return 'cms_page_view' == $this->getRequestPath();
    }

    /**
     * @return bool
     */
    public function isCategoryPage()
    {
        return 'catalog_category_view' == $this->getRequestPath();
    }

    /**
     * @return bool
     */
    public function isSearchPage()
    {
        return 'catalogsearch_advanced_result' == $this->getRequestPath()
        || 'catalogsearch_result_index' == $this->getRequestPath();
    }

    /**
     * @return bool
     */
    public function isProductPage()
    {
        $p = Mage::registry('current_product');
        return $p instanceof Mage_Catalog_Model_Product && $p->getId();
    }

    /**
     * @return bool
     */
    public function isCartPage()
    {
        return 'checkout_cart_index' == $this->getRequestPath();
    }

    /**
     * @return bool
     */
    public function isCheckoutPage()
    {
        $r = Mage::app()->getRequest();
        return false !== strpos($r->getRouteName(), 'checkout') && 'success' != $r->getActionName();
    }

    public function isConfirmationPage()
    {
        /*
         * default controllerName is "onepage"
         * relax the check, only check if contains checkout
         * somecheckout systems has different prefix/postfix,
         * but all contains checkout
         */
        $r = Mage::app()->getRequest();
        return false !== strpos($r->getRouteName(), 'checkout') && 'success' == $r->getActionName();
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return bool
     */
    public function hasCustomerTransacted(Mage_Customer_Model_Customer $customer)
    {
        if (!$customer->getId()) {
            return false;
        }

        /** @var Mage_Core_Model_Resource $r */
        $r = Mage::getSingleton('core/resource');

        $read = $r->getConnection('core_read');
        $select = $read->select()
            ->from($r->getTableName('sales/order'), array('c' => new Zend_Db_Expr('COUNT(*)')))
            ->where('customer_id = ?', $customer->getId());

        return $read->fetchOne($select) > 0;
    }
}