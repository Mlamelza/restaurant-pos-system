<?php
$pagetitle = 'Add Employee';
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

// Setting our variable to an empty string, which will be used to display messages later
$errormsg = "";
$formtype = 1;

//For our state drop down menu
$sqlselectstate = "SELECT * from state";
$resultstate = $db->prepare($sqlselectstate);
$resultstate->execute();

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
 
//echo $row3['dblocationName'];

/*foreach($resultselectlo as $location){
	echo $location['dblocationID'] . ' ';
	 echo $location['dblocationName'] . '<br>';
}*/
/*******************************************************************************/
/* Here we have our queries to our DB, gathering data needed for our drop downs
Notice that based on the logged in user permissions, the specific job titles
will be pulled from the database accordingly. If the logged in user is in an
Administrative position, all job titles will be pulled from the DB.*/
if ($showform == 5)  {
$queryJobs = 'SELECT job.dbjobID, job.dbjobName FROM job
          ORDER BY dbjobName ASC'; // Listing by job name alphabetical order
$employeeJobs = $db->prepare($queryJobs);
$employeeJobs->execute();
}
//If the current user is a Regional Manager, all job titles except for the 
//Administrative or Regional Manager positions will be pulled from our DB.
else if ($showform == 7)  {
$queryJobs = 'SELECT job.dbjobID, job.dbjobName FROM job WHERE job.dbjobID != 18
	&& job.dbjobID != 1 ORDER BY dbjobName ASC'; // Listing by job name alphabetical order
$employeeJobs = $db->prepare($queryJobs);
$employeeJobs->execute();
}
//If the logged in user is a GM, all job titles will be pulled from our DB, except 
//for the Administrative, Regional, AM, or General Manager titles.
else if ($showform == 1) {
$queryJobs = 'SELECT job.dbjobID, job.dbjobName FROM job WHERE job.dbjobID !=1 && job.dbjobID
	!= 18 && job.dbjobID !=2 && job.dbjobID !=3 ORDER BY dbjobName ASC'; // Listing by job name alphabetical order
$employeeJobs = $db->prepare($queryJobs);
$employeeJobs->execute();
}
/*If the current user logged in is an Assistant Manager, the job titles that will be pulled 
will include hourly positions, as well as Shift Leader & Bar Manager. This is because only the
GM, Regional Manager, and Administrators will be permitted to enter managerial newhires.*/
else if ($showform == 8) {
$queryJobs = 'SELECT job.* FROM job WHERE job.dbjobID !=1 && job.dbjobID !=2 &&
	job.dbjobID != 18 && job.dbjobID != 3 && job.dbjobID != 9 
	ORDER BY dbjobName ASC'; // Listing by job name alphabetical order
$employeeJobs = $db->prepare($queryJobs);
$employeeJobs->execute();
}/*If the current user logged in is either a Bar Manager, or Shift Leader, the job titles 
that will be pulled will only include hourly positions.*/
else if ($showform == 6) {
$queryJobs = 'SELECT job.* FROM job WHERE job.dbjobID !=1 && job.dbjobID !=2 && job.dbjobID !=3 &&
	job.dbjobID != 18 && job.dbjobID != 9 && job.dbjobID != 4 && job.dbjobID != 5
	ORDER BY dbjobName ASC'; // Listing by job name alphabetical order
$employeeJobs = $db->prepare($queryJobs);
$employeeJobs->execute();
}

/*If the current user logged in is a Kitchen Manager, and editing is allowed, the job titles 
that will be pulled will be identical to those of SL and Bar Managers.*/
else if ($showform == 2) {
$queryJobs = 'SELECT job.* FROM job WHERE job.dbjobID !=1 && job.dbjobID !=2 && job.dbjobID !=3 &&
	job.dbjobID != 18 && job.dbjobID != 9 && job.dbjobID != 4 && job.dbjobID != 5
	ORDER BY dbjobName ASC'; // Listing by job name alphabetical order
$employeeJobs = $db->prepare($queryJobs);
$employeeJobs->execute();
}

/*
while($jobs = $employeeJobs->fetch())
{	
	echo $jobs['dbjobID'] . ' ';
	 echo $jobs['dbjobName'] . '<br>';
}*/

/**Notice here that if the clear button is pressed, the form type 
that is displayed will become the $formtype of 1. The same applies 
if the new submit button is pressed. This means that the user would
essentially start over and enter a new employee record into the DB.
This clears the input boxes and drop downs so the user can start over
and add a new record to our DB table.
**/
if (isset($_POST['theclear']))	{
	$formtype = 1; 
}

if (isset($_POST['thenewsubmit']))	{
	$formtype = 1; 
}

/**Here we are checking to see if the Submit button was entered, indicating a new
employee entry, given the permission to do so. We will check and see if any of the
required fields are empty, and we will clean the data before being entered.
**/
if (isset($_POST['thesubmit']))	{
    //Cleaning the data
    $formfield['ffempssn'] = trim($_POST['empssn']);
    $formfield['ffemphiredate'] = trim($_POST['emphiredate']);
    $formfield['ffempjob'] = ($_POST['empjob']);
    $formfield['ffemplocation'] = ($_POST['emplocation']);
    $formfield['ffemppay'] = ($_POST['emppay']);
	$formfield['ffempfirstname'] = trim($_POST['empfirstname']);
    $formfield['ffempmiddle'] = trim($_POST['empmiddle']);
    $formfield['ffemplastname'] = trim($_POST['emplastname']);
	$formfield['ffempaddress1'] = trim($_POST['empaddress1']);
    $formfield['ffempaddress2'] = trim($_POST['empaddress2']);
    $formfield['ffempcity'] = trim($_POST['empcity']);
    $formfield['ffempstate'] = ($_POST['empstate']);
    $formfield['ffempzip'] = trim($_POST['empzip']);
    $formfield['ffempphone'] = trim(strtolower($_POST['empphone']));
	$formfield['ffempcellphone'] = trim(strtolower($_POST['empcellphone']));

    //Checking to see if the required fields are empty
	if (empty($formfield['ffempssn'])) {
        $errormsg .= "Your SSN is empty! ";
    }
    if (empty($formfield['ffemphiredate'])) {
        $errormsg .= "Your Hire Date is empty! ";
    }
    if (empty($formfield['ffempjob'])) {
        $errormsg .= "Your Job Title is empty! ";
    }
	if (empty($formfield['ffemplocation'])) {
        $errormsg .= "Your Location is empty! ";
	}	
	if (empty($formfield['ffemppay'])) {
        $errormsg .= "Your Pay Rate is empty! ";
    }
    if (empty($formfield['ffempfirstname'])) {
        $errormsg .= "Your First Name is empty! ";
    }
    if (empty($formfield['ffemplastname'])) {
        $errormsg .= "Your Last Name is empty! ";
    }
	if (empty($formfield['ffempaddress1'])) {
        $errormsg .= "Your Address field is empty! ";
	}
	if (empty($formfield['ffempcity'])) {
        $errormsg .= "Your City is empty! ";
    }
	if (empty($formfield['ffempstate'])) {
        $errormsg .= "Your State is empty! ";
    }
	if (empty($formfield['ffempzip'])) {
        $errormsg .= "Your ZIP Code is empty! ";
    }
	if (empty($formfield['ffempphone'])) {
        $errormsg .= "Your Phone is empty! ";
    }
	
	//Validating the SSN/////////////
	$ssnTrim = $formfield['ffempssn'];
	
	// Must be in format AAA-GG-SSSS or AAAGGSSSS
    if ( ! preg_match("/^([0-9]{9}|[0-9]{3}-[0-9]{2}-[0-9]{4})$/", $ssnTrim)) {
        $errormsg .= "Your SSN format is not valid! ";
    } else {
	
	// Split groups into an array
    $ssnFormat = (strlen($ssnTrim) == 9) ? preg_replace("/^([0-9]{3})([0-9]{2})([0-9]{4})$/", "$1-$2-$3", $ssnTrim) : $ssnTrim;
    $ssn_array = explode('-', $ssnFormat);
	
	// number groups must follow these rules:
    // * no single group can have all 0's
    // * first group cannot be 666, 900-999
    // * second group must be 01-99
    // * third group must be 0001-9999
	
	foreach ($ssn_array as $group) {
        if ($group == 0) {
            $errormsg .= "Your SSN is not valid! ";
        }
    }
	
	if ($ssn_array[0] == 666 || $ssn_array[0] > 899) {
        $errormsg .= "Your SSN cannot be accepted! ";
		}
	}
	///////////////////////////////////////////////////////

    //Looking for duplicate SSN
    $checkssn = "SELECT * FROM employee 
	           WHERE dbemployeeSSN = '" . $formfield['ffempssn'] . "' ";
    $result = $db->query($checkssn);
    $count = $result->rowCount();

    if ($count > 0) {
        $errormsg .= "SSN already exists. Please enter a new SSN! ";
    }
	
	//****************************************************************************************************************************************
	
	    //Output errors (If any)
		//Notice that if there is an error message, meaning that one of the required
		//fields is empty, the form changes to $formtype 3, which is essentially the 
		//same, but displaying exactly what the user has entered into the fields.
    if ($errormsg != "") {
		echo '<div class = "container ps-5">';
		echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
		echo "There are errors! " . $errormsg . '</div></div>';
	//	echo $errormsg;
		$formtype = 3;
	}	else
	//If there are no errors, we will try to insert our data into the database. But first, we
	//want to pull our max value from our employee id field so that we will know what the current
	//customer id will be for our current employee data record being entered into our DB.
		{
			
        //Beginning the try/catch. Begin Try
        try {
			
            //Entering info into the database
            $sqlinsert = 'INSERT INTO employee (dbemployeeSSN, dbemployeeLastName, dbemployeeFirstName, dbemployeeMI, 
				dbemployeeHomePhone, dbemployeeCellPhone, dbemployeeAddress1, dbemployeeAddress2, dbemployeeCity,
				dbemployeeZip, dbemployeeHireDate, dbstateID, dbstatusID)
	                          VALUES (:bvempssn, :bvemplastname, :bvempfirstname, :bvempmi, :bvempphone,
								:bvempcellphone, :bvempaddress1, :bvempaddress2, :bvempcity,
	                                :bvempzip, :bvemphiredate, :bvempstate, :bvempstatus)';
            $stmtinsert = $db->prepare($sqlinsert);
			$stmtinsert->bindvalue(':bvempssn', $formfield['ffempssn']);
            $stmtinsert->bindvalue(':bvemplastname', $formfield['ffemplastname']);
            $stmtinsert->bindvalue(':bvempfirstname', $formfield['ffempfirstname']);
			$stmtinsert->bindvalue(':bvempmi', $formfield['ffempmiddle']);
            $stmtinsert->bindvalue(':bvempphone', $formfield['ffempphone']);
            $stmtinsert->bindvalue(':bvempcellphone', $formfield['ffempcellphone']);
            $stmtinsert->bindvalue(':bvempaddress1', $formfield['ffempaddress1']);
            $stmtinsert->bindvalue(':bvempaddress2', $formfield['ffempaddress2']);
            $stmtinsert->bindvalue(':bvempcity', $formfield['ffempcity']);
            $stmtinsert->bindvalue(':bvempzip', $formfield['ffempzip']);
			$stmtinsert->bindvalue(':bvemphiredate', $formfield['ffemphiredate']);
			$stmtinsert->bindvalue(':bvempstate', $formfield['ffempstate']);
			$stmtinsert->bindvalue(':bvempstatus', 1);
            $stmtinsert->execute();
			
            //echo "<div class='success'><p>There are no errors!</p></div>";
			//Once the newly entered data has succeeded, we pull the newly entered
			//id value, indicating the newly entered employee record id.
			$sqlmax = "SELECT MAX(dbemployeeID) AS maxid FROM employee";
					$resultmax = $db->prepare($sqlmax);
					$resultmax->execute();
					$rowmax = $resultmax->fetch();
					$maxid = $rowmax["maxid"];
			
			//Now we insert our location associated with our new employee record
			$locationinsert = 'INSERT INTO locationDetail (dblocationID, dbemployeeID)
	                          VALUES (:bvemplocation, :bvempid)';
            $stmtlocationinsert = $db->prepare($locationinsert);
			$stmtlocationinsert->bindvalue(':bvemplocation', $formfield['ffemplocation']);
			$stmtlocationinsert->bindvalue(':bvempid', $maxid);
			$stmtlocationinsert->execute();
			
			//Now we insert our job associated with our new employee record
			//Notice here there is another status that will be entered into 
			//our jobDetail table, that will be associated with the job position
			$jobinsert = 'INSERT INTO jobDetail (dbjobID, dbemployeeID, dbjobstatusID)
	                          VALUES (:bvempjob, :bvempid, :bvstatus)';
            $stmtjobinsert = $db->prepare($jobinsert);
			$stmtjobinsert->bindvalue(':bvempjob', $formfield['ffempjob']);
			$stmtjobinsert->bindvalue(':bvempid', $maxid);
			$stmtjobinsert->bindvalue(':bvstatus', 1);
			$stmtjobinsert->execute();
			
			//Now we select our jobDetailID value from our jobDetail table that is
			//associated with the employee record and job just entered. This way we
			//can successfully enter a pay rate associated with this jobDetailID.
			$jobdetailselect = 'SELECT jobDetail.* FROM jobDetail, employee, status,
				job WHERE jobDetail.dbjobID = job.dbjobID AND jobDetail.dbemployeeID =
				employee.dbemployeeID AND jobDetail.dbjobstatusID = status.dbstatusID 
				AND job.dbjobID = :bvempjob AND employee.dbemployeeID = :bvempid';
			$stmtjobdetail = $db->prepare($jobdetailselect);
			$stmtjobdetail->bindvalue(':bvempjob', $formfield['ffempjob']);
			$stmtjobdetail->bindvalue(':bvempid', $maxid);
			$stmtjobdetail->execute();
			$rowjobdetail = $stmtjobdetail ->fetch();
			$jobdetailid = $rowjobdetail['dbjobDetailID'];
			
			//We finally insert our pay rate for the job associated with the new employee
			//record, by inserting our retrieved jobDetailID value and pay rate.
			$payrateinsert = 'INSERT INTO payRateDetail (dbjobDetailID, dbpayRate)
	                          VALUES (:bvjobdetailid, :bvpayrate)';
            $stmtpayrateinsert = $db->prepare($payrateinsert);
			$stmtpayrateinsert->bindvalue(':bvjobdetailid', $jobdetailid);
			$stmtpayrateinsert->bindvalue(':bvpayrate', $formfield['ffemppay']);
			$stmtpayrateinsert->execute();
			
			//Successful data insert message
			echo '<div class = "container ps-5">';
			echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
			echo "There are no errors! Form has been submitted." . '</div></div>';
		
			//Once the data has been inserted successfully, we select all of this newly 
			//inserted data record so we can display it in our table below.				
			$sqlselect = 'SELECT employee.*, state.*, job.*, location.*, payRateDetail.*,
				status.* from employee, state, job, jobDetail, location, locationDetail, status,
				payRateDetail WHERE employee.dbemployeeID = :bvempid AND employee.dbstateID = state.dbstateID 
				AND jobDetail.dbjobID = job.dbjobID AND jobDetail.dbemployeeID = employee.dbemployeeID AND 
				locationDetail.dblocationID = location.dblocationID AND locationDetail.dbemployeeID = 
				employee.dbemployeeID AND payRateDetail.dbjobDetailID = jobDetail.dbjobDetailID AND
				employee.dbstatusID = status.dbstatusID AND job.dbjobID = :bvempjob AND 
				location.dblocationID = :bvemplocation AND jobDetail.dbjobDetailID =
				:bvjobdetailid';
			$result = $db->prepare($sqlselect);
			$result->bindValue(':bvempid', $maxid);
			$result->bindValue(':bvempjob', $formfield['ffempjob']);
			$result->bindValue(':bvemplocation', $formfield['ffemplocation']);
			$result->bindValue(':bvjobdetailid', $jobdetailid);
			$result->execute();
			$row = $result->fetch();
			$formtype = 2;
			/*Notice that once the newly entered record into our DB is successful,
			the new record will display utilizing the SQL statement here. The form
			that is displayed changes once again to equal $formtype 2, which now
			displays the data pulled directly from our DB.*/
		//	echo $row['dbemployeeID'];
		//	echo $row['dbemployeeFirstName'];
			
        }//Try ends
            //Catch begins
        catch (PDOException $e) {
            echo 'ERROR!' . $e->getMessage();
            exit();
        }//Catch ends
    }//If statement ends
} //If isset ends


// Indicates Managerial-type positions (including Shift Leader) except Admin and Regional
if ((($showform == 1) || ($showform == 2) || ($showform == 6) || ($showform == 8))  && ($formtype == 1))
{
	//We will gather the last entry id in our employee table and add 1 to it in order
	//to obtain the current new employee entry if successfully inserted into our DB.
	$sqlmax = "SELECT MAX(dbemployeeID) AS maxid FROM employee";
					$resultmax = $db->prepare($sqlmax);
					$resultmax->execute();
					$rowmax = $resultmax->fetch();
					$maxid = $rowmax["maxid"]; // Now we have our last item id that was inserted into the item table
					$newid = $maxid + 1;
?> 
<!--default form for permissible users except Admins and Regionals-->
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
                                                <label for = "empssn" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee SSN<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empssn" id="empssn" class="form-control" placeholder = "555555555" pattern="\d{3}?\d{2}?\d{4}" 
														value="<?php echo $formfield['ffempssn']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee First Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empfirstname" id="empfirstname" class="form-control" placeholder = "Enter Employee First Name" 
														value="<?php echo $formfield['ffempfirstname']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empmiddle" class ="text-light" style="font-family: 'Raleway', sans-serif;">Middle Initial</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empmiddle" id="empmiddle" class="form-control" placeholder = "Enter Middle Initial"
													value="<?php echo $formfield['ffempmiddle']; ?>" >
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "emplastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Last Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="emplastname" id="emplastname" class="form-control" placeholder = "Enter Employee Last Name" 
														value="<?php echo $formfield['ffemplastname']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empaddress1" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Address<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empaddress1" id="empaddress1" class="form-control" placeholder = "Enter Employee Address" 
														value="<?php echo $formfield['ffempaddress1']; ?>" required >
                                                </div>
                                            </div> 
											<div class="col-12">
                                                <label for = "empaddress2" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Address 2</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empaddress2" id="empaddress2" class="form-control" placeholder = "Enter Employee Address 2"
														value="<?php echo $formfield['ffempaddress2']; ?>">
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empcity" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee City<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empcity" id="empcity" class="form-control" placeholder = "Enter Employee City" 
														value="<?php echo $formfield['ffempcity']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-sm-9">
											<!--	<input type="hidden" name = "custid" value = "<?php echo $newid ?>" />-->
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3">
                                               <button type="reset" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee State<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="empstate" name="empstate" required>
												<option value="">SELECT STATE</option>
												<?php while ($rowstate = $resultstate->fetch() )
												{
												if ($rowstate['dbstateID'] == $formfield['ffempstate'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowstate['dbstateID'] . '"' . $checker . '>' .
												$rowstate['dbstateName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-4">
                                                <label for = "empzip"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee ZIP Code<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="empzip" id="empzip" class="form-control border-dark" placeholder = "Enter Five-Digit ZIP Code" pattern="[0-9]{5}" 
														value="<?php echo $formfield['ffempzip']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "empphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee Home Phone<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="empphone" id="empphone" class="form-control border-dark" placeholder = "1234567890" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" 
														value="<?php echo $formfield['ffempphone']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "empcellphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee Cell Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="empcellphone" id="empcellphone" class="form-control border-dark" placeholder = "1234567890" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}"
														value="<?php echo $formfield['ffempcellphone']; ?>">
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "emphiredate"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Date of Hire<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="date" name ="emphiredate" id="emphiredate" class="form-control border-dark" value="<?php echo $formfield['ffemphiredate']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empjob" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Job Title<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="empjob" name="empjob" required>
												<option value="">SELECT POSITION</option>
												<?php while ($employeeJob = $employeeJobs->fetch() )
												{
													if ($employeeJob['dbjobID'] == $formfield['ffempjob'])
													{$checker = 'selected';}
													else {$checker = '';}
													echo '<option value="'. $employeeJob['dbjobID'] . '"' . $checker . '>' . 
													$employeeJob['dbjobName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="emplocation" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Location<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="emplocation" name="emplocation" required>
												<option value="">SELECT LOCATION</option>
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
											<div class="col-12 pb-4">
                                                <label for = "emppay" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Pay Rate<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="number" min="0.00" max="50.00" step="0.01" name ="emppay" id="emppay" class="form-control border-dark" required
														placeholder = "99.99" value="<?php echo $formfield['ffemppay']; ?>" pattern = "^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$">
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
<div class = "container pt-5"></div>
<?php
}#1-1
//Indicates Administrative and Regional Manager roles
else if ((($showform == 5) || ($showform == 7))  && ($formtype == 1))
{
	//We will gather the last entry id in our employee table and add 1 to it in order
	//to obtain the current new employee entry if successfully inserted into our DB.
	$sqlmax = "SELECT MAX(dbemployeeID) AS maxid FROM employee";
					$resultmax = $db->prepare($sqlmax);
					$resultmax->execute();
					$rowmax = $resultmax->fetch();
					$maxid = $rowmax["maxid"]; // Now we have our last item id that was inserted into the item table
					$newid = $maxid + 1;
?> 
<!--default form for Admins and Regionals-->
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
                                                <label for = "empssn" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee SSN<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empssn" id="empssn" class="form-control" placeholder = "555555555" pattern="\d{3}?\d{2}?\d{4}" 
														value="<?php echo $formfield['ffempssn']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee First Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empfirstname" id="empfirstname" class="form-control" placeholder = "Enter Employee First Name" 
														value="<?php echo $formfield['ffempfirstname']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empmiddle" class ="text-light" style="font-family: 'Raleway', sans-serif;">Middle Initial</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empmiddle" id="empmiddle" class="form-control" placeholder = "Enter Middle Initial"
													value="<?php echo $formfield['ffempmiddle']; ?>" >
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "emplastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Last Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="emplastname" id="emplastname" class="form-control" placeholder = "Enter Employee Last Name" 
														value="<?php echo $formfield['ffemplastname']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empaddress1" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Address<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empaddress1" id="empaddress1" class="form-control" placeholder = "Enter Employee Address" 
														value="<?php echo $formfield['ffempaddress1']; ?>" required >
                                                </div>
                                            </div> 
											<div class="col-12">
                                                <label for = "empaddress2" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Address 2</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empaddress2" id="empaddress2" class="form-control" placeholder = "Enter Employee Address 2"
														value="<?php echo $formfield['ffempaddress2']; ?>">
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empcity" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee City<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empcity" id="empcity" class="form-control" placeholder = "Enter Employee City" 
														value="<?php echo $formfield['ffempcity']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-sm-9">
											<!--	<input type="hidden" name = "custid" value = "<?php echo $newid ?>" />-->
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3">
                                               <button type="reset" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee State<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="empstate" name="empstate" required>
												<option value="">SELECT STATE</option>
												<?php while ($rowstate = $resultstate->fetch() )
												{
												if ($rowstate['dbstateID'] == $formfield['ffempstate'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowstate['dbstateID'] . '"' . $checker . '>' .
												$rowstate['dbstateName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-4">
                                                <label for = "empzip"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee ZIP Code<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="empzip" id="empzip" class="form-control border-dark" placeholder = "Enter Five-Digit ZIP Code" pattern="[0-9]{5}" 
														value="<?php echo $formfield['ffempzip']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "empphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee Home Phone<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="empphone" id="empphone" class="form-control border-dark" placeholder = "1234567890" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" 
														value="<?php echo $formfield['ffempphone']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "empcellphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee Cell Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="empcellphone" id="empcellphone" class="form-control border-dark" placeholder = "1234567890" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}"
														value="<?php echo $formfield['ffempcellphone']; ?>">
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "emphiredate"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Date of Hire<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="date" name ="emphiredate" id="emphiredate" class="form-control border-dark" value="<?php echo $formfield['ffemphiredate']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empjob" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Job Title<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="empjob" name="empjob" required>
												<option value="">SELECT POSITION</option>
												<?php while ($employeeJob = $employeeJobs->fetch() )
												{
													if ($employeeJob['dbjobID'] == $formfield['ffempjob'])
													{$checker = 'selected';}
													else {$checker = '';}
													echo '<option value="'. $employeeJob['dbjobID'] . '"' . $checker . '>' . 
													$employeeJob['dbjobName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="emplocation" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Location<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="emplocation" name="emplocation" required>
												<option value="">SELECT LOCATION</option>
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
											<div class="col-12 pb-4">
                                                <label for = "emppay" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Pay Rate<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="number" min="0.00" max="50.00" step="0.01" name ="emppay" id="emppay" class="form-control border-dark" required
														placeholder = "99.99" value="<?php echo $formfield['ffemppay']; ?>" pattern = "^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$">
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
<div class = "container pt-5"></div>
<?php
}#2-1
//This is going to be the form that displays when the submit button is entered by
//an administrator or member of management. However, this form will appear only if
//the data has been entered successfully into our DB. Notice the permissions allowed.
else if ((($showform == 1) || ($showform == 2) || ($showform >= 5)) && ($formtype == 2))
{ 
$sqlmax = "SELECT MAX(dbemployeeID) AS maxid FROM employee";
					$resultmax = $db->prepare($sqlmax);
					$resultmax->execute();
					$rowmax = $resultmax->fetch();
					$maxid = $rowmax["maxid"]; // Now we have our last item id that was inserted into the item table
				
					/***This is where we output our next id number***/
				//	echo $maxid;
					//echo $newid; // Our new customer id 
				//	$hello = "hello, formtype 2";;
				//	echo $hello;
?>
<!--Form displayed when data has been entered successfully into the DB-->
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
                                                <label for = "empssn" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee SSN<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empssn" id="empssn" class="form-control" pattern="\d{3}?\d{2}?\d{4}" 
														value = "<?php echo $row['dbemployeeSSN']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee First Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empfirstname" id="empfirstname" class="form-control" 
														value="<?php echo $row['dbemployeeFirstName']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empmiddle" class ="text-light" style="font-family: 'Raleway', sans-serif;">Middle Initial</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empmiddle" id="empmiddle" class="form-control" value="<?php echo $row['dbemployeeMI']; ?>" >
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "emplastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Last Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="emplastname" id="emplastname" class="form-control"
														value="<?php echo $row['dbemployeeLastName']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empaddress1" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Address<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empaddress1" id="empaddress1" class="form-control" 
														value="<?php echo $row['dbemployeeAddress1']; ?>" required >
                                                </div>
                                            </div> 
											<div class="col-12">
                                                <label for = "empaddress2" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Address 2</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empaddress2" id="empaddress2" class="form-control"
														value="<?php echo $row['dbemployeeAddress2']; ?>">
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empcity" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee City<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empcity" id="empcity" class="form-control"
														value="<?php echo $row['dbemployeeCity']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
											<!--	<input type="hidden" name = "custid" value = "<?php echo $newid ?>" />-->
                                               <button type="submit" name="thenewsubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">NEW ACCOUNT</button>
                                            </div>
										<!--	<div class="col-sm-3">
                                               <button type="reset" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>-->
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee State<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="empstate" name="empstate" required>
												<option value="">SELECT STATE</option>
												<?php while ($rowstate = $resultstate->fetch() )
												{
												if ($rowstate['dbstateID'] == $formfield['ffempstate'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowstate['dbstateID'] . '"' . $checker . '>' .
												$rowstate['dbstateName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-4">
                                                <label for = "empzip"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee ZIP Code<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="empzip" id="empzip" class="form-control border-dark" pattern="[0-9]{5}" 
														value="<?php echo $row['dbemployeeZip']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "empphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee Home Phone<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="empphone" id="empphone" class="form-control border-dark" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" 
														value="<?php echo $row['dbemployeeHomePhone']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "empcellphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee Cell Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="empcellphone" id="empcellphone" class="form-control border-dark" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}"
														value="<?php echo $row['dbemployeeCellPhone']; ?>">
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "emphiredate"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Date of Hire<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="date" name ="emphiredate" id="emphiredate" class="form-control border-dark" value="<?php echo $row['dbemployeeHireDate']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empjob" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Job Title<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="empjob" name="empjob" required>
												<option value="">SELECT POSITION</option>
												<?php while ($employeeJob = $employeeJobs->fetch() )
												{
													if ($employeeJob['dbjobID'] == $formfield['ffempjob'])
													{$checker = 'selected';}
													else {$checker = '';}
													echo '<option value="'. $employeeJob['dbjobID'] . '"' . $checker . '>' . 
													$employeeJob['dbjobName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="emplocation" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Location<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="emplocation" name="emplocation" required>
												<option value="">SELECT LOCATION</option>
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
											<div class="col-12 pb-4">
                                                <label for = "emppay" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Pay Rate<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="number" min="0.00" max="50.00" step="0.01" name ="emppay" id="emppay" class="form-control border-dark" required
														value="<?php echo $row['dbpayRate']; ?>" pattern = "^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$">
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
<div class = "container pt-5"></div>
<div class = "container">
<table class = "table table-striped table-bordered">
<thead class="thead-dark border border-dark bg-dark text-light">
	<tr>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Employee SSN</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">DOH</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Job Title</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Location</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Pay Rate</th>
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
	</tr>
</thead>
<tbody class = "border border-dark">
	
<?php
	
	echo '<tr><td>' . $row['dbemployeeSSN'] . '</td><td>' . $row['dbemployeeHireDate'] . '</td><td>' . $row['dbjobName'] . 
		'</td><td>' . $row['dblocationName'] . '</td><td>$' . $row['dbpayRate'] . '</td><td>' . $row['dbemployeeFirstName'] . 
		'</td><td>' . $row['dbemployeeMI'] . '</td><td>' . $row['dbemployeeLastName'] . '</td><td>' . $row['dbemployeeAddress1'] .
		'</td><td>' . $row['dbemployeeAddress2'] . '</td><td>' . $row['dbemployeeCity'] . '</td><td>' . $row['dbstateAbrev'] . 
		'</td><td>' . $row['dbemployeeZip'] . '</td><td>' . $row['dbemployeeHomePhone'] . '</td><td>' .  $row['dbemployeeCellPhone'] . 
		'</td></tr>';
?>
</tbody>
</table>
</div>
<?php
}#2
//Here, this $formtype 3 will display if there was an error message of some sort,
//so the form is essentially the same as $formtype 1, but the fields are clear, with
//no default placeholders. That way, the user can simply enter in the data they need
//to, filling in any of the required information that was missing.
else if ((($showform == 1) || ($showform == 2) || ($showform >= 5)) && ($formtype == 3))
{ 
$sqlmax = "SELECT MAX(dbemployeeID) AS maxid FROM employee";
					$resultmax = $db->prepare($sqlmax);
					$resultmax->execute();
					$rowmax = $resultmax->fetch();
					$maxid = $rowmax["maxid"]; // Now we have our last item id that was inserted into the item table
					$newid = $maxid + 1;
					//echo $newid; // Our new customer id 
				//	$hello = " Hi this is formtype 3 ";
				//	echo $hello;
?>
<!--form that will display when an error has occurred-->
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
                                                <label for = "empssn" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee SSN<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empssn" id="empssn" class="form-control" pattern="\d{3}?\d{2}?\d{4}" 
														value="<?php echo $formfield['ffempssn']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee First Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empfirstname" id="empfirstname" class="form-control"
														value="<?php echo $formfield['ffempfirstname'];?>" required >
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empmiddle" class ="text-light" style="font-family: 'Raleway', sans-serif;">Middle Initial</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empmiddle" id="empmiddle" class="form-control"
													value="<?php echo $formfield['ffempmiddle']; ?>" >
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "emplastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Last Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="emplastname" id="emplastname" class="form-control"
														value="<?php echo $formfield['ffemplastname']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empaddress1" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Address<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empaddress1" id="empaddress1" class="form-control"
														value="<?php echo $formfield['ffempaddress1']; ?>" required >
                                                </div>
                                            </div> 
											<div class="col-12">
                                                <label for = "empaddress2" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee Address 2</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empaddress2" id="empaddress2" class="form-control"
														value="<?php echo $formfield['ffempaddress2']; ?>">
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "empcity" class ="text-light" style="font-family: 'Raleway', sans-serif;">Employee City<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="empcity" id="empcity" class="form-control"
														value="<?php echo $formfield['ffempcity']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12">
											<!--	<input type="hidden" name = "custid" value = "<?php echo $newid ?>" />-->
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
										<!--	<div class="col-sm-3">
                                               <button type="submit" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>-->
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee State<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="empstate" name="empstate" required>
												<option value="">SELECT STATE</option>
												<?php while ($rowstate = $resultstate->fetch() )
												{
												if ($rowstate['dbstateID'] == $formfield['ffempstate'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowstate['dbstateID'] . '"' . $checker . '>' .
												$rowstate['dbstateName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-4">
                                                <label for = "empzip"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee ZIP Code<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="empzip" id="empzip" class="form-control border-dark" pattern="[0-9]{5}" 
														value="<?php echo $formfield['ffempzip']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "empphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee Home Phone<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="empphone" id="empphone" class="form-control border-dark" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" 
														value="<?php echo $formfield['ffempphone']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "empcellphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Employee Cell Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="empcellphone" id="empcellphone" class="form-control border-dark" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}"
														value="<?php echo $formfield['ffempcellphone']; ?>">
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "emphiredate"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Date of Hire<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="date" name ="emphiredate" id="emphiredate" class="form-control border-dark" value="<?php echo $formfield['ffemphiredate']; ?>" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="empjob" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Job Title<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="empjob" name="empjob" required>
												<option value="">SELECT POSITION</option>
												<?php while ($employeeJob = $employeeJobs->fetch() )
												{
													if ($employeeJob['dbjobID'] == $formfield['ffempjob'])
													{$checker = 'selected';}
													else {$checker = '';}
													echo '<option value="'. $employeeJob['dbjobID'] . '"' . $checker . '>' . 
													$employeeJob['dbjobName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="emplocation" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Location<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="emplocation" name="emplocation" required>
												<option value="">SELECT LOCATION</option>
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
											<div class="col-12 pb-4">
                                                <label for = "emppay" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Pay Rate<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="number" min="0.00" max="50.00" step="0.01" name ="emppay" id="emppay" class="form-control border-dark" required
													 value="<?php echo $formfield['ffemppay']; ?>" pattern = "^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$">
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
	<div class = "container pt-5"></div>
<?php
}#3
else if ($showform == 3) {
echo '<div class = "container ps-5">';
	echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
	echo "You do not have authorization to access this page!" . '</div></div>';}
?>
<?php 
// Indicates no user is logged in so log in form will display
if ($showform == 4) {
//Login Form
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