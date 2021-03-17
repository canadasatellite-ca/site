<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Model\Staff;

/**
 * Customer repository.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
use Magestore\Webpos\Helper\Permission;

/**
 * Class StaffManagement
 * @package Magestore\Webpos\Model\Staff
 */
class StaffManagement implements \Magestore\Webpos\Api\Staff\StaffManagementInterface
{
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;
    /**
     * @var \Magestore\Webpos\Model\WebPosSession
     */
    protected $_session;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezone;
    /**
     * @var WebPosSession
     */
    protected $_webPosSession;

    /**
     * @var Permission
     */
    protected $_permissionHelper;

    /**
     * @var \Magestore\Webpos\Model\Staff|StaffFactory
     */
    protected $_staffFactory;

    protected $_sessionManager;


    /**
     * StaffManagement constructor.
     * @param \Magestore\Webpos\Model\WebPosSession $session
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magestore\Webpos\Model\WebPosSession $webPosSession
     * @param \Magestore\Webpos\Model\Staff $staff
     */
    public function __construct(
        \Magestore\Webpos\Model\WebPosSession $session,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magestore\Webpos\Model\Staff\WebPosSession $webPosSession,
        \Magestore\Webpos\Helper\Permission $webposPermission,
        \Magestore\Webpos\Model\Staff\StaffFactory $staff,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Session\SessionManager $sessionManager
    )
    {
        $this->_session = $session;
        $this->_timezone = $timezone;
        $this->_webPosSession = $webPosSession;
        $this->_staffFactory = $staff;
        $this->_permissionHelper = $webposPermission;
        $this->_request = $request;
        $this->_sessionManager = $sessionManager;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Staff\StaffInterface $staff
     * @return string
     */
    public function login($staff)
    {
        $username = $staff->getUsername();
        $password = $staff->getPassword();
        if ($username && $password) {
            try {
                $resultLogin = $this->_permissionHelper->login($username, $password);
                if ($resultLogin != 0) {
                    $data = array();
                    $data['staff_id'] = $resultLogin;
                    $this->_sessionManager->regenerateId();
                    $data['session_id'] = $this->_sessionManager->getSessionId();
                    $data['logged_date'] = strftime('%Y-%m-%d %H:%M:%S', $this->_timezone->scopeTimeStamp());
                    $this->_webPosSession->setData($data);
                    $this->_webPosSession->save();
                    return $data['session_id'];
                } else {
                    return false;
                }

            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
        return false;
    }

    /**
     *
     * @return string
     */
    public function logout()
    {
        $sessionId = $this->_request->getParam('session');
        $sessionLoginCollection = $this->_webPosSession->getCollection()
            ->addFieldToFilter('session_id', $sessionId);
        foreach ($sessionLoginCollection as $sessionLogin) {
            $sessionLogin->delete();
        }
        return true;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Staff\StaffInterface $staff
     * @return string
     */
    public function changepassword($staff)
    {
        $staffModel = $this->_staffFactory->create()->load($this->_permissionHelper->getCurrentUser());
        $result = [];
        if (!$staffModel->getId()) {
            $result['error'] = '401';
            $result['message'] = __('There is no staff!');
            return \Zend_Json::encode($result);
        }
        $staffModel->setDisplayName($staff->getUsername());
        $oldPassword = $staffModel->getPassword();
        if ($staffModel->validatePassword($staff->getOldPassword())) {
            if ($staff->getPassword()) {
                $staffModel->setPassword($staffModel->getEncodedPassword($staff->getPassword()));
            }
        } else {
            $result['error'] = '1';
            $result['message'] = __('Old password is incorrect!');
            return \Zend_Json::encode($result);
        }
        try {
            $staffModel->save();
            $newPassword = $staffModel->getPassword();
            if ($newPassword != $oldPassword) {
                $sessionParam = $this->_request->getParam('session');
                $userSession = $this->_webPosSession->getCollection()
                    ->addFieldToFilter('staff_id', array('eq' => $staffModel->getId()))
                    ->addFieldToFilter('session_id', array('neq' => $sessionParam));
                foreach ($userSession as $session) {
                    $session->delete();
                }
            }
        } catch (\Exception $e) {
            $result['error'] = '1';
            $result['message'] = $e->getMessage();
            return \Zend_Json::encode($result);
        }
        $result['error'] = '0';
        $result['message'] = __('Your account is saved successfully!');
        return \Zend_Json::encode($result);
    }
}