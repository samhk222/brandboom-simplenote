<?php
class DB { 
    
    private static $objInstance; 
    
    /* 
     * Class Constructor - Create a new database connection if one doesn't exist 
     * Set to private so no-one can create a new instance via ' = new DB();' 
     */ 
    private function __construct() {} 
    
    /* 
     * Like the constructor, we make __clone private so nobody can clone the instance 
     */ 
    private function __clone() {} 
    
    /* 
     * Returns DB instance or create initial connection 
     * @param 
     * @return $objInstance; 
     */ 
    public static function getInstance(  ) { 
            
        try {
            
            if(!self::$objInstance){ 
                self::$objInstance = new PDO(getenv('DSN'), getenv('DB_USER'), getenv('DB_PASS')); 
                self::$objInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
                self::$objInstance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
                self::$objInstance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
                //self::$objInstance->query("SET NAMES utf8");
                //echo '<br>instancia criada';
            }       

        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        
        return self::$objInstance; 
    
    } # end method 
    
    /* 
     * Passes on any static calls to this class onto the singleton PDO instance 
     * @param $chrMethod, $arrArguments ação
     * @return $mix 
     */ 
    final public static function __callStatic( $chrMethod, $arrArguments ) { 
        $objInstance = self::getInstance(); 
        
/*        echo "\n<pre>";
        print_r($arrArguments);
        echo "\n</pre>";*/
            
        return call_user_func_array(array($objInstance, $chrMethod), $arrArguments); 
        
    } # end method 

    public static function interpolateQuery($query, $params) {
        $keys = array();
        $values = $params;

        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:'.$key.'/';
            } else {
                $keys[] = '/[?]/';
            }

            if (is_string($value))
                $values[$key] = "'" . $value . "'";

            if (is_array($value))
                $values[$key] = "'" . implode("','", $value) . "'";

            if (is_null($value))
                $values[$key] = 'NULL';
        }

        $query = preg_replace($keys, $values, $query);

        return $query;
    }


}