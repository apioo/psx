
Authentication
==============

It is often the case that you want that only registered users can POST new 
entries to the API endpoint. In this case the user has to authorize before 
submitting a new record. PSX offers several authorization methods for your API. 
In this chapter we will look at different authentication methods and howto 
implement them.

Basic authentication
--------------------

Basic authentication is the most simple authentication method where a user 
provides an username and password in the header. Note if you use basic 
authentication you should use https since the username and password is 
transported in plaintext over the wire. Add the following method to the news API 
in order to add basic authentication

.. code-block:: php

    <?php

    use PSX\Dispatch\RequestFilter\BasicAuthentication;
    
    ...
    
    public function getRequestFilter()
    {
    	$auth = new BasicAuthentication(function($username, $password){
    
    		if($username == '[username]' && $password == '[passsword]')
    		{
    			return true;
    		}
    
    		return false;
    
    	});
    
    	return array($auth);
    }

Oauth authentication
--------------------

Sample oauth authentication

.. code-block:: php

    <?php
    
    use PSX\Dispatch\RequestFilter\OauthAuthentication;
    use PSX\Oauth\Provider\Data\Consumer;
    
    ...
    
    public function getRequestFilter()
    {
    	$auth = new OauthAuthentication(function($consumerKey, $token){
    
    		// this is only to illustrate what to return. Normally you have to check
    		// - is it a valid consumerKey
    		// - does the token belongs to an valid request with a valid status
    		// - is the token not expired
    		// PSX calculates and compares the signature if you return an consumer.
    		// For more informations see http://tools.ietf.org/html/rfc5849
    		if($consumerKey == '[consumerKey]' && $token == '[token]')
    		{
    			return new Consumer('[consumerKey]', '[consumerSecret]', '[token]', '[tokenSecret]');
    		}
    
    		return false;
    
    	});
    
    	return array($auth);
    }
