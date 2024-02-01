<?php
$pagetitle = 'Delete Customer';
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

//Setting our variables to empty strings at first
$errormsg = "";
$formfield['ffcustid'] = $_POST['custid'];
$custid = $_SESSION['custid'];
//echo $custid;
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


$sqlselect = 'SELECT customer.*, state.*, plan.* from customer, state, plan WHERE
				dbcustomerID = :bvcustid AND customer.dbstateID = state.dbstateID AND 
				customer.dbplanID = plan.dbplanID';
$result = $db->prepare($sqlselect);
$result->bindValue(':bvcustid', $custid);
//$result->bindValue(':bvcustid', $formfield['ffcustid']);
$result->execute();
$row = $result->fetch(); 
//echo $row[dbcustomerID];

/*Here we are checking if the "delete" button was submitted on the updated
page. Notice the default formtype is set and we are pulling the Customer
id record that was selected by the Administrator. The data associated with 
the specific record is pulled from the DB.*/
	/*	if( isset($_POST['thedelete']) )
		{	
			$formtype = 1;
			$formfield['ffcustid'] = $_POST['custid'];
			$custid = $_SESSION['custid'];
			$sqlselect = 'SELECT customer.*, state.*, plan.* from customer, state, plan WHERE
				dbcustomerID = :bvcustid AND customer.dbstateID = state.dbstateID AND 
				customer.dbplanID = plan.dbplanID';
			$result = $db->prepare($sqlselect);
			$result->bindValue(':bvcustid', $custid);
			//$result->bindValue(':bvcustid', $formfield['ffcustid']);
			$result->execute();
			$row = $result->fetch(); 
		} //ends if isset
		*/
//This executes when the "submit" button is pressed to actually delete
//a record. Notice the formtype changes and the session variable is reset.
if( isset($_POST['thesubmit']) )
		{
			$formtype = 2;
			$formfield['ffcustid'] = $_POST['custid'];
			$custid = $_SESSION['custid'];
			
			//We run the select query again, pulling the specific record
			$sqlselect = 'SELECT customer.*, state.*, plan.* from customer, state, plan WHERE
				dbcustomerID = :bvcustid AND customer.dbstateID = state.dbstateID AND 
				customer.dbplanID = plan.dbplanID';
			$result = $db->prepare($sqlselect);
			$result->bindValue(':bvcustid', $custid);
			//$result->bindValue(':bvcustid', $formfield['ffcustid']);
			$result->execute();
			$row = $result->fetch(); 
			
			//We cleanse our data and execute data validation as well
			//Data Cleansing
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
			
			/*  ****************************************************************************
     		CHECK FOR EMPTY FIELDS
    		Complete the lines below for any REQUIRED form fields only.
			Do not do for optional fields.
    		**************************************************************************** */
		/*	if(empty($formfield['ffcustfirstname'])){$errormsg .= "Your First Name is empty! ";}
			if(empty($formfield['ffcustlastname'])){$errormsg .= "Your Last Name is empty! ";}
			if(empty($formfield['ffcustaddress1'])){$errormsg .= "Your Address is empty! ";}
			if(empty($formfield['ffcustcity'])){$errormsg .= "Your City is empty! ";}
			if(empty($formfield['ffcuststate'])){$errormsg .= "Your State is empty! ";}
			if(empty($formfield['ffcustzip'])){$errormsg .= "Your ZIP Code is empty! ";}
			if(empty($formfield['ffcustphone'])){$errormsg .= "Your Phone is empty! ";}
			if(empty($formfield['ffcustplan'])){$errormsg .= "Your Plan is empty! ";}*/
			
		//If all fields are empty, an error message will display	
		if ((empty($formfield['ffcustfirstname'])) && (empty($formfield['ffcustlastname'])) && 
			(empty($formfield['ffcustaddress1'])) && (empty($formfield['ffcustaddress2'])) &&
			(empty($formfield['ffcustcity'])) && (empty($formfield['ffcuststate'])) &&
			(empty($formfield['ffcustzip'])) && (empty($formfield['ffcustphone'])) && 
			(empty($formfield['ffcustcellphone'])) && (empty($formfield['ffcustemail'])) &&
			(empty($formfield['ffcustplan']))) {
					
				$errormsg .= "ERROR!! All fields cannot be empty! ";
				}
	
	
			/*******************************************************************************
			DISPLAY ERRORS
			If we have concatenated the error message with details, then let the user know
			**************************************************************************** */
			if($errormsg != "")
			{
			echo '<div class = "container ps-5">';
			echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
			echo "There are errors! " . $errormsg . '</div></div>';
				$formtype = 2;
				//We run the select query again, pulling the specific record
			$sqlselect = 'SELECT customer.*, state.*, plan.* from customer, state, plan WHERE
				dbcustomerID = :bvcustid AND customer.dbstateID = state.dbstateID AND 
				customer.dbplanID = plan.dbplanID';
			$result = $db->prepare($sqlselect);
			$result->bindValue(':bvcustid', $custid);
			//$result->bindValue(':bvcustid', $formfield['ffcustid']);
			$result->execute();
			$row = $result->fetch(); 
				
			}
			else
			{
			
				try
				{
				//Delete all data associated with current record
				$sqldelete = 'DELETE customer.* FROM customer, state, plan WHERE
					dbcustomerID = :bvcustid AND customer.dbstateID = state.dbstateID AND 
					customer.dbplanID = plan.dbplanID';
				$result = $db->prepare($sqldelete);
			    $result->bindValue(':bvcustid', $formfield['ffcustid']);
				$result->bindValue(':bvcustid', $custid);
			    $result->execute();
			    $row = $result->fetch(); 
				
				echo '<div class = "container ps-5">';
				echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
				echo "Record has been deleted. " . '</div></div>';
					
				//	echo '<p><span id = "display_message">Record has been deleted.</span></p>';

					
				/*Notice here I pull the data from the database once the data has been successfully
				updated, thus correctly showing the newly edited information in the form and table below.*/
				$sqlselect = 'SELECT customer.*, state.*, plan.* from customer, state, plan WHERE
					dbcustomerID = :bvcustid AND customer.dbstateID = state.dbstateID AND 
					customer.dbplanID = plan.dbplanID';
				$result = $db->prepare($sqlselect);
				$result->bindValue(':bvcustid', $custid);
				$result->execute();
				$row = $result->fetch(); 
				$formtype = 3;



				}//try
				catch(PDOException $e)
				{
					echo 'ERROR!!!' .$e->getMessage();
					exit();
				}
			}
		}//if isset submit
		
/*If the user logged in is an Administrator, this will be the initial form that will be displayed.*/
if (($showform == 5) && ($formtype == 1))
{
?>
<script>
  function cancelDelete() {
    alert("Deletion request canceled.")
		window.location = "boot_selectcustomer.php";
    
}
</script>
<!--default form to display when Admin requests to delete customer record--->
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
                                                <label for = "custfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer First Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custfirstname" id="custfirstname" class="form-control" value = "<?php echo $row['dbcustomerFirstName']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "custlastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Last Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custlastname" id="custlastname" class="form-control" value = "<?php echo $row['dbcustomerLastName']; ?>" readonly>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "custaddress1" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Address</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custaddress1" id="custaddress1" class="form-control" value = "<?php echo $row['dbcustomerAddress1']; ?>" readonly>
                                                </div>
                                            </div> 
											<div class="col-12">
                                                <label for = "custaddress2" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Address 2</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custaddress2" id="custaddress2" class="form-control" value = "<?php echo $row['dbcustomerAddress2']; ?>" readonly>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "custcity" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer City</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custcity" id="custcity" class="form-control" value = "<?php echo $row['dbcustomerCity']; ?>" readonly>
                                                </div>
                                            </div>
											<div class="col-sm-9">
												<input type="hidden" name = "custid" value="<?php echo $custid ?>" />
                                               <button type="submit" name="thecancel" onClick="cancelDelete(); return false;" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CANCEL</button>
                                            </div>
											<div class="col-sm-3">
												<input type="hidden" name = "custid" value="<?php echo $custid ?>" />
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">DELETE</button>
											</div>
                                </div>
                            </div>
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer State</label>
												<select class="form-select" id="custstate" name="custstate" disabled>
												<option value="">SELECT STATE</option>
												<?php while ($row_state = $result_state->fetch() )
												{
												if ($row_state['dbstateID'] == $row['dbstateID'])
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
                                                <label for = "custzip"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer ZIP Code</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="custzip" id="custzip" class="form-control border-dark" value = "<?php echo $row['dbcustomerZip']; ?>" pattern="[0-9]{5}" readonly>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Home Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="custphone" id="custphone" class="form-control border-dark" value = "<?php echo $row['dbcustomerHomePhone']; ?>" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" readonly>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custcellphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Cell Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="custcellphone" id="custcellphone" class="form-control border-dark" value = "<?php echo $row['dbcustomerCellPhone']; ?>" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" readonly>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custemail" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Email Address</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="email" name ="custemail" id="custemail" class="form-control border-dark" value = "<?php echo $row['dbcustomerEmail']; ?>" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" readonly>
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custplan" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Plan</label>
												<select class="form-select" id="custplan" name="custplan" disabled>
												<option value="">SELECT PLAN</option>
												<?php while ($row_plan = $result_plan->fetch() )
												{
												if ($row_plan['dbplanID'] == $row['dbplanID'])
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
<!--table displaying results--->
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
}#1-1
/*Form will display if some sort of error message has been displayed*/
else if (($showform == 5) && ($formtype == 2))
{
?>
<script>
  function cancelDelete() {
    alert("Deletion request canceled.")
		window.location = "boot_selectcustomer.php";
    
}
  function pageReturn() {
		window.location = "boot_selectcustomer.php";
}
</script>
<!--Form displayed if error occurs-->
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
                                                <label for = "custfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer First Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custfirstname" id="custfirstname" class="form-control" value = "<?php echo $row['dbcustomerFirstName']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "custlastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Last Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custlastname" id="custlastname" class="form-control" value = "<?php echo $row['dbcustomerLastName']; ?>" readonly>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "custaddress1" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Address</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custaddress1" id="custaddress1" class="form-control" value = "<?php echo $row['dbcustomerAddress1']; ?>" readonly >
                                                </div>
                                            </div> 
											<div class="col-12">
                                                <label for = "custaddress2" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Address 2</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custaddress2" id="custaddress2" class="form-control" value = "<?php echo $row['dbcustomerAddress2']; ?>" readonly>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "custcity" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer City</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custcity" id="custcity" class="form-control" value = "<?php echo $row['dbcustomerCity']; ?>" readonly>
                                                </div>
                                            </div>
											<div class="col-sm-9">
												<input type="hidden" name = "custid" value="<?php echo $custid ?>" />
                                               <button type="submit" name="thecancel" onClick="cancelDelete(); return false;" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CANCEL</button>
                                            </div>
											<div class="col-sm-3">
												<input type="hidden" name = "custid" value="<?php echo $custid ?>" />
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">DELETE</button>
											</div>
                                </div>
                            </div>
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer State</label>
												<select class="form-select" id="custstate" name="custstate" disabled>
												<option value="">SELECT STATE</option>
												<?php while ($row_state = $result_state->fetch() )
												{
												if ($row_state['dbstateID'] == $row['dbstateID'])
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
                                                <label for = "custzip"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer ZIP Code</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="custzip" id="custzip" class="form-control border-dark" value = "<?php echo $row['dbcustomerZip']; ?>" pattern="[0-9]{5}" readonly>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Home Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="custphone" id="custphone" class="form-control border-dark" value = "<?php echo $row['dbcustomerHomePhone']; ?>" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" readonly>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custcellphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Cell Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="custcellphone" id="custcellphone" class="form-control border-dark" value = "<?php echo $row['dbcustomerCellPhone']; ?>" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" readonly>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custemail" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Email Address</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="email" name ="custemail" id="custemail" class="form-control border-dark" value = "<?php echo $row['dbcustomerEmail']; ?>" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" readonly>
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custplan" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Plan</label>
												<select class="form-select" id="custplan" name="custplan" disabled>
												<option value="">SELECT PLAN</option>
												<?php while ($row_plan = $result_plan->fetch() )
												{
												if ($row_plan['dbplanID'] == $row['dbplanID'])
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
<!--table displaying results--->
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
}#1-2
/*Form will display when data has been successfully deleted from the DB**/
else if (($showform == 5) && ($formtype == 3))
{
?>
<script>
  function cancelDelete() {
    alert("Deletion request canceled.")
		window.location = "boot_selectcustomer.php";
    
}
  function pageReturn() {
		window.location = "boot_selectcustomer.php";
}
</script>
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
                                                <label for = "custfirstname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer First Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custfirstname" id="custfirstname" class="form-control" value = "<?php echo $row['dbcustomerFirstName']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "custlastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Last Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custlastname" id="custlastname" class="form-control" value = "<?php echo $row['dbcustomerLastName']; ?>" readonly>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "custaddress1" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Address</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custaddress1" id="custaddress1" class="form-control" value = "<?php echo $row['dbcustomerAddress1']; ?>" readonly >
                                                </div>
                                            </div> 
											<div class="col-12">
                                                <label for = "custaddress2" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Address 2</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custaddress2" id="custaddress2" class="form-control" value = "<?php echo $row['dbcustomerAddress2']; ?>" readonly>
                                                </div>
                                            </div>
											<div class="col-12">
                                                <label for = "custcity" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer City</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custcity" id="custcity" class="form-control" value = "<?php echo $row['dbcustomerCity']; ?>" readonly>
                                                </div>
                                            </div>
											<div class="col-12">
												<input type="hidden" name = "custid" value="<?php echo $custid ?>" />
                                               <button type="submit" name="thecancel" onClick="pageReturn(); return false;" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">RETURN</button>
                                            </div>
										<!--	<div class="col-sm-3">
												<input type="hidden" name = "custid" value="<?php echo $custid ?>" />
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">DELETE</button>
											</div>-->
                                </div>
                            </div>
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer State</label>
												<select class="form-select" id="custstate" name="custstate" disabled>
												<option value="">SELECT STATE</option>
												<?php while ($row_state = $result_state->fetch() )
												{
												if ($row_state['dbstateID'] == $row['dbstateID'])
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
                                                <label for = "custzip"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer ZIP Code</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="custzip" id="custzip" class="form-control border-dark" value = "<?php echo $row['dbcustomerZip']; ?>" pattern="[0-9]{5}" readonly>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Home Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="custphone" id="custphone" class="form-control border-dark" value = "<?php echo $row['dbcustomerHomePhone']; ?>" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" readonly>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custcellphone" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Cell Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="tel" name ="custcellphone" id="custcellphone" class="form-control border-dark" value = "<?php echo $row['dbcustomerCellPhone']; ?>" pattern = "[0-9]{3}[0-9]{3}[0-9]{4}" readonly>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "custemail" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Email Address</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="email" name ="custemail" id="custemail" class="form-control border-dark" value = "<?php echo $row['dbcustomerEmail']; ?>" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" readonly>
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custplan" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Plan</label>
												<select class="form-select" id="custplan" name="custplan" disabled>
												<option value="">SELECT PLAN</option>
												<?php while ($row_plan = $result_plan->fetch() )
												{
												if ($row_plan['dbplanID'] == $row['dbplanID'])
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
<!--table displaying results--->
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
}#1-3




else if (($showform == 1) || ($showform == 2) || ($showform == 3)
		|| ($showform == 6) || ($showform == 7) || ($showform == 8)) {
echo '<div class = "container ps-5">';
echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
echo "You do not have authorization to access this page!" . '</div></div>';}

// Indicates no user is logged in so log in form will display
else if ($showform == 4) {
?>
<!----Login Form-------->
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