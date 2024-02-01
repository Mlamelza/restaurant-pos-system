<?php
$pagetitle = 'Select Employee';
require_once 'boot_header.php';
require_once 'connect.php';

// Here we are checking to see if a session has been started, if the job
// has been set, and if the current status of the user is active
if((isset($_SESSION['empid'])) && (isset($_SESSION['empjobid']))
	&& (($_SESSION['empstatusid'])== 1))
{	
	//This indicates a permission level of 1 (General Manager)
	if ($_SESSION['emppermitid'] == 1) {
		$showform = 1;
	} 
	/*This indicates a permission level of 9 (Assistant Manager, Shift Lead,
	or Bar Manager. We then break this down to the specific job because an
	AM will certainly have greater data access than a SL or Bar Manager*/
	else if ($_SESSION['emppermitid'] == 9) {
		
		//Assistant Manager
		if ($_SESSION['empjobid'] == 3) {
			$showform = 8;
		}
		 //Shift Leader or Bar Manager
		else if (($_SESSION['empjobid'] == 4) || ($_SESSION['empjobid'] == 5)){
			$showform = 6;
		}
	}// ends if permit level of 9
	
	// This indicates a permission level of KM, setting our $showform variable to equal 2
	else if ($_SESSION['emppermitid'] == 2) {
		$showform = 2;
	}
	
	// This indicates a permission level of our waitstaff, cooks, and support staff, setting our $showform variable to equal 3
	else if (($_SESSION['emppermitid'] == 3) || ($_SESSION['emppermitid'] == 4) || ($_SESSION['emppermitid'] == 5) || 
	($_SESSION['emppermitid'] == 6) || ($_SESSION['emppermitid'] == 8) || ($_SESSION['emppermitid'] == 10)
		|| ($_SESSION['emppermitid'] == 11)) {
		$showform = 3;
	}
	
	// Indicates a permission level of Admin or Regional Manager
	else if ($_SESSION['emppermitid'] == 7) {
		
		//Regional Manager
		if ($_SESSION['empjobid'] == 18) {
		$showform = 7;
		}
		//Administrator
		else if ($_SESSION['empjobid'] == 1) {
			$showform = 5;
		}
	}// ends permit level of 7
	
 
} // ends our if isset for employee session
// If no user is logged in, we simply set our $showform variable to equal 4
else {
	$showform = 4;
	// if user is not logged in
}

// We are setting our default formtype variable, which will be displayed
//to the appropriate personnel
//$errormsg = "";
$formtype = 1;


/**********************************************************************************************************/
//For our state drop down menu in our forms below
$sqlselectstate = "SELECT * from state ORDER BY dbstateName ASC";
$resultstate = $db->prepare($sqlselectstate);
$resultstate->execute();

//For our status drop down menu in our forms below
$sqlselectstatus = "SELECT * from status WHERE dbstatusID <=2";
$resultstatus = $db->prepare($sqlselectstatus);
$resultstatus->execute();

//Here we pull locations associated with the user logged in, setting the 
//default value to be the location session variable of the current user.
//As they change their current location, so will the drop down default value.


$sqlselectlo3 = "SELECT location.* FROM 
						employee, location, locationDetail WHERE 
						locationDetail.dblocationID = location.dblocationID AND 
						locationDetail.dbemployeeID = employee.dbemployeeID AND 
						employee.dbemployeeID = :bvempid ORDER BY CASE WHEN 
						location.dblocationID = :bvlocid THEN 1 ELSE 2 END, dblocationID";
$resultselectlo3 = $db->prepare($sqlselectlo3);
$resultselectlo3->bindvalue(':bvempid', $_SESSION['empid']);
$resultselectlo3->bindvalue(':bvlocid', $_SESSION['emplocationid']);
$resultselectlo3->execute();

/*$sqlselectlo3 = "SELECT location.* FROM 
						employee, location, locationDetail WHERE 
						locationDetail.dblocationID = location.dblocationID AND 
						locationDetail.dbemployeeID = employee.dbemployeeID AND 
						employee.dbemployeeID = :bvemployeeid ORDER BY 
						locationDetail.dblocationDetailID";
$resultselectlo3 = $db->prepare($sqlselectlo3);
$resultselectlo3->bindvalue(':bvemployeeid', $_SESSION['empid']);
$resultselectlo3->execute();*/

/*******************************************************************************/
/* Here we have our queries to our DB, gathering data needed for our drop downs
Notice that based on the logged in user permissions, the specific job titles
will be pulled from the database accordingly. If the logged in user is in an
Administrative position, all job titles will be pulled from the DB.*/
if ($showform == 5)  {
$queryjobs = 'SELECT job.* FROM job
          ORDER BY dbjobName ASC'; // Listing by job name alphabetical order
$employeejobs = $db->prepare($queryjobs);
$employeejobs->execute();
}
//If the current user is a Regional Manager, all job titles except for the 
//Administrative positions will be pulled from our DB.
else if ($showform == 7)  {
$queryjobs = 'SELECT job.* FROM job WHERE job.dbjobID != 1 
	ORDER BY dbjobName ASC'; // Listing by job name alphabetical order
$employeejobs = $db->prepare($queryjobs);
$employeejobs->execute();
}
//If the logged in user is a GM, all job titles will be pulled from our DB, 
//except for the Administrative and Regional Manager titles.
else if ($showform == 1) {
$queryjobs = 'SELECT job.* FROM job WHERE job.dbjobID !=1 && 
	job.dbjobID!= 18 ORDER BY dbjobName ASC'; // Listing by job name alphabetical order
$employeejobs = $db->prepare($queryjobs);
$employeejobs->execute();
}
/*If the current user logged in is an Assistant Manager, the job titles that will be pulled 
will include Assistant Manager, Kitchen Manager, Bar Manager, SL, and hourly positions.*/
else if ($showform == 8) {
$queryjobs = 'SELECT job.* FROM job WHERE job.dbjobID !=1 && job.dbjobID !=2 &&
	job.dbjobID != 18 ORDER BY dbjobName ASC'; // Listing by job name alphabetical order
$employeejobs = $db->prepare($queryjobs);
$employeejobs->execute();
}
/*If the current user logged in is a Shift Leader or Bar Manager, the job titles that 
will be pulled will include KM, Bar Manager, Shift Leader, and all hourly positions.*/
else if ($showform == 6) {
$queryjobs = 'SELECT job.* FROM job WHERE job.dbjobID !=1 && job.dbjobID !=2 && job.dbjobID !=3 &&
	job.dbjobID != 18 ORDER BY dbjobName ASC'; // Listing by job name alphabetical order
$employeejobs = $db->prepare($queryjobs);
$employeejobs->execute();
}
/*If the current user logged in is a Kitchen Manager, the job titles that will be pulled
 will be identical to those of SL and Bar Managers.*/
else if ($showform == 2) {
$queryjobs = 'SELECT job.* FROM job WHERE job.dbjobID !=1 && job.dbjobID !=2 && job.dbjobID !=3 &&
	job.dbjobID != 18 ORDER BY dbjobName ASC'; // Listing by job name alphabetical order
$employeejobs = $db->prepare($queryjobs);
$employeejobs->execute();
}

/**Notice here that if the clear button is pressed, the form type 
that is displayed will become the $formtype of 1. The same applies 
if the new submit button is pressed. This means that the user would
essentially start over and select a new employee record from the DB.
This clears the input boxes and drop downs so the user can start over
and search for a new record in our DB table.
**/
if (isset($_POST['theclear']))	{
	$formtype = 1; 
}

/*Here is where we check to see if the submit button is pressed by the user. If so, 
we cleanse our formfield data and attempt to pull the appropriate record from the DB.
*/


// We check and see if the submit button to select an employee is set
if(isset($_POST['thesubmit'])) {

	//Setting our empty message variables
	$stringclause = '';
	$stringclause2 = '';
	$errormsg = "";
	
	//Now we cleanse our data from our formfields below
//	$formfield['ffempssn'] = trim($_POST['empssn']);
//	$formfield['ffemphiredate'] = trim($_POST['emphiredate']);
    $formfield['ffempjob'] = ($_POST['empjob']);
    $formfield['ffemplocation'] = ($_POST['emplocation']);
	$formfield['ffempfirstname'] = trim($_POST['empfirstname']);
    $formfield['ffempstatus'] = trim($_POST['empstatus']);
    $formfield['ffemplastname'] = trim($_POST['emplastname']);
//	$formfield['ffempaddress1'] = trim($_POST['empaddress1']);
//  $formfield['ffempaddress2'] = trim($_POST['empaddress2']);
//  $formfield['ffempcity'] = trim($_POST['empcity']);
//  $formfield['ffempstate'] = ($_POST['empstate']);
//  $formfield['ffempzip'] = trim($_POST['empzip']);
//  $formfield['ffempphone'] = trim(strtolower($_POST['empphone']));
//  $formfield['ffempcellphone'] = trim(strtolower($_POST['empcellphone']));
	
	// If the following fields are not left empty, we assign the appropriate message 
	// to our stringclause variable that will display accordingly
/*	if ($formfield['ffempssn']  !=''){
	$stringclause .=" AND dbemployeeSSN = :bvempssn";
	}
	if ($formfield['ffemphiredate']  !=''){
	$stringclause .=" AND dbemployeeHireDate = :bvemphiredate";
	}*/
	if ($formfield['ffempjob']  !=''){
	$stringclause .=" AND job.dbjobID = :bvempjob";
	}
	if ($formfield['ffemplocation']  !=''){
	$stringclause .=" AND location.dblocationID = :bvemplocation";}
	
/*	if ($formfield['ffempfirstname']  !='') {
		$stringclause .=" AND dbemployeeFirstName = :bvempfirstname";
	}
	if ($formfield['ffemplastname']  !='') {
		$stringclause .=" AND dbemployeeLastName = :bvemplastname";
	}
	if ($formfield['ffempaddress1']  !='') {
		$stringclause .=" AND dbemployeeAddress1 = :bvempaddress1";
	}
	if ($formfield['ffempaddress2']  !='') {
		$stringclause .=" AND dbemployeeAddress2 = :bvempaddress2";
	}
	if ($formfield['ffempcity']  !='') {
		$stringclause .=" AND dbemployeeCity = :bvempcity";
	} 
	if ($formfield['ffempstate']  !='') {
		$stringclause .=" AND employee.dbstateID = :bvempstate";
	}
	if ($formfield['ffempzip']  !='') {
		$stringclause .=" AND dbemployeeZip = :bvempzip";
	}
	if ($formfield['ffempphone']  !='') {
		$stringclause .=" AND dbemployeeHomePhone = :bvempphone";
	}
	if ($formfield['ffempcellphone']  !='') {
		$stringclause .=" AND dbemployeeCellPhone = :bvempcellphone";
	}*/
	if ($formfield['ffempstatus']  !='') {
		$stringclause .=" AND status.dbstatusID = :bvempstatus";
	}
	
	//Here we check and see if the submit button was pressed with ALL empty fields
	if ((empty($formfield['ffempjob'])) && (empty($formfield['ffemplocation'])) && (empty($formfield['ffempfirstname'])) 
		&& (empty($formfield['ffemplastname'])) && (empty($formfield['ffempstatus']))) {
					
				$errormsg .= "You must choose at least one search method! ";
				}
	//Notice that if there is an error message, it will simply be because no search criteria was used at all
	//so essentially, taking the user to the very beginning, assigning the formtype to equal 1.
	if ($errormsg != "") {
echo '<div class = "container ps-5">';
		echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
		echo "There are errors! " . $errormsg . '</div></div>';
	//	echo $errormsg;		
	//This is essentially starting over since the form has nothing entered to find
		$formtype = 1;
	} else
		
		{
	

	// Now we gather our data from the DB according to our formfield values
	
/*	$sqlselect = "SELECT employee.*, job.*, location.*, status.*, state.*
		FROM employee, job, location, status, state, locationDetail, jobDetail 
		WHERE locationDetail.dbemployeeID = employee.dbemployeeID
		AND locationDetail.dblocationID = location.dblocationID AND
		employee.dbstatusID = status.dbstatusID AND employee.dbstateID =
		state.dbstateID AND jobDetail.dbjobID = job.dbjobID AND 
		jobDetail.dbemployeeID = employee.dbemployeeID 
		AND dbemployeeFirstName like CONCAT('%', :bvempfirstname,'%')
		AND dbemployeeLastname like CONCAT('%', :bvemplastname,'%')
		AND dbemployeeSSN like CONCAT('%', :bvempssn,'%')
		AND employee.dbstatusID like CONCAT('%', :bvempstatus,'%')
		AND dbemployeeHomePhone like CONCAT('%', :bvempphone,'%')
		AND dbemployeeCellPhone like CONCAT('%', :bvempcellphone,'%')
		AND dbemployeeAddress1 like CONCAT('%', :bvempaddress1,'%')
		AND dbemployeeAddress2 like CONCAT('%', :bvempaddress2,'%')
		AND dbemployeeCity like CONCAT('%', :bvempcity,'%')
		AND location.dblocationID like CONCAT('%', :bvemplocation,'%')
		AND job.dbjobID like CONCAT('%', :bvempjob,'%')	
		AND dbemployeeZip like CONCAT('%', :bvempzip,'%')
		AND dbemployeeHireDate like CONCAT('%', :bvemphiredate,'%')
		AND employee.dbstateID like CONCAT('%', :bvempstate,'%')"
	. $stringclause; */
	
	$sqlselect = "SELECT employee.*, job.*, location.*, status.*, state.*, jobstatus.*, payRateDetail.*
		FROM employee, job, location, status, state, locationDetail, jobDetail, jobstatus, payRateDetail
		WHERE locationDetail.dbemployeeID = employee.dbemployeeID
		AND payRateDetail.dbjobDetailID = jobDetail.dbjobDetailID
		AND locationDetail.dblocationID = location.dblocationID AND
		employee.dbstatusID = status.dbstatusID AND employee.dbstateID =
		state.dbstateID AND jobDetail.dbjobID = job.dbjobID AND 
		jobDetail.dbemployeeID = employee.dbemployeeID
		AND jobDetail.dbjobstatusID = jobstatus.dbjobstatusID
		AND jobDetail.dbjobstatusID = :bvstatus
		AND dbemployeeFirstName like CONCAT('%', :bvempfirstname,'%')
		AND dbemployeeLastname like CONCAT('%', :bvemplastname,'%')"
	. $stringclause; 
	
	
	
// We prepare and bind our values so we can gather the appropriate DB records
$result = $db->prepare($sqlselect);
$result->bindValue(':bvempfirstname', $formfield['ffempfirstname']);
$result->bindValue(':bvemplastname', $formfield['ffemplastname']);
//$result->bindValue(':bvempssn', $formfield['ffempssn']);
//$result->bindValue(':bvempstatus', $formfield['ffempstatus']);
//$result->bindValue(':bvempphone', $formfield['ffempphone']);
//$result->bindValue(':bvempcellphone', $formfield['ffempcellphone']);
//$result->bindValue(':bvempaddress1', $formfield['ffempaddress1']);
//$result->bindValue(':bvempaddress2', $formfield['ffempaddress2']);
//$result->bindValue(':bvempcity', $formfield['ffempcity']);
$result->bindValue(':bvstatus', 1);
//$result->bindValue(':bvemplocation', $formfield['ffemplocation']);
//$result->bindValue(':bvempjob', $formfield['ffempjob']);
//$result->bindValue(':bvempzip', $formfield['ffempzip']);
//$result->bindValue(':bvemphiredate', $formfield['ffemphiredate']);
//$result->bindValue(':bvempstate', $formfield['ffempstate']);

/*
if ($formfield['ffemphiredate']  !='') {
$result->bindValue(':bvemphiredate', $formfield['ffemphiredate']); }

if ($formfield['ffempaddress1']  !='') {
$result->bindValue(':bvempaddress1', $formfield['ffempaddress1']); }

if ($formfield['ffempaddress2']  !='') {
$result->bindValue(':bvempaddress2', $formfield['ffempaddress2']); }

if ($formfield['ffempcity']  !='') {
$result->bindValue(':bvempcity', $formfield['ffempcity']); }

if ($formfield['ffempstate']  !='') {
$result->bindValue(':bvempstate', $formfield['ffempstate']); }

if ($formfield['ffempzip']  !='') {
$result->bindValue(':bvempzip', $formfield['ffempzip']); }

if ($formfield['ffempphone']  !='') {
$result->bindValue(':bvempphone', $formfield['ffempphone']); }

if ($formfield['ffempcellphone']  !='') {
$result->bindValue(':bvempcellphone', $formfield['ffempcellphone']); }
*/
if ($formfield['ffempstatus']  !='') {
$result->bindValue(':bvempstatus', $formfield['ffempstatus']); }

if ($formfield['ffemplocation']  !='') {
$result->bindValue(':bvemplocation', $formfield['ffemplocation']); }

if ($formfield['ffempjob']  !='') {
$result->bindValue(':bvempjob', $formfield['ffempjob']); }
/*
if ($formfield['ffempssn']  !='') {
$result->bindValue(':bvempssn', $formfield['ffempssn']); }

if ($formfield['ffempfirstname']  !='') {
$result->bindValue(':bvempfirstname', $formfield['ffempfirstname']); }

if ($formfield['ffemplastname']  !='') {
$result->bindValue(':bvemplastname', $formfield['ffemplastname']); }
*/
$result->execute();
//Now here, if the select clause is successful, but finds no records at all
		//matching the search criteria, then we display the error message and reset the
		//formtype to 3, which will simply hold the values entered by the user.
		$count = $result->rowCount();
		if ($count <1)
		{
			echo '<div class = "container ps-5">';
			echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
			echo "No such record exists!" . '</div></div>';
			$formtype = 2;
		} else if ($count >=1)
		{
			echo '<div class = "container ps-5">';
			echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
			echo "Results found." . '</div></div>';
			$formtype = 3;
		}
	}	
} // ends if isset


/***************************************************************************************/
/*Notice here that we have the $showform variables according to the user permissions set
above for the current user logged in. The $formtype 1 will be the default type of form,
and the drop down menus will be based on who is currently logged in.*/
if ((($showform == 1) || ($showform == 2) || ($showform == 5) || ($showform == 6)
		|| ($showform == 7) || ($showform == 8)) && ($formtype == 1))
{	
?>
<!--------This is the default form that shows up with permissible users---------------------------------------->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3" style = "font-family: 'Ysabeau SC', sans-serif;">Employee Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
						<form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "empfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee First Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empfirstname" id="empfirstname" class="form-control" value="<?php echo $formfield['ffempfirstname']; ?>" 
														placeholder = "Search Employee First Name">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "emplastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Last Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="emplastname" id="emplastname" class="form-control" value="<?php echo $formfield['ffemplastname']; ?>"
														placeholder = "Search Employee Last Name">
                                                </div>
                                            </div>
											<div class="col-sm-9">
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3">
                                               <button type="submit" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empstatus" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee Status</label>
												<select class="form-select border-dark" id="empstatus" name="empstatus">
										<!--		<option value="">SELECT STATUS</option>-->
												<?php while ($rowstatus = $resultstatus->fetch() )
												{
												if ($rowstatus['dbstatusID'] == $formfield['ffempstatus'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowstatus['dbstatusID'] . '"' . $checker . '>' .
												$rowstatus['dbstatusName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
										   <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empjob" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Job Title</label>
												<select class="form-select border-dark" id="empjob" name="empjob">
										<!--		<option value="">SELECT POSITION</option>-->
												<?php while ($employeejob = $employeejobs->fetch() )
												{
												if ($employeejob['dbjobID'] == $formfield['ffempjob'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $employeejob['dbjobID'] . '"' . $checker . '>' . 
												$employeejob['dbjobName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="emplocation" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Location</label>
												<select class="form-select border-dark" id="emplocation" name="emplocation">
										<!--		<option value="">SELECT LOCATION</option>-->
												<?php 
												while ($row3 = $resultselectlo3->fetch() ){
												if ($row3['dblocationID'] == $formfield['ffemplocation'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $row3['dblocationID'] . '"' .
												$checker . '>' . $row3['dblocationName'] . '</option>';
												}
												?>
												</select>
											 </div>
										</div>
                                </div>
                            </div>
						</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
<?php 
}#1
/***************************************************************************************/
/*Here, the form that will display indicates the fact that the submit button was pressed 
by the user and the formfields will hold the values entered by the current user logged in.*/
else if ((($showform == 1) || ($showform == 2) || ($showform == 5) || ($showform == 6)
		|| ($showform == 7) || ($showform == 8)) && ($formtype == 2))
{	
?>
<!--Form displayed if no records are found--->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3" style = "font-family: 'Ysabeau SC', sans-serif;">Employee Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
						<form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "empfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee First Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empfirstname" id="empfirstname" class="form-control" value="<?php echo $formfield['ffempfirstname']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "emplastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Last Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="emplastname" id="emplastname" class="form-control" value="<?php echo $formfield['ffemplastname']; ?>">
                                                </div>
                                            </div>
											<div class="col-sm-9">
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3">
                                               <button type="submit" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empstatus" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee Status</label>
												<select class="form-select border-dark" id="empstatus" name="empstatus">
										<!--		<option value="">SELECT STATUS</option>-->
												<?php while ($rowstatus = $resultstatus->fetch() )
												{
												if ($rowstatus['dbstatusID'] == $formfield['ffempstatus'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowstatus['dbstatusID'] . '"' . $checker . '>' .
												$rowstatus['dbstatusName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
										   <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empjob" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Job Title</label>
												<select class="form-select border-dark" id="empjob" name="empjob">
										<!--		<option value="">SELECT POSITION</option>-->
												<?php while ($employeejob = $employeejobs->fetch() )
												{
												if ($employeejob['dbjobID'] == $formfield['ffempjob'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $employeejob['dbjobID'] . '"' . $checker . '>' . 
												$employeejob['dbjobName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="emplocation" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Location</label>
												<select class="form-select border-dark" id="emplocation" name="emplocation">
											<!--<option value="">SELECT LOCATION</option>-->
												<?php 
												while ($row3 = $resultselectlo3->fetch() ){
												if ($row3['dblocationID'] == $formfield['ffemplocation'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $row3['dblocationID'] . '"' .
												$checker . '>' . $row3['dblocationName'] . '</option>';
												}
												?>
												</select>
											 </div>
										</div>
                                </div>
                            </div>
						</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
<?php 
}#2
/***********************************************************************************************/
/*The form displayed indicates the fact that at least one record was found when search was done.*/
else if ((($showform == 1) || ($showform == 2) || ($showform == 5) || ($showform == 6)
		|| ($showform == 7) || ($showform == 8)) && ($formtype == 3))
{	
?>
<!--Form displayed along with search results below-->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3" style = "font-family: 'Ysabeau SC', sans-serif;">Employee Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
						<form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "empfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee First Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empfirstname" id="empfirstname" class="form-control" value="<?php echo $formfield['ffempfirstname']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "emplastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Last Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="emplastname" id="emplastname" class="form-control" value="<?php echo $formfield['ffemplastname']; ?>">
                                                </div>
                                            </div>
											<div class="col-sm-9">
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3">
                                               <button type="submit" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empstatus" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee Status</label>
												<select class="form-select border-dark" id="empstatus" name="empstatus">
									<!--		<option value="">SELECT STATUS</option>-->
												<?php while ($rowstatus = $resultstatus->fetch() )
												{
												if ($rowstatus['dbstatusID'] == $formfield['ffempstatus'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowstatus['dbstatusID'] . '"' . $checker . '>' .
												$rowstatus['dbstatusName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
										   <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empjob" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Job Title</label>
												<select class="form-select border-dark" id="empjob" name="empjob">
										<!--	<option value="">SELECT POSITION</option>-->
												<?php while ($employeejob = $employeejobs->fetch() )
												{
												if ($employeejob['dbjobID'] == $formfield['ffempjob'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $employeejob['dbjobID'] . '"' . $checker . '>' . 
												$employeejob['dbjobName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="emplocation" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Location</label>
												<select class="form-select border-dark" id="emplocation" name="emplocation">
										<!--	<option value="">SELECT LOCATION</option>-->
												<?php 
												while ($row3 = $resultselectlo3->fetch() ){
												if ($row3['dblocationID'] == $formfield['ffemplocation'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $row3['dblocationID'] . '"' .
												$checker . '>' . $row3['dblocationName'] . '</option>';
												}
												?>
												</select>
											 </div>
										</div>
                                </div>
                            </div>
						</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
	<div class = "container">
<table class = "table table-striped table-bordered">
<thead class="thead-dark border border-dark bg-dark text-light">
	<tr>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Employee SSN</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">DOH</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Job Title</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Location</th>
<!--	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Pay Rate</th>-->
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">First Name</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">MI</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Last Name</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Address</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Address 2</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">City</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">State</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">ZIP Code</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Home Phone</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Cell Phone</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">&nbsp;</th>
	</tr>
</thead>
<tbody class = "border border-dark">
	
<?php
while ( $row = $result-> fetch() )
	{
	
	echo '<tr><td>' . $row['dbemployeeSSN'] . '</td><td>' . $row['dbemployeeHireDate'] . '</td><td>' . $row['dbjobName'] . 
		'</td><td>' . $row['dblocationName'] . '</td><td>' . $row['dbemployeeFirstName'] . 
		'</td><td>' . $row['dbemployeeMI'] . '</td><td>' . $row['dbemployeeLastName'] . '</td><td>' . $row['dbemployeeAddress1'] .
		'</td><td>' . $row['dbemployeeAddress2'] . '</td><td>' . $row['dbemployeeCity'] . '</td><td>' . $row['dbstateAbrev'] . 
		'</td><td>' . $row['dbemployeeZip'] . '</td><td>' . $row['dbemployeeHomePhone'] . '</td><td>' .  $row['dbemployeeCellPhone'] . 
		'</td><td>' .
		//This will make a button that sends the user to another page "updateemployee.php"	  
			'<form action = "boot_updateemployee.php" method = "post">
				<input type = "hidden" name = "empid" value = "' . $row['dbemployeeID'] . '">
				<input type = "hidden" name = "jobid" value = "' . $row['dbjobID'] . '">
				<input type = "submit" class = "rounded-3 bg-dark text-light" name = "theedit" value = "EDIT">
			</form>' . '</td></tr>';	  
	}	
?>
</tbody>
</table>
</div>
<?php 
}#3

// If permission level is indicative of the rest of staff members (no authorization)
else if ($showform == 3) {
	echo '<div class = "container ps-5">';
	echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
	echo "You do not have authorization to access this page!" . '</div></div>';
} // ends if $showform 3
// Indicates the user is not logged in, so log in form will show
if ($showform == 4) {
//Login form
?>
<div class="d-flex flex-column min-vh-100 justify-content-center align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
				 <h3 class="mb-3" style = "font-family: 'Ysabeau SC', sans-serif;">Login Now</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="form-left h-100 py-5 px-5">
                                    <form name = "loginform" id="loginform" method= "post" action="boot_login.php" class="row g-4">
                                            <div class="col-12">
                                                <label for = "empusername" class ="text-light pb-1" style="font-family: 'Raleway', sans-serif;">Username<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="bi bi-person-fill"></i></div>
                                                    <input type="text" name ="empusername" id="empusername" class="form-control" placeholder="Enter Username" required>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <label for = "emppassword" class ="text-light pb-1" style="font-family: 'Raleway', sans-serif;">Password<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="bi bi-lock-fill"></i></div>
                                                    <input type="password" name="emppassword" id="emppassword" class="form-control" placeholder="Enter Password" required>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="inlineFormCheck">
                                                    <label style="font-family: 'Raleway', sans-serif;" class="form-check-label text-light" for="inlineFormCheck">Remember me</label>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <a href="#" class="float-end text-light" style="font-family: 'Raleway', sans-serif;">Forgot Password?</a>
                                            </div>

                                            <div class="col-12">
                                                <button type="submit" name="thelogin" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">LOGIN</button>
                                            </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-5">
                                    			<img id="loginlogo" alt="Login Logo" class = "pt-4" style="height: 270px; vertical-align: middle;" src="./images/mainlogo3.svg">

								 </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
include 'boot_footer.php';
?>