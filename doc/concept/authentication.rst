
Authentication
==============

Abstract
--------

This chapter shows how to implement authentication for a controller

Basic authentication
--------------------

Basic authentication is the most simple authentication method where a user 
provides a username and password in the header. Note if you use basic 
authentication you should use https since the username and password is 
transported in plaintext over the wire. Add the following method to the 
controller in order to add basic authentication

.. code-block:: php

    <?php

    use PSX\Framework\Filter\BasicAuthentication;
    
    ...
    
    public function getPreFilter()
    {
    	$auth = new BasicAuthentication(function($username, $password) {
    
    		if ($username == '[username]' && $password == '[passsword]') {
    			return true;
    		}
    
    		return false;
    
    	});
    
    	return array($auth);
    }

Oauth authentication
--------------------

Sample oauth authentication. This is only to illustrate what to return. Normally 
you have to check

* is the consumerKey valid
* does the token belongs to a valid request with a valid status
* is the token not expired

PSX calculates and compares the signature if you return an consumer. For more 
informations see :rfc:`5849`.

.. code-block:: php

    <?php
    
    use PSX\Framework\Filter\OauthAuthentication;
    use PSX\Oauth\Consumer;
    
    ...
    
    public function getPreFilter()
    {
    	$auth = new OauthAuthentication(function($consumerKey, $token) {
    
    		if ($consumerKey == '[consumerKey]' && $token == '[token]') {
    			return new Consumer('[consumerKey]', '[consumerSecret]', '[token]', '[tokenSecret]');
    		}
    
    		return false;
    
    	});
    
    	return array($auth);
    }

Oauth2 authentication
--------------------

Sample oauth2 authentication. In the callback you have to check whether the 
provided `Bearer` access token is valid. For more informations see :rfc:`6749`.

.. code-block:: php

    <?php
    
    use PSX\Framework\Filter\Oauth2Authentication;
    
    ...
    
    public function getPreFilter()
    {
        $auth = new Oauth2Authentication(function($accessToken) {
    
            if ($accessToken == '[accessToken]') {
                return true;
            }
    
            return false;
    
        });
    
        return array($auth);
    }
