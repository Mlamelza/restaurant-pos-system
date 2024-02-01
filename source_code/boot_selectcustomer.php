<?php
$pagetitle = 'Select Customer';
require_once 'boot_header.php';
//Connecting to the database
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
	
	// This indicates a permission level of our waitstaff, bar staff and FOH hourly staff members, setting $showform to equal 3
	else if (($_SESSION['emppermitid'] == 4) || ($_SESSION['emppermitid'] == 5) || 
	($_SESSION['emppermitid'] == 6) || ($_SESSION['emppermitid'] == 10)
		|| ($_SESSION['emppermitid'] == 11)) {
		$showform = 3;
	}
	// This indicates a permission level of our kitchen hourly staff and support staff, setting our $showform variable to equal 9
	else if (($_SESSION['emppermitid'] == 3) || ($_SESSION['emppermitid'] == 8)) {
		$showform = 9;
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

//Important variables
//We set our error message variable to an empty string and we set our default
//$formtype variable to equal 1, which will display to managerial staff members
$stringclause = '';
$message = '';
$errormsg = "";
$formtype  = 1;
//These statements will pull data for our drop down form boxes
$sqlselect_state = "SELECT * from state";
$result_state = $db->prepare($sqlselect_state);
$result_state->execute();

$sqlselect_plan = "SELECT * from plan";
$result_plan = $db->prepare($sqlselect_plan);
$result_plan->execute();

/**Notice here that if the clear button is pressed, the form type 
that is displayed will become the $formtype of 1. The same applies 
if the new submit button is pressed. This means that the user would
essentially start over and select a new customer record from the DB.
This clears the input boxes and drop downs so the user can start over
and search for a new record in our DB table.
**/
if (isset($_POST['theclear']))	{
	$formtype = 1; 
}

if (isset($_POST['thenewsubmit']))	{
	$formtype = 1; 
}
/*Here is where we check to see if the submit button is pressed by the user. If so, 
we cleanse our formfield data and attempt to pull the appropriate record from the DB.
*/
	
	if( isset($_POST['thesubmit']) )
	{
		//Cleaning the data
		
		$formfield['ffcustfirstname'] = trim($_POST['custfirstname']);
		$formfield['ffcustlastname'] = trim($_POST['custlastname']);
	//	$formfield['ffcustaddress1'] = trim($_POST['custaddress1']);
	//	$formfield['ffcustaddress2'] = trim($_POST['custaddress2']);
		$formfield['ffcustcity'] = trim($_POST['custcity']);
		$formfield['ffcuststate'] = ($_POST['custstate']);
	//	$formfield['ffcustzip'] = trim($_POST['custzip']);
	//	$formfield['ffcustphone'] = trim(strtolower($_POST['custphone']));
	//	$formfield['ffcustcellphone'] = trim(strtolower($_POST['custcellphone']));
	//	$formfield['ffcustemail'] = trim(strtolower($_POST['custemail']));
		$formfield['ffcustplan'] = ($_POST['custplan']);
		
		//Here we add a search element if the user does search via the state option
		if ($formfield['ffcuststate'] != '') {
			$stringclause .= " AND customer.dbstateID = :bvcuststate";
		}
		//Here we add a search element if the user does search via the plan option
		if ($formfield['ffcustcity'] != '') {
			$stringclause .= " AND customer.dbcustomerCity like CONCAT('%', :bvcustcity, '%')";
		}

		//Here we add a search element if the user does search via the plan option
		if ($formfield['ffcustplan'] != '') {
			$stringclause .= " AND customer.dbplanID = :bvcustplan";
		}
		
		//Here we check and see if the submit button was pressed with ALL empty fields
	/*	if ((empty($formfield['ffcustfirstname'])) && (empty($formfield['ffcustlastname'])) && (empty($formfield['ffcustaddress1']))
				&& (empty($formfield['ffcustaddress2'])) && (empty($formfield['ffcustcity'])) && (empty($formfield['ffcuststate']))
				&& (empty($formfield['ffcustzip'])) && (empty($formfield['ffcustphone'])) && (empty($formfield['ffcustcellphone']))
				&& (empty($formfield['ffcustemail'])) && (empty($formfield['ffcustplan']))) {
					
			
				$errormsg .= '<p><span id= "display_message">You must choose at least one search method!</span></p>';	
				}*/
		if ((empty($formfield['ffcustfirstname'])) && (empty($formfield['ffcustlastname'])) && 
			(empty($formfield['ffcustcity'])) && (empty($formfield['ffcuststate'])) && 
			(empty($formfield['ffcustplan']))) {
					
				$errormsg .= "You must choose at least one search method! ";
				}
		/***************************************************************************************************************/
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
		//Selecting from the appropriate tables. In this instance the customers table.
		$sqlselect = "SELECT customer.*, state.*, plan.*
					  FROM customer, state, plan
					  WHERE customer.dbstateID = state.dbstateID
					  AND customer.dbplanID = plan.dbplanID
					  AND dbcustomerFirstName like CONCAT('%', :bvcustfirstname, '%')
					  AND dbcustomerLastName like CONCAT('%', :bvcustlastname, '%')"
				//	  AND dbcustomerCity like CONCAT('%', :bvcustcity, '%')
				//	  AND customer.dbplanID like CONCAT('%', :bvcustplan, '%')
					  . $stringclause;

					  
					  
		$result = $db->prepare($sqlselect);
		
		$result->bindvalue(':bvcustfirstname', $formfield['ffcustfirstname']);
		$result->bindvalue(':bvcustlastname', $formfield['ffcustlastname']);
	//	$result->bindvalue(':bvcustcity', $formfield['ffcustcity']);
	//	$result->bindvalue(':bvcustplan', $formfield['ffcustplan']);
	
		if ($formfield['ffcuststate'] != '') {
		$result->bindvalue(':bvcuststate', $formfield['ffcuststate']);}
		
		if ($formfield['ffcustcity'] != '') {
		$result->bindvalue(':bvcustcity', $formfield['ffcustcity']);}
		
		if ($formfield['ffcustplan'] != '') {
		$result->bindvalue(':bvcustplan', $formfield['ffcustplan']);}
		
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
		//	echo '<p><span id = "display_message">No such record exists!</span></p>';
		//	$formtype = 3;
			$formtype = 2;

		} else if ($count >=1)
		{
			echo '<div class = "container ps-5">';
			echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
			echo "Results found." . '</div></div>';
		//	echo '<p><span id = "display_message">Results found.</span></p>';
			$formtype = 3;
		}
	}
} //If isset submit ends if the user clicks the submit button for the form
/*	else
	{
		/*$sqlselect = "SELECT customer .*, state.*, plan.* FROM 
			customer, state, plan WHERE customer.dbstateID = state.dbstateID
			AND customer.dbplanID = plan.dbplanID";
		$result = $db-> query($sqlselect);			*/
/*$formtype = 1;		
	}//Else ends*/
	
	// Administrative, managerial, and other permissible users
if ((($showform == 1) || ($showform == 5) || ($showform == 6) || ($showform == 7)
	|| ($showform == 8) || ($showform == 2) || ($showform == 3)) && ($formtype == 1)){
?>
<!--------This is the default form that shows up with permissible users---------------------------------------->
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
                                                    <input type="text" name ="custfirstname" id="custfirstname" class="form-control" value="<?php echo $formfield['ffcustfirstname']; ?>" 
														placeholder = "Search Customer First Name">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "custlastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Last Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custlastname" id="custlastname" class="form-control" value="<?php echo $formfield['ffcustlastname']; ?>"
														placeholder = "Search Customer Last Name">
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
                                                <label for = "custcity"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer City</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="custcity" id="custcity" class="form-control border-dark" value="<?php echo $formfield['ffcustcity']; ?>"
														placeholder = "Search Customer City">
                                                </div>
                                            </div>
										   <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer State</label>
												<select class="form-select border-dark" id="custstate" name="custstate">
												<option value="">SELECT STATE</option>
												<?php while ($row_state = $result_state->fetch() )
				{
					if ($row_state['dbstateID'] == $formfield['ffcuststate'])
					{$checker = 'selected';}
				else{$checker = '';}
				echo '<option value="'. $row_state['dbstateID'] . '"' . $checker . '>' .
					$row_state['dbstateName'] . '</option>';
				}
				
				?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custplan" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Plan</label>
												<select class="form-select border-dark" id="custplan" name="custplan">
												<option value="">SELECT PLAN</option>
												<?php while ($row_plan = $result_plan->fetch() )
				{
					if ($row_plan['dbplanID'] == $formfield['ffcustplan'])
					{$checker = 'selected';}
				else{$checker = '';}
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
//This form will display if no record is found for permissible users
else if ((($showform == 1) || ($showform == 5) || ($showform == 6) || ($showform == 7)
	|| ($showform == 8) || ($showform == 2) || ($showform == 3)) && ($formtype == 2)){
?>
<!--------No records found---------------------------------------->
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
                                                    <input type="text" name ="custfirstname" id="custfirstname" class="form-control" 
													value="<?php echo $formfield['ffcustfirstname']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "custlastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Last Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custlastname" id="custlastname" class="form-control" 
													value="<?php echo $formfield['ffcustlastname']; ?>">
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
                                                <label for = "custcity"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer City</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="custcity" id="custcity" class="form-control border-dark" 
													value="<?php echo $formfield['ffcustcity']; ?>">
                                                </div>
                                            </div>
										   <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer State</label>
												<select class="form-select border-dark" id="custstate" name="custstate">
												<option value="">SELECT STATE</option>
												<?php while ($row_state = $result_state->fetch() )
												{
													if ($row_state['dbstateID'] == $formfield['ffcuststate'])
													{$checker = 'selected';}
													else{$checker = '';}
													echo '<option value="'. $row_state['dbstateID'] . '"' . $checker . '>' .
													$row_state['dbstateName'] . '</option>';
												}
				
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custplan" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Plan</label>
												<select class="form-select border-dark" id="custplan" name="custplan">
												<option value="">SELECT PLAN</option>
												<?php while ($row_plan = $result_plan->fetch() )
												{
												if ($row_plan['dbplanID'] == $formfield['ffcustplan'])
												{$checker = 'selected';}
												else{$checker = '';}
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
}#2
//This form will display for permissible users when a record is found
else if ((($showform == 1) || ($showform == 5) || ($showform == 6) || ($showform == 7)
	|| ($showform == 8)) && ($formtype == 3)){
?>
<!--------Records found---------------------------------------->
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
                                                    <input type="text" name ="custfirstname" id="custfirstname" class="form-control" 
													value="<?php echo $formfield['ffcustfirstname']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "custlastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Last Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custlastname" id="custlastname" class="form-control" 
													value="<?php echo $formfield['ffcustlastname']; ?>">
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
                                                <label for = "custcity"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer City</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="custcity" id="custcity" class="form-control border-dark" 
													value="<?php echo $formfield['ffcustcity']; ?>">
                                                </div>
                                            </div>
										   <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer State</label>
												<select class="form-select border-dark" id="custstate" name="custstate">
												<option value="">SELECT STATE</option>
												<?php while ($row_state = $result_state->fetch() )
												{
													if ($row_state['dbstateID'] == $formfield['ffcuststate'])
													{$checker = 'selected';}
													else{$checker = '';}
													echo '<option value="'. $row_state['dbstateID'] . '"' . $checker . '>' .
													$row_state['dbstateName'] . '</option>';
												}
				
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custplan" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Plan</label>
												<select class="form-select border-dark" id="custplan" name="custplan">
												<option value="">SELECT PLAN</option>
												<?php while ($row_plan = $result_plan->fetch() )
												{
												if ($row_plan['dbplanID'] == $formfield['ffcustplan'])
												{$checker = 'selected';}
												else{$checker = '';}
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
<!---table displaying results--->
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
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">&nbsp;</th>
	</tr>
</thead>
<tbody class = "border border-dark">
<?php
	while ( $row = $result-> fetch() )
		{											
			echo '<tr><td>' . $row['dbcustomerFirstName'] . '</td><td>' . $row['dbcustomerLastName'] . '</td><td>' . $row['dbcustomerAddress1'] . 
			'</td><td>' . $row['dbcustomerAddress2'] . '</td><td>' . $row['dbcustomerCity'] . '</td><td>' . $row['dbstateAbrev'] . 
			'</td><td>' . $row['dbcustomerZip'] . '</td><td>' . $row['dbcustomerHomePhone'] . '</td><td>' .  $row['dbcustomerCellPhone'] . 
			'</td><td>' . $row['dbcustomerEmail'] . '</td><td>' . $row['dbplanName'] . '</td><td>' . 
			//This will make a button that sends the user to another page "updatecustomer.php"	  
			'<form action = "boot_updatecustomer.php" method = "post">
				<input type = "hidden" name = "custid" value = "' . $row['dbcustomerID'] . '">
				<input type = "submit" class = "rounded-3 bg-dark text-light" name = "theedit" value = "EDIT">
			</form>' . '</td></tr>';	  
		}	
?>
</tbody>
</table>
</div>
<?php 
}#1-3
//This form will display for users that do not have permission to 
//edit customers when at least one record is found.
else if ((($showform == 2) || ($showform == 3)) && ($formtype == 3)){
?>
<!--------Records found---------------------------------------->
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
                                                    <input type="text" name ="custfirstname" id="custfirstname" class="form-control" 
													value="<?php echo $formfield['ffcustfirstname']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for = "custlastname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Customer Last Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="custlastname" id="custlastname" class="form-control" 
													value="<?php echo $formfield['ffcustlastname']; ?>">
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
                                                <label for = "custcity"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer City</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="custcity" id="custcity" class="form-control border-dark" 
													value="<?php echo $formfield['ffcustcity']; ?>">
                                                </div>
                                            </div>
										   <div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custstate" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer State</label>
												<select class="form-select border-dark" id="custstate" name="custstate">
												<option value="">SELECT STATE</option>
												<?php while ($row_state = $result_state->fetch() )
												{
													if ($row_state['dbstateID'] == $formfield['ffcuststate'])
													{$checker = 'selected';}
													else{$checker = '';}
													echo '<option value="'. $row_state['dbstateID'] . '"' . $checker . '>' .
													$row_state['dbstateName'] . '</option>';
												}
				
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="custplan" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Customer Plan</label>
												<select class="form-select border-dark" id="custplan" name="custplan">
												<option value="">SELECT PLAN</option>
												<?php while ($row_plan = $result_plan->fetch() )
												{
												if ($row_plan['dbplanID'] == $formfield['ffcustplan'])
												{$checker = 'selected';}
												else{$checker = '';}
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
<!---table displaying results--->
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
	while ( $row = $result-> fetch() )
		{											
			echo '<tr><td>' . $row['dbcustomerFirstName'] . '</td><td>' . $row['dbcustomerLastName'] . '</td><td>' . $row['dbcustomerAddress1'] . 
			'</td><td>' . $row['dbcustomerAddress2'] . '</td><td>' . $row['dbcustomerCity'] . '</td><td>' . $row['dbstateAbrev'] . 
			'</td><td>' . $row['dbcustomerZip'] . '</td><td>' . $row['dbcustomerHomePhone'] . '</td><td>' .  $row['dbcustomerCellPhone'] . 
			'</td><td>' . $row['dbcustomerEmail'] . '</td><td>' . $row['dbplanName'] . '</td></tr>';	  
		}	
?>
</tbody>
</table>
</div>
<?php 
}#2-3
else if ($showform == 9) {
	echo '<div class = "container ps-5">';
	echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
	echo "You do not have authorization to access this page!" . '</div></div>';
}
else if ($showform == 4) {
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
?>

<?php
include 'boot_footer.php';
?>