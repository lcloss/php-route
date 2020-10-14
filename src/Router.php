<?php
namespace LCloss\Route;

use LCloss\Debug\Debug;
use LCloss\Route\Request;
use LCloss\Route\Dispatcher;
use LCloss\Route\RouteCollection;

class Router 
{
    protected $route_collection;
    protected $dispatcher;
    private $_debug;

    public function __construct() 
    {
        $this->_debug = new Debug( DEBUG::DEBUG_NONE );

        $this->route_collection = new RouteCollection();
        $this->dispatcher = new Dispatcher();
    }
    
    public function get( $pattern, $callback ) 
    {
        $this->route_collection->add('get', $pattern, $callback);
        return $this;
    }
    public function post( $pattern, $callback ) 
    {
        $this->route_collection->add('post', $pattern, $callback);
        return $this;
    }
    public function put( $pattern, $callback ) 
    {
        $this->route_collection->add('put', $pattern, $callback);
        return $this;
    }
    public function delete( $pattern, $callback ) 
    {
        $this->route_collection->add('delete', $pattern, $callback);
        return $this;
    }

    public function find( $request_method, $pattern ) 
    {
        $this->_debug->printWhere();
        return $this->route_collection->where( $request_method, $pattern );
    }

    public function dispatch( $route, $params, $namespace = "App\\" ) 
    {
        $this->_debug->printWhere();
        return $this->dispatcher->dispatch( $route->callback, $params, $namespace );
    }

    protected function notFound() 
    {
        $this->_debug->printWhere();
        return header("HTTP/1.0 404 Not Found", true, 404);
    }

    public function resolve( $request ) 
    {
        $this->_debug->printWhere();
        $route = $this->find( $request->method(), $request->uri() );

        if ( $route ) {
            $params = $route->callback['values'] ? $this->getValues( $request->uri(), $route->callback['values'] ) : [];
            return $this->dispatch( $route, $params );
        }
        return $this->notFound();
    }

    protected function getValues( $pattern, $positions ) 
    {
        $result = [];

        $pattern = array_filter(explode('/', $pattern));

        foreach( $pattern as $key => $value ) {
            if ( in_array($key, $positions) ) {
                $result[array_search($key, $positions)] = $value;
            }
        }

        return $result;
    }

    public function translate( $name, $params ) 
    {
        $pattern = $this->route_collection->isThereAnyHow( $name );

        if ( $pattern ) {
            $request = new Request();
            $protocol = $request->protocol();
            $server = $request->server();
            $uri = [];

            // echo 'Base: ' . $request->base();

            foreach( array_filter(explode('/', $request->base())) as $key => $value ) {
                if ( $value == 'public' ) {
                    $uri[] = $value;
                    break;
                }
                $uri[] = $value;
            }
            $uri = implode('/', array_filter($uri)) . '/';

            // return $protocol . '://'. $server . $uri . $this->route_collection->convert( $pattern, $params );
            return $protocol . '://'. $server . $this->route_collection->convert( $pattern, $params );
        }
        return false;
    }
}