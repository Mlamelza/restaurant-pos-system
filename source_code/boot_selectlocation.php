<?php
$pagetitle = 'Select Restaurant Location';
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

//For our location drop down menu
$sqlselectloc = "SELECT * from location ORDER by dblocationName";
$resultloc = $db->prepare($sqlselectloc);
$resultloc->execute();

/**Notice here that if the clear button is pressed, the form type 
that is displayed will become the $formtype of 1. The same applies 
if the new submit button is pressed. This means that the user would
essentially start over and select a location record from the DB.
**/
if (isset($_POST['theclear']))	{
	$formtype = 1; 
}

/*Here is where we check to see if the submit button is pressed by the user. If so, 
we cleanse our formfield data and attempt to pull the appropriate record from the DB.
*/		
if( isset($_POST['thesubmit']) )
	{
		//Data Cleansing
		$formfield['fflocationname'] = trim($_POST['locationname']);
		/*****************************************************************************
     	CHECK FOR EMPTY FIELDS
    	Complete the lines below for any REQIURED form fields only.
		Do not do for optional fields.
		****************************************************************************/
		if(empty($formfield['fflocationname']))
		{
			$errormsg .= "Your Location is empty! ";
			}	
		//Notice that if there is an error message, it will simply be because no search criteria
		//was used, taking the user to the very beginning, assigning the formtype to equal 1.
		if ($errormsg != "") {
			echo '<div class = "container ps-5">';
			echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
			echo "There are errors! " . $errormsg . '</div></div>';
			//This is essentially starting over since the form has nothing entered to find
			$formtype = 1;		
		} else
		
	{
		//select clause for restaurant locations
		$sqlselect = 'SELECT * FROM location WHERE dblocationID
			= :bvlocationid';
		$result = $db->prepare($sqlselect);
		$result->bindValue(':bvlocationid', $formfield['fflocationname']);
		$result->execute();
		
	/*Now here, if the select clause is successful, but finds no records at all
	matching the search criteria, then we display the error message and reset the
	formtype to 2, which will simply hold the values entered by the user.*/
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
	}//ends else
		
}//ends if isset
/***************************************************************************************/
/*The following default formtype 1 is displayed for Administrative users*/
if (($showform == 5) && ($formtype == 1))
{	
?>
<!--default formtype 1---->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Restaurant Location Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12 pb-2">
											 <div class="form-group">
												<label for="locationname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Location Name</label>
												<select class="form-select" id="locationname" name="locationname">
												<option value="">SELECT LOCATION</option>
												<?php while ($rowloc = $resultloc->fetch() )
												{
												if ($rowloc['dblocationID'] == $formfield['fflocationname'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowloc['dblocationID'] . '"' . $checker . '>' . 
												$rowloc['dblocationName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											
											
											<div class="col-sm-9 pt-2">
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3 pt-2">
                                               <button type="submit" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
									</form>
                                </div>
                            </div>
							
                           <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-2">
                                    			<img id="rloclogo" alt="Location Logo" class = "pt-2" style="height: 240px; vertical-align: middle;" src="./images/restloclogo3.svg">

								 </div>
                            </div>
                        </div>
                    </div> 
               </div>
          </div>
     </div>
</div>	
<?php 
}#1-1
/***************************************************************************************/
/*Here, the form that will display indicates the fact that the submit button was pressed 
by the user and the formfields will hold the values entered by the current user logged in.*/
else if (($showform == 5) && ($formtype == 2))
{	
?>
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Restaurant Location Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12 pb-2">
											 <div class="form-group">
												<label for="locationname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Location Name</label>
												<select class="form-select" id="locationname" name="locationname">
												<option value="">SELECT LOCATION</option>
												<?php while ($rowloc = $resultloc->fetch() )
												{
												if ($rowloc['dblocationID'] == $formfield['fflocationname'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowloc['dblocationID'] . '"' . $checker . '>' . 
												$rowloc['dblocationName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											
											
											<div class="col-sm-9 pt-2">
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3 pt-2">
                                               <button type="submit" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
									</form>
                                </div>
                            </div>
							
                           <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-2">
                                    			<img id="rloclogo" alt="Location Logo" class = "pt-2" style="height: 240px; vertical-align: middle;" src="./images/restloclogo3.svg">

								 </div>
                            </div>
                        </div>
                    </div> 
               </div>
          </div>
     </div>
</div>	
<?php
}#1-2
//This form will display once a record is found
else if (($showform == 5) && ($formtype == 3))
{	
?>
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Restaurant Location Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12 pb-2">
											 <div class="form-group">
												<label for="locationname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Location Name</label>
												<select class="form-select" id="locationname" name="locationname">
												<option value="">SELECT LOCATION</option>
												<?php while ($rowloc = $resultloc->fetch() )
												{
												if ($rowloc['dblocationID'] == $formfield['fflocationname'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowloc['dblocationID'] . '"' . $checker . '>' . 
												$rowloc['dblocationName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											
											
											<div class="col-sm-9 pt-2">
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3 pt-2">
                                               <button type="submit" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
									</form>
                                </div>
                            </div>
							
                           <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-2">
                                    			<img id="rloclogo" alt="Location Logo" class = "pt-2" style="height: 240px; vertical-align: middle;" src="./images/restloclogo3.svg">

								 </div>
                            </div>
                        </div>
                    </div> 
               </div>
          </div>
     </div>
</div>
<div class = "container">
<table class = "table table-striped table-bordered w-25 p-3">
<thead class="thead-dark border border-dark bg-dark text-light">
	<tr>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Restaurant Location</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">&nbsp;</th>
	</tr>
</thead>
<tbody class = "border border-dark">	
	<?php 
	// We display our table data based on our SQL query above
	while ($row = $result-> fetch() )
		{
				echo '<tr><td>' . $row['dblocationName'] . '</td><td>' .
				//This will make a button that sends the user to another page "updatecategory.php"
				'<form action="boot_updatelocation.php" method="post">
					<input type="hidden" name="locid" value="'. $row['dblocationID'] .'">
					<input type="submit" class = "rounded-3 bg-dark text-light" name="theedit" value="EDIT">
				</form>' . '</td></tr>';
				
			}
		?>
</tbody>
</table>
</div>	
<?php
}#1-3
/*The following default formtype 1 is displayed for remaining permissible users*/
else if ((($showform == 1) || ($showform == 2) || ($showform == 3) ||
		($showform == 6) || ($showform == 8) || ($showform == 7)) && ($formtype == 1))
{	
?>
<!--default formtype 1---->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Restaurant Location Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12 pb-2">
											 <div class="form-group">
												<label for="locationname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Location Name</label>
												<select class="form-select" id="locationname" name="locationname">
												<option value="">SELECT LOCATION</option>
												<?php while ($rowloc = $resultloc->fetch() )
												{
												if ($rowloc['dblocationID'] == $formfield['fflocationname'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowloc['dblocationID'] . '"' . $checker . '>' . 
												$rowloc['dblocationName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											
											
											<div class="col-sm-9 pt-2">
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3 pt-2">
                                               <button type="submit" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
									</form>
                                </div>
                            </div>
							
                           <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-2">
                                    			<img id="rloclogo" alt="Location Logo" class = "pt-2" style="height: 240px; vertical-align: middle;" src="./images/restloclogo3.svg">

								 </div>
                            </div>
                        </div>
                    </div> 
               </div>
          </div>
     </div>
</div>	
<?php 
}#2-1
/***************************************************************************************/
/*Here, the form that will display indicates the fact that the submit button was pressed 
by the user and the formfields will hold the values entered by the current user logged in.*/
else if ((($showform == 1) || ($showform == 2) || ($showform == 3) ||
		($showform == 6) || ($showform == 8) || ($showform == 7)) && ($formtype == 2))
{	
?>
<!---form to display if no records are found-->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Restaurant Location Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12 pb-2">
											 <div class="form-group">
												<label for="locationname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Location Name</label>
												<select class="form-select" id="locationname" name="locationname">
												<option value="">SELECT LOCATION</option>
												<?php while ($rowloc = $resultloc->fetch() )
												{
												if ($rowloc['dblocationID'] == $formfield['fflocationname'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowloc['dblocationID'] . '"' . $checker . '>' . 
												$rowloc['dblocationName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											
											
											<div class="col-sm-9 pt-2">
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3 pt-2">
                                               <button type="submit" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
									</form>
                                </div>
                            </div>
							
                           <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-2">
                                    			<img id="rloclogo" alt="Location Logo" class = "pt-2" style="height: 240px; vertical-align: middle;" src="./images/restloclogo3.svg">

								 </div>
                            </div>
                        </div>
                    </div> 
               </div>
          </div>
     </div>
</div>	
<?php
}#2-2
/*Here, the form that will display indicates the fact that the submit button was pressed 
by the user and the formfields will hold the values entered by the current user logged in.*/
else if ((($showform == 1) || ($showform == 2) || ($showform == 3) ||
		($showform == 6) || ($showform == 8) || ($showform == 7)) && ($formtype == 3))
{	
?>
<!---form to display if records are found-->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Restaurant Location Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12 pb-2">
											 <div class="form-group">
												<label for="locationname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Location Name</label>
												<select class="form-select" id="locationname" name="locationname">
												<option value="">SELECT LOCATION</option>
												<?php while ($rowloc = $resultloc->fetch() )
												{
												if ($rowloc['dblocationID'] == $formfield['fflocationname'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowloc['dblocationID'] . '"' . $checker . '>' . 
												$rowloc['dblocationName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											
											
											<div class="col-sm-9 pt-2">
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3 pt-2">
                                               <button type="submit" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
									</form>
                                </div>
                            </div>
							
                           <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-2">
                                    			<img id="rloclogo" alt="Location Logo" class = "pt-2" style="height: 240px; vertical-align: middle;" src="./images/restloclogo3.svg">

								 </div>
                            </div>
                        </div>
                    </div> 
               </div>
          </div>
     </div>
</div>	
<div class = "container">
<table class = "table table-striped table-bordered w-25 p-3">
<thead class="thead-dark border border-dark bg-dark text-light">
	<tr>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Restaurant Location</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">&nbsp;</th>
	</tr>
</thead>
<tbody class = "border border-dark">	
	<?php 
	// We display our table data based on our SQL query above
	while ($row = $result-> fetch() )
		{
				echo '<tr><td>' . $row['dblocationName'] . '</td><td>' .
				//This will make a button that sends the user to another page "updatecategory.php"
				'<form action="boot_updatelocation.php" method="post">
					<input type="hidden" name="locid" value="'. $row['dblocationID'] .'">
					<input type="submit" class = "rounded-3 bg-dark text-light" name="theedit" value="VIEW">
				</form>' . '</td></tr>';
				
			}
		?>
</tbody>
</table>
</div>	
<?php
}#2-3
// Indicates the user is not logged in, so log in form will show
if ($showform == 4)
{
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