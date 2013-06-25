PSX Framework
===

## About

PSX is a framework written in PHP to create RESTful APIs. It helps you building 
clean URLs serving web standard formats like JSON, XML, Atom and RSS. At the 
core PSX is build of three parts. A handler system (similar to repositories in 
doctrine) wich abstracts away the actual SQL queries from the domain logic. An 
routing system wich executes the fitting controller method depending on	the 
location of the controller and the annotation of the method. And an flexible 
data system to convert data records from the database into different formats 
like JSON, XML, Atom and RSS. PSX uses a lightweight DI container to handle 
dependencies (but is also compatible with the symfony DI container). The 
controller can return request or response filter wich can react or modify the 
HTTP request or response. PSX offers some basic request filter to handle i.e. 
Basic or Oauth authentication. In addition PSX offers some cool components to 
use and implement OAuth, OpenID, Opengraph, Opensocial, Opensearch, 
PubSubHubbub, WebFinger, Atom, and RSS.

## Links

Website:
http://phpsx.org

Examples:
http://example.phpsx.org


[![Build Status](https://travis-ci.org/k42b3/psx.png)](https://travis-ci.org/k42b3/psx)

