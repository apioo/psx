
OAuth provider implementation
=============================

If you want offer an Oauth endpoint for applications PSX offers some classes to 
implement the Oauth 1.0 specification. In the following section we will 
implement a bas Oauth endpoint where a client can get tokens and access an 
protected API. More informations about Oauth at 
http://tools.ietf.org/html/rfc5849.

Setting up the table
--------------------

.. code-block:: sql

    CREATE TABLE IF NOT EXISTS `request` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `ip` varchar(128) NOT NULL,
      `token` varchar(40) NOT NULL,
      `tokenSecret` varchar(40) NOT NULL,
      `nonce` varchar(32) NOT NULL,
      `verifier` varchar(16) DEFAULT NULL,
      `authorized` int(1) NOT NULL DEFAULT '0',
      `callback` varchar(256) DEFAULT NULL,
      `exchangeDate` datetime DEFAULT NULL,
      `authorizationDate` datetime DEFAULT NULL,
      `insertDate` datetime NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `token` (`token`),
      UNIQUE KEY `tokenSecret` (`tokenSecret`),
      UNIQUE KEY `nonce` (`nonce`),
      UNIQUE KEY `verifier` (`verifier`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

Oauth endpoints
---------------

In order to enable Oauth authentication we have to implement the following 
endpoints like defined in the specification.

+------------------------------+----------------------------------------+
| Endpoint                     | Location                               |
+------------------------------+----------------------------------------+
| Temporary Credential Request | http://localhost/index.php/api/request |
| Resource Owner Authorization | http://localhost/index.php/api/auth    |
| Token Request                | http://localhost/index.php/api/access  |
+------------------------------+----------------------------------------+

Temporary Credential Request
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

This endpoint is for obtaining an temporary credential.

.. code-block:: php

    <?php
    
    namespace api;
    
    use PSX\DateTime;
    use PSX\Exception;
    use PSX\Oauth\Provider\RequestAbstract;
    use PSX\Oauth\Provider\Data;
    
    class request extends RequestAbstract
    {
    	public function onLoad()
    	{
    		try
    		{
    			// if we call the handle method the OAuth request is proccessed and
    			// the getConsumer() and getResponse() method is called
    			$this->handle();
    		}
    		catch(\Exception $e)
    		{
    			header('HTTP/1.1 500 Internal Server Error');
    
    			echo $e->getMessage();
    
    			if($this->config['psx_debug'] === true)
    			{
    				echo "\n\n" . $e->getTraceAsString();
    			}
    		}
    	}
    
    	protected function getConsumer($consumerKey)
    	{
    		if($consumerKey == $this->config['consumer_key'])
    		{
    			return new Data\Consumer($this->config['consumer_key'], $this->config['consumer_secret']);
    		}
    		else
    		{
    			throw new Exception('Invalid consumer key');
    		}
    	}
    
    	protected function getResponse(Data\Consumer $consumer, Data\Request $request)
    	{
    		// generate tokens
    		$token       = sha1(uniqid(mt_rand(), true));
    		$tokenSecret = sha1(uniqid(mt_rand(), true));
    
    		// insert request
    		$this->getSql()->insert('request', array(
    
    			'ip'          => $_SERVER['REMOTE_ADDR'],
    			'token'       => $token,
    			'tokenSecret' => $tokenSecret,
    			'nonce'       => $request->getNonce(),
    			'callback'    => $request->getCallback(),
    			'insertDate'  => date(DateTime::SQL),
    
    		));
    
    		// return response
    		$response = new Data\Response();
    		$response->setToken($token);
    		$response->setTokenSecret($tokenSecret);
    
    		return $response;
	}
}

Resource Owner Authorization
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If the Oauth client has obtained the temporary credential the user will be 
redirected to the Resource Owner Authorization endpoint.

.. code-block:: php

    <?php
    
    namespace api;
    
    use PSX\Exception;
    use PSX\DateTime;
    use PSX\Url;
    use PSX\ModuleAbstract;
    use PSX\Sql\Condition;
    
    class auth extends ModuleAbstract
    {
    	public function onLoad()
    	{
    		$token = isset($_GET['oauth_token']) ? $_GET['oauth_token'] : null;
    
    		if(!empty($token))
    		{
    			$row = $this->getSql()->getRow('SELECT `id`, `ip`, `token`, `authorized`, `callback`, `insertDate` FROM `request` WHERE `token` = ?', array($token));
    
    			if(!empty($row))
    			{
    				// @todo normally we have to check here whether the current user
    				// is authenticated. If not the user has to login. Then a form 
    				// should be displayed whether the user wants to grant or deny 
    				// the application. If the user allows the application we 
    				// approve the request. To simplify things we acccept the 
    				// request on load
    
    				// validate
    				if($_SERVER['REMOTE_ADDR'] != $row['ip'])
    				{
    					throw new Exception('Token was requested from another ip');
    				}
    
    				if($row['authorized'] != 0)
    				{
    					throw new Exception('Token was already authorized');
    				}
    
    				// @todo check the insertDate whether token is expired
    
    				// generate verifier
    				$verifier = substr(sha1(uniqid(mt_rand(), true)), 0, 16);
    
    				// update request
    				$con = new Condition(array('id', '=', $row['id']));
    
    				$this->getSql()->update('request', array(
    
    					'verifier'          => $verifier,
    					'authorized'        => 1,
    					'authorizationDate' => date(DateTime::SQL),
    
    				), $con);
    
    				// redirect user or display verifier
    				if($row['callback'] != 'oob')
    				{
    					$url = new Url($row['callback']);
    					$url->addParam('oauth_token', $row['token']);
    					$url->addParam('oauth_verifier', $verifier);
    
    				    	header('Location: ' . strval($url));    
    				    	exit;    
    				}    
    				e    lse    
    				{    
    					echo '<p>You have successful authorized a token. Please provide the following verifier to your application in order to complete the authorization     proccess.</p>';
    					echo '<p>Verifier:</p><p><b>' . $verifier . '</b></p>';
    				}
    			}
    			else
    			{
    				throw new Exception('Invalid token');
    			}
    		}
    		else
    		{
    			throw new Exception('Token not set');
    		}
    	}
    }

Token Request
^^^^^^^^^^^^^

.. code-block:: php

    <?php
    
    namespace api;
    
    use PSX\DateTime;
    use PSX\Exception;
    use PSX\Oauth\Provider\AccessAbstract;
    use PSX\Oauth\Provider\Data;
    use PSX\Sql\Condition;
    
    class access extends AccessAbstract
    {
    	protected $id;
    	protected $nonce;
    	protected $verifier;
    
    	public function onLoad()
    	{
    		try
    		{
    			// if we call the handle method the OAuth request is proccessed and
    			// the getConsumer() and getResponse() method is called
    			$this->handle();
    		}
    		catch(\Exception $e)
    		{
    			header('HTTP/1.1 500 Internal Server Error');
    
    			echo $e->getMessage();
    
    			if($this->config['psx_debug'] === true)
    			{
    				echo "\n\n" . $e->getTraceAsString();
    			}
    
    			exit;
    		}
    	}
    
    	protected function getConsumer($consumerKey, $token)
    	{
    		if($consumerKey == $this->config['consumer_key'])
    		{
    			$row = $this->getSql()->getRow('SELECT id, nonce, verifier, token, tokenSecret FROM request WHERE token = ? AND authorized = 1', array($token));
    
    			if(!empty($row))
    			{
    				$this->id       = $row['id'];
    				$this->nonce    = $row['nonce'];
    				$this->verifier = $row['verifier'];
    
    				return new Data\Consumer($this->config['consumer_key'], $this->config['consumer_secret'], $row['token'], $row['tokenSecret']);
    			}
    			else
    			{
    				throw new Exception('Invalid token');
    			}
    		}
    		else
    		{
    			throw new Exception('Invalid consumer key');
    		}
    	}
    
    	protected function getResponse(Data\Consumer $consumer, Data\Request $request)
    	{
    		// validate
    		if($this->nonce == $request->getNonce())
    		{
    			throw new Exception('Nonce hasnt changed');
    		}
    
    		if($this->verifier != $request->getVerifier())
    		{
    			throw new Exception('Invalid verifier');
    		}
    
    		// generate a new access token
    		$token       = sha1(uniqid(mt_rand(), true));
    		$tokenSecret = sha1(uniqid(mt_rand(), true));
    
    		// update request
    		$con = new Condition(array('id', '=', $this->id));
    
    		$this->getSql()->update('request', array(
    
    			'authorized'   => 2,
    			'token'        => $token,
    			'tokenSecret'  => $tokenSecret,
    			'exchangeDate' => date(DateTime::SQL),
    
    		), $con);
    
    		// return response
    		$response = new Data\Response();
    		$response->setToken($token);
    		$response->setTokenSecret($tokenSecret);
    
    		return $response;
    	}
    }

Protect the API endpoint
^^^^^^^^^^^^^^^^^^^^^^^^

Now we can protect our API like defined in the "API authorization" chapter. Here 
an simple implementation

.. code-block:: php

    public function getRequestFilter()
    {
    	$config = $this->getConfig();
    	$sql    = $this->getSql();
    
    	$auth = new OauthAuthentication(function($consumerKey, $token) use ($config, $sql){
    
    		if($consumerKey == $config['consumer_key'])
    		{
    			$row = $sql->getRow('SELECT token, tokenSecret FROM request WHERE token = ? AND authorized = 2', array($token));
    
    			if(!empty($row))
    			{
    				return new Data\Consumer($config['consumer_key'], $config['consumer_secret'], $row['token'], $row['tokenSecret']);
    			}
    			else
    			{
    				throw new Exception('Invalid token');
    			}
    		}
    		else
    		{
    			throw new Exception('Invalid consumer key');
    		}
    
    	});
    
    	return array($auth);
    }

