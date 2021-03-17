<?php

/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Api\Staff;

interface StaffManagementInterface
{

    /**
     * @param \Magestore\Webpos\Api\Data\Staff\StaffInterface $staff
     * @return string
     */
    public function login($staff);
    /**
     * 
     * @return string
     */
    public function logout();

    /**
     * @param \Magestore\Webpos\Api\Data\Staff\StaffInterface $staff
     * @return string
     */
    public function changepassword($staff);

}
