<?php
$pagetitle = 'Add Job Title';
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

//For our permission drop down menu
$sqlselectp = "SELECT * from permission";
$resultp = $db->prepare($sqlselectp);
$resultp->execute();

/**Notice here that if the clear button is pressed, the form type 
that is displayed will become the $formtype of 1. The same applies 
if the new submit button is pressed. This means that the user would
essentially start over and enter a new job title record into the DB.
This clears the input boxes so the user can start over and add a 
new record to our DB table.
**/
if (isset($_POST['theclear']))	{
	$formtype = 1; 
}
//This resets the original default form as well.
if (isset($_POST['thenewsubmit']))	{
	$formtype = 1; 
}

//Checking to see if the submit button of first form was submitted
		if( isset($_POST['thesubmit']) )
		{

			//Data Cleansing
			$formfield['ffjobname'] = trim($_POST['jobname']);
			$formfield['ffpermitid'] = trim($_POST['permitid']);

			/*  ****************************************************************************
     		CHECK FOR EMPTY FIELDS
    		Complete the lines below for any REQIURED form fields only.
			Do not do for optional fields.
			****************************************************************************/
			if(empty($formfield['ffjobname'])){$errormsg .= "Your Job Title is empty! ";}
			if(empty($formfield['ffpermitid'])){$errormsg .= "Your Permission is empty! ";}

	//Looking for duplicate item 
    $checkitem = "SELECT * FROM job WHERE dbjobName = '" . $formfield['ffjobname'] . "' ";
    $result = $db->query($checkitem);
    $count = $result->rowCount();
	
	if ($count > 0) {
        $errormsg .= "Job Title already exists. Please enter a new job title! ";
    }
	
	/*  ****************************************************************************
			DISPLAY ERRORS
			If we have concatenated the error message with details, then let the user know.
			If there is an error, the $formtype changes, which is the same except for the 
			fact that the form will hold the values entered by the user.
			**************************************************************************** */
			if($errormsg != "")
			{
			echo '<div class = "container ps-5">';
			echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
			echo "There are errors! " . $errormsg . '</div></div>';
			$formtype = 2;
			}
			else
			{
				try
				
				//If there are no errors, we will try to insert our data into the database. 
				{
				$sqlinsert = 'INSERT INTO job (dbjobName, dbpermissionID)
						VALUES (:bvjobname, :bvpermitid)';
				$stmtinsert = $db->prepare($sqlinsert);
				$stmtinsert->bindvalue(':bvjobname', $formfield['ffjobname']);
				$stmtinsert->bindvalue(':bvpermitid', $formfield['ffpermitid']);
				$stmtinsert->execute();
				
			//Successful data insert message
			echo '<div class = "container ps-5">';
			echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
			echo "There are no errors! Form has been submitted." . '</div></div>';
				
				//Now we select the newly added data to our DB
				$sqlselectj = 'SELECT job.*, permission.* FROM job,
				permission WHERE job.dbpermissionID = permission.dbpermissionID
				AND job.dbjobName = :bvjobname';
				$resultj = $db->prepare($sqlselectj);
				$resultj->bindvalue(':bvjobname', $formfield['ffjobname']);
				$resultj->execute();
				$rowj = $resultj->fetch();
				
				//Now we select the newly added data to our DB
				$sqlselect = 'SELECT job.*, permission.* FROM job,
				permission WHERE job.dbpermissionID = permission.dbpermissionID';
				$result = $db->prepare($sqlselect);
			//	$result->bindvalue(':bvjobname', $formfield['ffjobname']);
				$result->execute();
			//	$row = $result->fetch();
				//Indicates record has been successfully updated
				$formtype = 3;
					
				}//try
				catch(PDOException $e)
				{
					echo 'ERROR!!!' .$e->getMessage();
					exit();
				}
			}//else statement end				
		}//ends if isset

// Indicates Administrative positions with default formtype 1
if (($showform == 5)  && ($formtype == 1))
{
?>
<!--default formtype 1---->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Job Title Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "jobname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Job Title<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="jobname" id="jobname" class="form-control" placeholder = "Enter Job Title Name" pattern="[^'\x25]+"
														 value ="<?php echo $formfield['ffjobname']; ?>" title= "Input cannot contain special characters.">
                                                </div>
                                            </div>
											<div class="col-12">
											 <div class="form-group">
												<label for="permitid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Permission Level<span class="text-light">*</span></label>
												<select class="form-select" id="permitid" name="permitid" required>
												<option value="">SELECT PERMISSION</option>
												<?php while ($rowp = $resultp->fetch() )
												{
												if ($rowp['dbpermissionID'] == $formfield['ffpermitid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowp['dbpermissionID'] . '"' . $checker . '>' . 
												$rowp['dbpermissionTitle'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											
											
											<div class="col-sm-9">
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3">
                                               <button type="reset" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
									</form>
                                </div>
                            </div>
							
                           <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-4">
                                    			<img id="joblogo" alt="Job Logo" class = "pt-4" style="height: 255px; vertical-align: middle;" src="./images/joblogo1.svg">

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
// Indicates Administrative position with $formtype 2, meaning, the submit button 
// was pressed and some sort of error has occurred and will be displayed.
else if (($showform == 5)  && ($formtype == 2))
{
?>			
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Job Title Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "jobname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Job Title<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="jobname" id="jobname" class="form-control" pattern="[^'\x25]+"
														 value ="<?php echo $formfield['ffjobname']; ?>" title= "Input cannot contain special characters.">
                                                </div>
                                            </div>
											<div class="col-12">
											 <div class="form-group">
												<label for="permitid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Permission Level<span class="text-light">*</span></label>
												<select class="form-select" id="permitid" name="permitid">
												<option value="">SELECT PERMISSION</option>
												<?php while ($rowp = $resultp->fetch() )
												{
												if ($rowp['dbpermissionID'] == $formfield['ffpermitid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowp['dbpermissionID'] . '"' . $checker . '>' . 
												$rowp['dbpermissionTitle'] . '</option>';
												}
												?>
												</select>
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
									</form>
                                </div>
                            </div>
							
                           <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-4">
                                    			<img id="joblogo" alt="Job Logo" class = "pt-4" style="height: 255px; vertical-align: middle;" src="./images/joblogo1.svg">

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
 // Indicates Administrative position with $formtype 3, which means a successful
// entry has been made into the database and be displayed in table below the form.
else if (($showform == 5)  && ($formtype == 3))
{
?>		
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Job Title Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "jobname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Job Title<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="jobname" id="jobname" class="form-control" pattern="[^'\x25]+"
														 value ="<?php echo $rowj['dbjobName']; ?>" title= "Input cannot contain special characters.">
                                                </div>
                                            </div>
											<div class="col-12">
											 <div class="form-group">
												<label for="permitid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Permission Level<span class="text-light">*</span></label>
												<select class="form-select" id="permitid" name="permitid" required>
												<option value="">SELECT PERMISSION</option>
												<?php while ($rowp = $resultp->fetch() )
												{
												if ($rowp['dbpermissionID'] == $formfield['ffpermitid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowp['dbpermissionID'] . '"' . $checker . '>' . 
												$rowp['dbpermissionTitle'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											
											
											<div class="col-12">
                                               <button type="submit" name="thenewsubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">ADD NEW JOB TITLE</button>
                                            </div>
									</form>
                                </div>
                            </div>
							
                           <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-4">
                                    			<img id="joblogo" alt="Job Logo" class = "pt-4" style="height: 255px; vertical-align: middle;" src="./images/joblogo1.svg">

								 </div>
                            </div>
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
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Job Title</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Permission Title</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Permission Description</th>
	</tr>
</thead>
<tbody class = "border border-dark">
<?php 
//display our query results
while ($row = $result-> fetch())

{		
	echo '<tr><td>' . $row['dbjobName'] . '</td><td>' . $row['dbpermissionTitle'] 
	. '</td><td>' . $row['dbpermissionDescription'] . '</td></tr>';
}	
?>
</tbody>
</table>
</div>
<?php
}#1-3
if (($showform == 1) || ($showform == 2) || ($showform == 3) ||
		($showform == 6) || ($showform == 7) || ($showform == 8)) {
	echo '<div class = "container ps-5">';
	echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
	echo "You do not have authorization to access this page!" . '</div></div>';
}//non-permissible users
?>
<?php 
// Indicates the user is not logged in, so log in form will show
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