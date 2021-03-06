<?php


namespace Box\Mod\Client\Api;


class ClientTest extends \PHPUnit_Framework_TestCase {

    public function testgetDi()
    {
        $di = new \Box_Di();
        $client = new \Box\Mod\Client\Api\Client();
        $client->setDi($di);
        $getDi = $client->getDi();
        $this->assertEquals($di, $getDi);
    }

    public function testbalance_get_list()
    {
        $data = array();

        $model = new \Model_Client();
        $model->loadBean(new \RedBeanPHP\OODBBean());

        $serviceMock = $this->getMockBuilder('\Box\Mod\Client\ServiceBalance')->getMock();
        $serviceMock->expects($this->atLeastOnce())
            ->method('getSearchQuery')
            ->will($this->returnValue(array('sql', array())));

        $simpleResultArr = array(
            'list' => array(
                array('id' => 1),
            ),
        );

        $pagerMock = $this->getMockBuilder('\Box_Pagination')->disableOriginalConstructor()->getMock();
        $pagerMock ->expects($this->atLeastOnce())
            ->method('getSimpleResultSet')
            ->will($this->returnValue($simpleResultArr));

        $model = new \Model_ClientBalance();
        $model->loadBean(new \RedBeanPHP\OODBBean());
        $dbMock = $this->getMockBuilder('\Box_Database')->getMock();
        $dbMock->expects($this->atLeastOnce())
            ->method('getExistingModelById')
            ->will($this->returnValue($model));

        $di = new \Box_Di();
        $di['mod_service'] = $di->protect(function ($name) use($serviceMock) {return $serviceMock;});
        $di['pager'] = $pagerMock;
        $di['db'] = $dbMock;

        $client = new \Box\Mod\Client\Api\Client();
        $client->setDi($di);
        $client->setService($serviceMock);
        $client->setIdentity($model);

        $result = $client->balance_get_list($data);

        $this->assertInternalType('array', $result);
    }

    public function testchange_password_PasswordRequired()
    {
        $client = new \Box\Mod\Client\Api\Client();

        $data = array();

        $this->setExpectedException('\Box_Exception', 'Password required');
        $client->change_password($data);
    }

    public function testchange_password_PasswordConfirmationRequired()
    {
        $client = new \Box\Mod\Client\Api\Client();

        $data = array(
            'password' => '1234'
        );

        $this->setExpectedException('\Box_Exception', 'Password confirmation required');
        $client->change_password($data);
    }

    public function testchange_password_PasswordDoNotMatch()
    {
        $client = new \Box\Mod\Client\Api\Client();

        $data = array(
            'password' => '1234',
            'password_confirm' => '1234567'
        );

        $this->setExpectedException('\Box_Exception', 'Passwords do not match.');
        $client->change_password($data);
    }

    public function testupdate()
    {
        $data = array(
            'id'             => 1,
            'first_name'     => 'John',
            'last_name'      => 'Smith',
            'aid'            => '0',
            'gender'         => 'male',
            'birthday'       => '1999-01-01',
            'company'        => 'LTD Testing',
            'company_vat'    => 'VAT0007',
            'address_1'      => 'United States',
            'address_2'      => 'Utah',
            'phone_cc'       => '+1',
            'phone'          => '555-345-345',
            'document_type'  => 'doc',
            'document_nr'    => '1',
            'notes'          => 'none',
            'country'        => 'Moon',
            'postcode'       => 'IL-11123',
            'city'           => 'Chicaco',
            'state'          => 'IL',
            'currency'       => 'USD',
            'tax_exempt'     => 'n/a',
            'created_at'     => '2012-05-10',
            'email'          => 'test@example.com',
            'group_id'       => 1,
            'status'         => 'test status',
            'company_number' => '1234',
            'type'           => '',
            'lang'           => 'en',
            'custom_1'       => '',
            'custom_2'       => '',
            'custom_3'       => '',
            'custom_4'       => '',
            'custom_5'       => '',
            'custom_6'       => '',
            'custom_7'       => '',
            'custom_8'       => '',
            'custom_9'       => '',
            'custom_10'      => '',
        );

        $model = new \Model_Client();
        $model->loadBean(new \RedBeanPHP\OODBBean());

        $dbMock = $this->getMockBuilder('\Box_Database')->getMock();
        $dbMock->expects($this->atLeastOnce())
            ->method('store')->will($this->returnValue(1));

        $serviceMock = $this->getMockBuilder('\Box\Mod\Client\Service')->getMock();
        $serviceMock->expects($this->atLeastOnce())->
        method('emailAreadyRegistered')->will($this->returnValue(false));

        $eventMock = $this->getMockBuilder('\Box_EventManager')->getMock();
        $eventMock->expects($this->atLeastOnce())->
        method('fire');

        $validatorMock = $this->getMockBuilder('\Box_Validate')->getMock();
        $validatorMock->expects($this->atLeastOnce())->method('isEmailValid');

        $boxModMock = $this->getMockBuilder('\Box_Mod')->disableOriginalConstructor()->getMock();
        $boxModMock->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn(array());

        $di = new \Box_Di();
        $di['db'] = $dbMock;
        $di['mod'] = $di->protect(function ($name) use($boxModMock) {return $boxModMock;});
        $di['events_manager'] = $eventMock;
        $di['validator'] = $validatorMock;
        $di['logger'] = new \Box_Log();

        $api = new \Box\Mod\Client\Api\Client();
        $api->setDi($di);
        $api->setIdentity($model);
        $api->setService($serviceMock);
        $result = $api->update($data);
        $this->assertTrue($result);
    }
}
 