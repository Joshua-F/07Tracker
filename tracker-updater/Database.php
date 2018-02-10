<?php
/**
 * Last Updated: 1-31-2012
 *
 * @author  Joshua F <thekrazyone96@gmail.com>
 * @version 1.0
 */

class Database {

    /**
     * Holds the connection
     *
     * @var resource
     */
    protected $con = null;

    /**
     * The query id
     *
     * @var resource
     */
    protected $query_id;

    /**
     * Array holding all queries
     *
     * @var array
     */
    public $queries = array();

    /**
     * Weither or not to echo out json readable errors
     * @var boolean
     */
    public $jsonError = false;

    /**
     * Constructs the member class
     */
    public function __construct( $dbinfo ) {
        if ( !is_array( $dbinfo ) ) {
            exit( 'Constructor requires an arg array with the following: host, user, pass, name(database name) and port if needed.' );
        }

        $this->connect( $dbinfo['host'], $dbinfo['user'], $dbinfo['pass'], $dbinfo['name'], isset( $dbinfo['port'] ) ? $dbinfo['port'] : 3306 );
    }

    /**
     * Connects to a database
     *
     * @param string  $host     The host to connect to
     * @param string  $username The database username
     * @param string  $password The users password
     * @param string  $database The database name
     * @param integer $port     [Optional] The port the database is on
     */
    private function connect( $host, $username, $password, $database, $port=3306 ) {
        try {
            $this->con = new PDO( "mysql:host={$host};dbname={$database};port={$port}", $username, $password );
            $this->con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            // $this->con->setAttribute( PDO::ATTR_PERSISTENT, true );
        } catch ( PDOException $e ) {
            $this->errorAndDie( $e->getMessage() );
        }
    }

    /**
     * Executes a direct query to the database
     *
     * @param string  $query       The query to run
     * @param boolean $return_self If true return class instance for chaining, if false return the PDOStatement instance
     * @return object PDOStatement object or self instance
     */
    public function query( $query, $return_self=true ) {
        $this->query_id = $this->con->prepare( $query );
        return $return_self ? $this : $this->query_id;
    }

    /**
     * Bind a value to a param
     *
     * @param mixed   $param Parameter identified
     * @param mixed   $value The value to set the param to
     * @param object  $query [Optional] If null use stored statement
     * @param int     $type  [Optional] Leave blank to auto detect
     * @return object        Self instance for chaining
     */
    public function bind( $param, $value, $query=null, $type=null ) {

        $query = is_null( $query ) ? $this->query_id : $query ;

        if ( is_null( $type ) ) {
            switch ( true ) {
            case is_null( $value ):
                $type = PDO::PARAM_NULL;
                break;
            case is_int( $value ):
                $type = PDO::PARAM_INT;
                break;
            case is_bool( $value ):
                $type = PDO::PARAM_BOOL;
                break;
            default:
                $type = PDO::PARAM_STR;
            }
        }

        try {
            $query->bindParam( $param, $value, $type );
        } catch(Exception $e) {
            $this->errorAndDie($e->getMessage());
        }
        return $this;
    }

    /**
     * Fetches the data from a database
     *
     * @param resource $query       The query
     * @param integer $fetch_style The way to fetch the results
     * @return array        An array holding the next result from the database
     */
    public function fetch( $query=null, $fetch_style=PDO::FETCH_ASSOC ) {
        $query = is_null( $query ) ? $this->query_id : $query ;
        return $query->fetch( $fetch_style );
    }

    /**
     * Fetches all data from a database
     *
     * @param resource $query       The query
     * @param integer $fetch_style The way to fetch the results
     * @return array        An array holding the results from the database
     */
    public function fetchAll( $query=null, $fetch_style=PDO::FETCH_ASSOC ) {
        $query = is_null( $query ) ? $this->query_id : $query ;
        return $query->fetchAll( $fetch_style );
    }

    /**
     * Get the total number of rows
     *
     * @param object  $query [Optional] Keep null to use stored statement
     * @return return        The number of rows
     */
    public function getTotalRows( $query=null ) {
        $query = is_null( $query ) ? $this->query_id : $query ;
        return $query->rowCount();
    }

    /**
     * Executes a query
     *
     * @param object  $query [Optional] Keep null to use the statement that is stored in the class
     * @return object        Self instance for chaining
     */
    public function execute( $query=null ) {
        $query = is_null( $query ) ? $this->query_id : $query ;
        $this->queries[] = $query->queryString;
        try {
            $query->execute();
        } catch (PDOException $e) {
            $this->errorAndDie( $e->getMessage() );
        }
        return $this;
    }

    protected function errorAndDie($error) {
        if ($this->jsonError) {
            die(json_encode(array("sql_error" => $error)));
        } else {
            die($error);
        }
    }

    /**
     * Destruct the class
     */
    public function __destruct() {
        $this->con = null;
    }

}
