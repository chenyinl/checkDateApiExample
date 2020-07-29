<?php
/**
 * Ajax call to get the banner setting by date range
 * GET /ajax/api.php
 * 
 * Parameters:
 *   start_date 
 *     [option]
 *     Example: 2020-07-25
 *     It will be set to current time if not given
 *   end_date 
 *     [option]
 *     Example: 2020-07-25
 *     It will be set to current time if not given
 * 
 * Output:
 *   data
 *     The banner setting data
 *   meta
 *     Echo the start data and end date
 *   errors
 *     Error message if any
 * 
 * Output format: JSON
 * 
 * Example
 *   INPUT:
 *       /ajax/api.php?start_date=2019-06-01&end_date=2019-06-30
 *       /ajax/api.php
 *   OUTPUT:
 *      {
 *	       "data":[
 *		      {
 *			     "id":"953",
 *			     ...
 *           ...
 *			     "date_start":"2020-07-01",
 *			     "date_end":"2020-07-29",
 *			     "date_update":"2020-07-28 13:08:26",
 *			     "date_orig":"2019-05-22 13:10:53",
 *			     "date_end_real":"2020-07-30",
 *		      }
 *	       ],
 *	       "errors":null,
 *	       "meta":{
 *		      "start_date":"2020-07-28 18:1:35",
 *		      "end_date":"2020-07-28 18:1:35"
 *	       }
 *	    }
 */

/* disable PEAR error handling so it won't die */
PEAR::setErrorHandling(PEAR_ERROR_EXCEPTION);

/**
 * sanitize the input and check the format 
 * date_as_string defined at /php_classes/lib_format.php
 */
function prepareDate($date): string {

	if(isset($date)){
		if ((bool)strtotime($date) === false){
		    throw new Exception( "Invalid date given: ".$date );
	    }
		$d = date_create($date);
		return date_as_string( $d, true, false );
	} else{
		return date( 'Y-m-d H:I:s' );
	}
}

/**
 * The main code
 */
try {//$start_date = request_validate('start_date', $type = 'string');
    $start_date = prepareDate( $_GET['start_date'] );
    $end_date   = prepareDate( $_GET['end_date'] );
    
    $bannerObj = New DB_Banner( $dbh );
    $bannerObj -> setStart_date( $start_date );
    $bannerObj -> setEnd_date( $end_date );
    $bannerObj -> composeSQL();
    $bannerObj -> queryDB();
    
    $returnObj = $bannerObj->results;
    
} catch (Throwable $t) {
	/* save the errors */
	$returnObj = array(
	    "data" => NULL,
	    "error"=> $t->getMessage(),
	    "meta" => NULL
	);

} finally {
	/* print out the results */
	header( 'Content-Type: application/json' );
	echo json_encode( $returnObj );
	exit();
}
