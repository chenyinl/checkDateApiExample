<?php
/**
 * A class for query the data
 * 
 * By given date range, it query the database for the available
 * data and return data in array
 * 
 * Example of using this class:
 *    $Obj = New DB_Banner( $dbh );
 *    $Obj -> setStart_date( $start_date );
 *    $Obj -> setEnd_date  ( $end_date );
 *    $Obj -> composeSQL();
 *    $Obj -> queryDB();
 *    the result will be in $Obj->results
 *
 * @category Ajax
 * @package  DB
 * @author   Chen Lin linchenyin@gmail.com
 * @see      
 * @since    2020/7/25
 */
 
class DB_Query {

    /**
     * Database object, a DB_mysqli instance
     */
    private $_dbh;

    /**
     * Start date, in 'Y-m-d H:I:s' format
     */
    private  $start_date;

    /**
     * End date, in 'Y-m-d H:I:s' format
     */
    private  $end_date;

    /**
     * SQL query
     */
    private $sql;

    /**
     * Column names from the database query
     */
    private $keys;
    
    /**
     * Data from the database query
     */
    private $data;

    /**
     * The result to be returned to the class callee
     */
    public $results;

    function __construct(&$dbh)
    {
		/* initial database */
        $this->_dbh = $dbh;
        
        /* initial data arry */
        $this->data = array();
        
        /*the column names */
        $this->keys = array(
			"id",
			"co_id" ,
      ...
      ...
      ...
			"name"
         );
    }

    /* set the start date */
    public function setStart_date(string $date)
    {
        $this->start_date = $date;
    }
    
    /* set the end date */
    public function setEnd_date(string $date)
    {
        $this->end_date = $date;
    }
    
    /* compose the SQL */
    public function composeSQL()
    {
        $this->sql = "
        SELECT 
            ...
            ...
        FROM 
        LEFT JOIN 
            ON 
        INNER JOIN 
            ON
        WHERE 
            AND (? >= date_start AND ?<date_end)
            AND  mhb.cobrand_id=1 
        ORDER BY ... DESC";
    }

    /* send query to the DB with prepare statement to prevent SQL 
     * injection attack
     */

    public function queryDB()
    {
        try{
            /**
             * if prepare failed, it will be handled at catch
             * for debug, var_dump(get_object_vars($this->_dbh)) can be 
             * used to check the values, for example:
             * $this->_dbh->prepared_queries[0]
             */
            $statement = $this->_dbh->prepare( $this->sql );
            $r = $this->_dbh->execute(
                $statement,
                array( $this->start_date, $this->end_date )
            );
            
            /* fetch the data */
            while($d = $r->fetchRow()){
				/* add key to data since fetch does not include keys */
                $row = array_combine(
                    $this->keys,
                    $d
                );
                $this->data[] = $row;
            }
        } catch(\Throwable  $e){
            $this->errors = $e->getMessage();
        } finally {
			/* save the results */
			$this->setResults();
		}
    }
    
    /**
     * Compose the results from database or exception
     */
    public function setResults()
    {
		$this->results = array(
		    "data" => $this->data,
		    "errors" => $this->errors,
		    "meta" => array(
		        "start_date" => $this->start_date,
		        "end_date"   => $this->end_date
		    )
		);
	}
}
