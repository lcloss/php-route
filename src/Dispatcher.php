<?php
namespace LCloss\Route;
use LCloss\Debug\Debug;

class Dispatcher {
    private $_debug;
    public function __construct()
    {
        $this->_debug = new Debug( Debug::DEBUG_NONE );
    }
    public function dispatch( $callback, $params = [], $namespace = "App\\" ) 
    {
        $this->_debug->printWhere();
        $this->_debug->printInfo('params:');
        $this->_debug->printInfo($params);

        if ( is_callable( $callback['callback'] )) {
            return call_user_func_array( $callback['callback'], array_values( $params ));

        } elseif ( is_string( $callback['callback'] )) {
            if ( false !== !!strpos( $callback['callback'], '@') ) {

                if ( !empty($callback['namespace']) ) {
                    $namespace = $callback['namespace'];
                }

                $callback['callback'] = explode('@', $callback['callback']);
                $controller = $namespace.$callback['callback'][0];
                $action = $callback['callback'][1];

                $rc = new \ReflectionClass($controller);

                if ( $rc->isInstantiable() && $rc->hasMethod( $action )) {
                    return call_user_func_array( array( new $controller, $action ), array_values( $params ) );
                } else {
                    throw new \Exception('Erro no dispatcher: controller não pôde ser instanciado ou método não existe');
                }
            }
        }
        throw new \Exception('Erro no dispatcher: método não implementado.');
    }

}