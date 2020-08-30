
<?php
	//include connection file 
	include_once("connection.php");
	 
	// initilize all variable
	$params =  $totalRecords = $data = array();

	$sqlTot = $sqlRec = $where = "";
	
	
	$params = $_REQUEST;
	$limit = $params["rowCount"];

	if (isset($params["current"])) { $page  = $params["current"]; } else { $page=1; };  
    $start_from = ($page-1) * $limit;
	// check search value exist
	if( !empty($params['searchPhrase']) ) {   
		$where .=" WHERE ";
		$where .=" ( employee_name LIKE '".$params['searchPhrase']."%' ";    
		$where .=" OR employee_salary LIKE '".$params['searchPhrase']."%' ";

		$where .=" OR employee_age LIKE '".$params['searchPhrase']."%' )";
	}
	if( !empty($params['sort']) ) {  
		$where .=" ORDER By ".key($params['sort']) .' '.current($params['sort'])." ";
	}
	// getting total number records without any search
	$sql = "SELECT * FROM `employee` ";
	$sqlTot .= $sql;
	$sqlRec .= $sql;
	
	
	//concatenate search sql if value exist
	if(isset($where) && $where != '') {

		$sqlTot .= $where;
		$sqlRec .= $where;
	}
	if ($limit!=-1)
	$sqlRec .= "LIMIT $start_from, $limit";
		
	$queryTot = mysqli_query($conn, $sqlTot) or die("database error:". mysqli_error($conn));


	$totalRecords = mysqli_num_rows($queryTot);

	$queryRecords = mysqli_query($conn, $sqlRec) or die("error to fetch employees data");

	//iterate on results row and create new index array of data
	while( $row = mysqli_fetch_assoc($queryRecords) ) { 
		$data[] = $row;
		//echo "<pre>";print_R($data);die;
	}	

	$json_data = array(
			"current"            => intval( $params['current'] ), 
			"rowCount"            => 10, 			
			"total"    => intval( $totalRecords ),
			"rows"            => $data   // total data array
			);

	echo json_encode($json_data);  // send data as json format
?>
	