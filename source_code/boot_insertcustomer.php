<?php
$pagetitle = 'Add Customer';
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

$errormsg = "";
$formtype = 1;

/**These are our sql statements that will be used to pull our data for our list boxes
in our form below. You can see the for loops used to run through the data from our DB.
**/
//For our state drop down menu
$sqlselect_state = "SELECT * from state";
$result_state = $db->prepare($sqlselect_state);
$result_state->execute();

//For our customer plan drop down menu
$sqlselect_plan = "SELECT * from plan";
$result_plan = $db->prepare($sqlselect_plan);
$result_plan->execute();

/**Notice here that if the clear button is pressed, the form type 
that is displayed will become the $formtype of 1. The same applies 
if the new submit button is pressed. This means that the user would
essentially start over and enter a new customer record into the DB.
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
customer entry, given the permission to do so. We will check and see if any of the
required fields are empty, and we will clean the data before being entered.
**/
if (isset($_POST['thesubmit']))	{
    //Cleaning the data
    $formfield['ffcustfirstname'] = trim($_POST['custfirstname']);
    $formfield['ffcustlastname'] = trim($_POST['custlastname']);
    $formfield['ffcustaddress1'] = trim($_POST['custaddress1']);
    $formfield['ffcustaddress2'] = trim($_POST['custaddress2']);
    $formfield['ffcustcity'] = trim($_POST['custcity']);
    $formfield['ffcuststate'] = ($_POST['custstate']);
    $formfield['ffcustzip'] = trim($_POST['custzip']);
    $formfield['ffcustphone'] = trim(strtolower($_POST['custphone']));
	$formfield['ffcustcellphone'] = trim(strtolower($_POST['custcellphone']));
    $formfield['ffcustemail'] = trim(strtolower($_POST['custemail']));
	$formfield['ffcustplan'] = ($_POST['custplan']);

    //Checking to see if the required fields are empty
	if (empty($formfield['ffcustfirstname'])) {
        $errormsg .= "Your First Name is empty! ";
    }
    if (empty($formfield['ffcustlastname'])) {
        $errormsg .= "Your Last Name is empty! ";
    }
    if (empty($formfield['ffcustaddress1'])) {
        $errormsg .= "Your Address is empty! ";
    }
	if (empty($formfield['ffcustcity'])) {
        $errormsg .= "Your City is empty! ";
    }
	if (empty($formfield['ffcuststate'])) {
        $errormsg .= "Your State is empty! ";
    }
	if (empty($formfield['ffcustzip'])) {
        $errormsg .= "Your ZIP Code is empty! ";
    }
	if (empty($formfield['ffcustphone'])) {
        $errormsg .= "Your Phone is empty! ";
    }
/*	if (empty($formfield['ffcustemail'])) {
        $errormsg .= "<p>Your email is empty</p>";
    }*/
	if (empty($formfield['ffcustplan'])) {
        $errormsg .= "Your Plan is empty! ";
    }
	//If the email field is not empty, then we check for valid input
	if (!empty($formfield['ffcustemail'])) {
        //Validating the email
		if (!filter_var($formfield['ffcustemail'], FILTER_VALIDATE_EMAIL)) {
			$errormsg .= "Your Email is not valid! ";
			}
			else {
				 //Looking for duplicate email if it is in valid format
				$checkmail = "SELECT * FROM customer 
				WHERE dbcustomerEmail = '" . $formfield['ffcustemail'] . "' ";
				$result = $db->query($checkmail);
				$count = $result->rowCount();

				if ($count > 0) {
					$errormsg .= "Email already exists. Please enter a new email address! ";
				}
			}//ends else
	}//ends if not empty
    
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
	//want to pull our max value from our customer id field so that we will know what the current
	//customer id will be for our current customer data record being entered into our DB.
		{
			
        //Beginning the try/catch. Begin Try
        try {
			
            //Entering info into the database
            $sqlinsert = 'INSERT INTO customer (dbcustomerLastName, dbcustomerFirstName, dbcustomerEmail, 
				dbcustomerHomePhone, dbcustomerCellPhone, dbcustomerAddress1, dbcustomerAddress2, dbcustomerCity,
				dbcustomerZip, dbstateID, dbplanID)
	                          VALUES (:bvcustlastname, :bvcustfirstname, :bvcustemail, :bvcustphone,
								:bvcustcellphone, :bvcustaddress1, :bvcustaddress2, :bvcustcity,
	                                :bvcustzip, :bvcuststate, :bvcustplan)';
            $stmtinsert = $db->prepare($sqlinsert);
            $stmtinsert->bindvalue(':bvcustfirstname', $formfield['ffcustfirstname']);
            $stmtinsert->bindvalue(':bvcustlastname', $formfield['ffcustlastname']);
            $stmtinsert->bindvalue(':bvcustaddress1', $formfield['ffcustaddress1']);
            $stmtinsert->bindvalue(':bvcustaddress2', $formfield['ffcustaddress2']);
            $stmtinsert->bindvalue(':bvcustcity', $formfield['ffcustcity']);
            $stmtinsert->bindvalue(':bvcuststate', $formfield['ffcuststate']);
            $stmtinsert->bindvalue(':bvcustzip', $formfield['ffcustzip']);
            $stmtinsert->bindvalue(':bvcustphone', $formfield['ffcustphone']);
			$stmtinsert->bindvalue(':bvcustcellphone', $formfield['ffcustcellphone']);
            $stmtinsert->bindvalue(':bvcustemail', $formfield['ffcustemail']);
			$stmtinsert->bindvalue(':bvcustplan', $formfield['ffcustplan']);
            $stmtinsert->execute();
		echo '<div class = "container ps-5">';
		echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
		echo "There are no errors! Form has been submitted." . '</div></div>';
		//	echo '<p><span id = "display_message">There are no errors. Thank you.</span></p>';
		//	echo '<p><span id = "display_message">Form has been submitted.</span></p>';
            //echo "<div class='success'><p>There are no errors!</p></div>";
			//Once the newly entered data has succeeded, we pull the newly entered
			//id value, indicating the newly entered customer record id.
			$sqlmax = "SELECT MAX(dbcustomerID) AS maxid FROM customer";
					$resultmax = $db->prepare($sqlmax);
					$resultmax->execute();
					$rowmax = $resultmax->fetch();
					$maxid = $rowmax["maxid"];
					
			$sqlselect = 'SELECT customer.*, state.*, plan.* from customer, state, plan WHERE
				dbcustomerID = :bvcustid AND customer.dbstateID = state.dbstateID AND 
				customer.dbplanID = plan.dbplanID';
			$result = $db->prepare($sqlselect);
			$result->bindValue(':bvcustid', $maxid);
			$result->execute();
			$row = $result->fetch();
			$formtype = 2;
			/*Notice that once the newly entered record into our DB is successful,
			the new record will display utilizing the SQL statement here. The form
			that is displayed changes once again to equal $formtype 2, which now
			displays the data pulled directly from our DB.*/
		//	echo $row['dbcustomerID'];
		//	echo $row['dbcustomerFirstName'];
			
        }//Try ends
            //Catch begins
        catch (PDOException $e) {
            echo 'ERROR!' . $e->getMessage();
            exit();
        }//Catch ends
    }//If statement ends
} //If isset ends


/**Here we are ensuring that the only users with access to entering a new customer
is one with administrative or managerial permissions, in this case, a GM or AM in FOH.**/
if ((($showform == 1) || ($showform == 5) || ($showform == 6) || ($showform == 7)
		|| ($showform == 8)) && ($formtype == 1))
{
	//We will gather the last entry id in our customer table and add 1 to it in order
	//to obtain the current new customer entry if successfully inserted into our DB.
	$sqlmax = "SELECT MAX(dbcustomerID) AS maxid FROM customer";
					$resultmax = $db->prepare($sqlmax);
					$resultmax->execute();
					$rowmax = $resultmax->fetch();
					$maxid = $rowmax["maxid"]; // Now we have our last item id that was inserted into the item table
					$newid = $maxid + 1;
					/***This is where we output our next id number***/
					/************************************************/
			//		echo $newid; // Our new customer id 
			//		$hi = " this is formtype 1";
			//		echo $hi;
					
?>
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3" style = "font-family: 'Ysabeau SC', sans-serif;">Customer Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "custfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer First Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custfirstname" id="custfirstname" class="form-control" placeholder = "Enter Customer First Name" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "custlastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Last Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custlastname" id="custlastname" class="form-control" placeholder = "Enter Customer Last Name" required>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "custaddress1" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Address<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custaddress1" id="custaddress1" class="form-control" placeholder = "Enter Customer Address" required >
                                                </div>
                                            </div> 
											<div class="col-12">
                                                <label for = "custaddress2" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Address 2</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custaddress2" id="custaddress2" class="form-control" placeholder = "Enter Customer Address 2">
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "custcity" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer City<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custcity" id="custcity" class="form-control" placeholder = "Enter Customer City" required>
                                                </div>
                                            </div>
											<div class="col-sm-9">
												<input type="hidden" name = "custid" value = "<?php echo $newid ?>" />
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
												<label for="custstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer State<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="custstate" name="custstate" required>
												<option value="">SELECT STATE</option>
												<?php while ($row_state = $result_state->fetch() )
												{
												if ($row_state['dbstateID'] == $formfield['ffcuststate'])
													{$checker = 'selected';}
												else {$checker = '';}
													echo '<option value="'. $row_state['dbstateID'] . '"' . $checker . '>' .
													$row_state['dbstateName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-4">
                                                <label for = "custzip"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer ZIP Code<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="custzip" id="custzip" class="form-control border-dark" placeholder = "Enter Five-Digit ZIP Code" pattern="[0-9]{5}" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Home Phone<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="custphone" id="custphone" class="form-control border-dark" placeholder = "1234567890" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custcellphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Cell Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="custcellphone" id="custcellphone" class="form-control border-dark" placeholder = "1234567890" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}">
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custemail" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Email Address</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="email" name ="custemail" id="custemail" class="form-control border-dark" placeholder = "email@site.com" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$">
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custplan" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Plan<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="custplan" name="custplan" required>
												<option value="">SELECT PLAN</option>
												<?php while ($row_plan = $result_plan->fetch() )
												{
													if ($row_plan['dbplanID'] == $formfield['ffcustplan'])
														{$checker = 'selected';}
													else {$checker = '';}
														echo '<option value="'. $row_plan['dbplanID'] . '"' . $checker . '>' .
														$row_plan['dbplanName'] . '</option>';
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
//This is going to be the form that displays when the submit button is entered by
//an administrator or member of management. However, this form will appear only if
//the data has been entered successfully into our DB. Notice the permissions allowed.
else if ((($showform == 1) || ($showform == 5) || ($showform == 6) || ($showform == 7)
		|| ($showform == 8)) && ($formtype == 2))
{ 
$sqlmax = "SELECT MAX(dbcustomerID) AS maxid FROM customer";
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
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3" style = "font-family: 'Ysabeau SC', sans-serif;">Customer Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "custfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer First Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custfirstname" id="custfirstname" class="form-control" value="<?php echo $row['dbcustomerFirstName']; ?>" >
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "custlastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Last Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custlastname" id="custlastname" class="form-control" value="<?php echo $row['dbcustomerLastName']; ?>" >
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "custaddress1" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Address<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custaddress1" id="custaddress1" class="form-control" value="<?php echo $row['dbcustomerAddress1']; ?>" >
                                                </div>
                                            </div> 
											<div class="col-12">
                                                <label for = "custaddress2" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Address 2</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custaddress2" id="custaddress2" class="form-control" value="<?php echo $row['dbcustomerAddress2']; ?>">
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "custcity" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer City<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custcity" id="custcity" class="form-control" value="<?php echo $row['dbcustomerCity']; ?>" >
                                                </div>
                                            </div>
											<div class="col-12">
												<input type="hidden" name = "custid" value = "<?php echo $maxid ?>" />
                                               <button type="submit" name="thenewsubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">NEW ACCOUNT</button>
                                            </div>
									<!--		<div class="col-sm-3">
                                               <button type="reset" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>-->
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer State<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="custstate" name="custstate" >
												<option value="">SELECT STATE</option>
												<?php while ($row_state = $result_state->fetch() )
												{
												if ($row_state['dbstateID'] == $formfield['ffcuststate'])
													{$checker = 'selected';}
												else {$checker = '';}
													echo '<option value="'. $row_state['dbstateID'] . '"' . $checker . '>' .
													$row_state['dbstateName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-4">
                                                <label for = "custzip"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer ZIP Code<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="custzip" id="custzip" class="form-control border-dark" value="<?php echo $row['dbcustomerZip']; ?>" pattern="[0-9]{5}" >
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Home Phone<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="custphone" id="custphone" class="form-control border-dark" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" value="<?php echo $row['dbcustomerHomePhone']; ?>" >
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custcellphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Cell Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="custcellphone" id="custcellphone" class="form-control border-dark" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" value="<?php echo $row['dbcustomerCellPhone']; ?>">
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custemail" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Email Address</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="email" name ="custemail" id="custemail" class="form-control border-dark" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" value = "<?php echo $row['dbcustomerEmail']; ?>">
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custplan" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Plan<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="custplan" name="custplan" >
												<option value="">SELECT PLAN</option>
												<?php while ($row_plan = $result_plan->fetch() )
												{
													if ($row_plan['dbplanID'] == $formfield['ffcustplan'])
														{$checker = 'selected';}
													else {$checker = '';}
														echo '<option value="'. $row_plan['dbplanID'] . '"' . $checker . '>' .
														$row_plan['dbplanName'] . '</option>';
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
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">First Name</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Last Name</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Address</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Address 2</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">City</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">State</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">ZIP Code</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Home Phone</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Cell Phone</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Email</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Plan</th>
	</tr>
</thead>
<tbody class = "border border-dark">
	
<?php
	
			echo '<tr><td>' . $row['dbcustomerFirstName'] . '</td><td>' . $row['dbcustomerLastName'] . '</td><td>' . $row['dbcustomerAddress1'] . 
			'</td><td>' . $row['dbcustomerAddress2'] . '</td><td>' . $row['dbcustomerCity'] . '</td><td>' . $row['dbstateAbrev'] . 
			'</td><td>' . $row['dbcustomerZip'] . '</td><td>' . $row['dbcustomerHomePhone'] . '</td><td>' .  $row['dbcustomerCellPhone'] . 
			'</td><td>' . $row['dbcustomerEmail'] . '</td><td>' . $row['dbplanName'] . '</td></tr>';
?>
</tbody>
</table>
</div>
<?php
#2
//Here, this $formtype 3 will display if there was an error message of some sort,
//so the form is essentially the same as $formtype 1, but the fields are clear, with
//no default placeholders. That way, the user can simply enter in the data they need
//to, filling in any of the required information that was missing.
} 
else if ((($showform == 1) || ($showform == 5) || ($showform == 6) || ($showform == 7)
		|| ($showform == 8)) && ($formtype == 3))
{ 

$sqlmax = "SELECT MAX(dbcustomerID) AS maxid FROM customer";
					$resultmax = $db->prepare($sqlmax);
					$resultmax->execute();
					$rowmax = $resultmax->fetch();
					$maxid = $rowmax["maxid"]; // Now we have our last item id that was inserted into the item table
					$newid = $maxid + 1;
			//		echo $newid; // Our new customer id 
			//		$hello = "Hi this is formtype 3";
			//		echo $hello;
?>
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3" style = "font-family: 'Ysabeau SC', sans-serif;">Customer Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "custfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer First Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custfirstname" id="custfirstname" class="form-control" value="<?php echo $formfield['ffcustfirstname']; ?>" >
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "custlastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Last Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custlastname" id="custlastname" class="form-control" value="<?php echo $formfield['ffcustlastname']; ?>" >
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "custaddress1" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Address<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custaddress1" id="custaddress1" class="form-control" value="<?php echo $formfield['ffcustaddress1']; ?>" >
                                                </div>
                                            </div> 
											<div class="col-12">
                                                <label for = "custaddress2" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Address 2</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custaddress2" id="custaddress2" class="form-control" value="<?php echo $formfield['ffcustaddress2']; ?>">
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "custcity" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer City<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custcity" id="custcity" class="form-control" value="<?php echo $formfield['ffcustcity']; ?>" >
                                                </div>
                                            </div>
											<div class="col-sm-9">
												<input type="hidden" name = "custid" value = "<?php echo $newid ?>" />
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
												<label for="custstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer State<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="custstate" name="custstate" >
												<option value="">SELECT STATE</option>
												<?php while ($row_state = $result_state->fetch() )
												{
												if ($row_state['dbstateID'] == $formfield['ffcuststate'])
													{$checker = 'selected';}
												else {$checker = '';}
													echo '<option value="'. $row_state['dbstateID'] . '"' . $checker . '>' .
													$row_state['dbstateName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-4">
                                                <label for = "custzip"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer ZIP Code<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="custzip" id="custzip" class="form-control border-dark" pattern="[0-9]{5}" value="<?php echo $formfield['ffcustzip']; ?>" >
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Home Phone<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="custphone" id="custphone" class="form-control border-dark" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" value="<?php echo $formfield['ffcustphone']; ?>" >
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custcellphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Cell Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="custcellphone" id="custcellphone" class="form-control border-dark" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" value="<?php echo $formfield['ffcustcellphone']; ?>">
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custemail" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Email Address</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="email" name ="custemail" id="custemail" class="form-control border-dark" value="<?php echo $formfield['ffcustemail']; ?>" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$">
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custplan" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Plan<span class="text-dark">*</span></label>
												<select class="form-select border-dark" id="custplan" name="custplan" >
												<option value="">SELECT PLAN</option>
												<?php while ($row_plan = $result_plan->fetch() )
												{
													if ($row_plan['dbplanID'] == $formfield['ffcustplan'])
														{$checker = 'selected';}
													else {$checker = '';}
														echo '<option value="'. $row_plan['dbplanID'] . '"' . $checker . '>' .
														$row_plan['dbplanName'] . '</option>';
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
}#3
else if (($showform == 2) || ($showform == 3)) {
	echo '<div class = "container ps-5">';
	echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
	echo "You do not have authorization to access this page!" . '</div></div>';
}
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
?>	
<?php
include_once 'boot_footer.php';
?>
