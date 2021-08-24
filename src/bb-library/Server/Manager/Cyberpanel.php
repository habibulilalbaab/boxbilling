<?php
/**
 * BoxBilling
 *
 * @copyright BoxBilling, Inc (http://www.boxbilling.com)
 * @license   Apache-2.0
 *
 * Copyright BoxBilling, Inc
 * This source file is subject to the Apache-2.0 License that is bundled
 * with this source code in the file LICENSE
 */

    class Server_Manager_Cyberpanel extends Server_Manager
    {
    /**
     * Method is called just after obejct contruct is complete.
     * Add required parameters checks here. 
     */
	public function init()
    {
		if (!extension_loaded('curl')) {
			throw new Server_Exception('cURL extension is not enabled');
		}

		if(empty($this->_config['ip'])) {
			throw new Server_Exception('Server manager "Cyberpanel" is not configured properly. IP address is not set!');
		}

		if(empty($this->_config['host'])) {
			throw new Server_Exception('Server manager "Cyberpanel" is not configured properly. Hostname is not set!');
		}

		if(!$this->_config['secure']){
			/**
			 * CWP will simply return an error if you try to connect via a HTTP connection
			 */
			throw new Server_Exception('Server manager "Cyberpanel" is not configured properly. Cyberpanel API will only accept a secure connection!');
		}
		/**
		 * Default API port. Not sure if it can be overridden, but I've opted to leave the option anyways.
		 */
		if(empty($this->_config['port'])){
			$this->_config['port'] = '8090';
		}
	}

    /**
     * Return server manager parameters.
     * @return type 
     */
    public static function getForm()
    {
        return array(
            'label'     =>  'Cyberpanel',
        );
    }

    /**
     * Returns link to account management page
     * 
     * @return string 
     */
    public function getLoginUrl()
    {
		$host = $this->_config['host'];
		return 'https://'.$host.':8090';
    }

    /**
     * Returns link to reseller account management
     * @return string 
     */
    public function getResellerLoginUrl()
    {
		$host = $this->_config['host'];
		return 'https://'.$host.':8090';
    }

    /**
     * This method is called to check if configuration is correct
     * and class can connect to server
     * 
     * @return boolean 
     */
    public function testConnection()
    {
		$data = array(
			'adminUser'  => $this->_config['username'],
			'adminPass'  => $this->_config['password'],
		);
        $ch = curl_init();
        
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, 'https://'.$this->_config['host'].':'.$this->_config['port'].'/api/verifyConn');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        
        curl_setopt($ch, CURLOPT_POST, TRUE);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/json"
        ));
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $response = json_decode($response, 1);
        
        if($response['verifyConn'] == 1){
            return TRUE;
        }else{
            throw new Server_Exception('Connection to server failed');
        }
    }

    /**
     * MEthods retrieves information from server, assignes new values to
     * cloned Server_Account object and returns it.
     * @param Server_Account $a
     * @return Server_Account 
     */
    public function synchronizeAccount(Server_Account $a)
    {
        $this->getLog()->info('Synchronizing account with server '.$a->getUsername());
        $new = clone $a;
        //@example - retrieve username from server and set it to cloned object
        //$new->setUsername('newusername');
        return $new;
    }

    /**
     * Create new account on server
     * 
     * @param Server_Account $a 
     */
	public function createAccount(Server_Account $a)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Creating reseller hosting account');
        } else {
            $this->getLog()->info('Creating shared hosting account');
        }
	}

    /**
     * Suspend account on server
     * @param Server_Account $a 
     */
	public function suspendAccount(Server_Account $a)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Suspending reseller hosting account');
        } else {
            $this->getLog()->info('Suspending shared hosting account');
        }
	}

    /**
     * Unsuspend account on server
     * @param Server_Account $a 
     */
	public function unsuspendAccount(Server_Account $a)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Unsuspending reseller hosting account');
        } else {
            $this->getLog()->info('Unsuspending shared hosting account');
        }
	}

    /**
     * Cancel account on server
     * @param Server_Account $a 
     */
	public function cancelAccount(Server_Account $a)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Canceling reseller hosting account');
        } else {
            $this->getLog()->info('Canceling shared hosting account');
        }
	}

    /**
     * Change account package on server
     * @param Server_Account $a
     * @param Server_Package $p 
     */
	public function changeAccountPackage(Server_Account $a, Server_Package $p)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Updating reseller hosting account');
        } else {
            $this->getLog()->info('Updating shared hosting account');
        }
        
        $p->getName();
        $p->getQuota();
        $p->getBandwidth();
        $p->getMaxSubdomains();
        $p->getMaxParkedDomains();
        $p->getMaxDomains();
        $p->getMaxFtp();
        $p->getMaxSql();
        $p->getMaxPop();
        
        $p->getCustomValue('param_name');
	}

    /**
     * Change account username on server
     * @param Server_Account $a
     * @param type $new - new account username
     */
    public function changeAccountUsername(Server_Account $a, $new)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Changing reseller hosting account username');
        } else {
            $this->getLog()->info('Changing shared hosting account username');
        }
    }

    /**
     * Change account domain on server
     * @param Server_Account $a
     * @param type $new - new domain name
     */
    public function changeAccountDomain(Server_Account $a, $new)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Changing reseller hosting account domain');
        } else {
            $this->getLog()->info('Changing shared hosting account domain');
        }
    }

    /**
     * Change account password on server
     * @param Server_Account $a
     * @param type $new - new password
     */
    public function changeAccountPassword(Server_Account $a, $new)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Changing reseller hosting account password');
        } else {
            $this->getLog()->info('Changing shared hosting account password');
        }
    }

    /**
     * Change account IP on server
     * @param Server_Account $a
     * @param type $new - account IP
     */
    public function changeAccountIp(Server_Account $a, $new)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Changing reseller hosting account ip');
        } else {
            $this->getLog()->info('Changing shared hosting account ip');
        }
    }
}