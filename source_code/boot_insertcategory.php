<?php
$pagetitle = 'Add Food Category';
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
/**Notice here that if the clear button is pressed, the form type 
that is displayed will become the $formtype of 1. The same applies 
if the new submit button is pressed. This means that the user would
essentially start over and enter a new category record into the DB.
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
			$formfield['ffcatname'] = trim($_POST['catname']);
			/*  ****************************************************************************
     		CHECK FOR EMPTY FIELDS
    		Complete the lines below for any REQIURED form fields only.
			Do not do for optional fields.
    		**************************************************************************** */
			if(empty($formfield['ffcatname'])){$errormsg .= "Your Category is empty! ";}
			
			//Looking for duplicate item 
			$checkcat = "SELECT * FROM category WHERE dbcategoryName = '" . $formfield['ffcatname'] . "' ";
			$result = $db->query($checkcat);
			$count = $result->rowCount();
	
			if ($count > 0) {
				$errormsg .= "Category Name already exists. Please enter a new category! ";
			}
			/*  ****************************************************************************
			DISPLAY ERRORS
			If we have concatenated the error message with details, then let the user know
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
				{
					//If there are no errors, we attempt to insert data into our DB
					$sqlinsert = 'INSERT INTO category (dbcategoryName)
								  VALUES (:bvcatname)';
					$stmtinsert = $db->prepare($sqlinsert);
					$stmtinsert->bindvalue(':bvcatname', $formfield['ffcatname']);
					$stmtinsert->execute();
					
					//Successful data insert message
			echo '<div class = "container ps-5">';
			echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
			echo "There are no errors! Form has been submitted." . '</div></div>';
					
					//Now we select the newly added data to our DB
					$sqlselect = 'SELECT * FROM category WHERE dbcategoryName =  :bvcatname';
					$result = $db->prepare($sqlselect);
					$result->bindvalue(':bvcatname', $formfield['ffcatname']);
					$result->execute();
					$row = $result->fetch();
					//Indicates record has been successfully updated
					$formtype = 3;
					
				}//try
				catch(PDOException $e)
				{
					echo 'ERROR!!!' .$e->getMessage();
					exit();
				}
			}//else statement end
		}//if isset submit

// Indicates Administrative positions with default formtype 1
if (($showform == 5)  && ($formtype == 1))
{
?>
<!--default formtype 1---->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Food Category Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12 pb-2">
                                                <label for = "catname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Category Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="catname" id="catname" class="form-control" placeholder = "Enter Food Category Name" pattern="[^'\x20]+"
														required value ="<?php echo $formfield['ffcatname']; ?>" title= "Input cannot contain special characters.">
                                                </div>
                                            </div>
											
											
											<div class="col-sm-9 pt-2">
                                               <button type="submit" name="thesubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">SUBMIT</button>
                                            </div>
											<div class="col-sm-3 pt-2">
                                               <button type="reset" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>
									</form>
                                </div>
                            </div>
							
                           <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-2">
                                    			<img id="catlogo" alt="Category Logo" class = "pt-2" style="height: 240px; vertical-align: middle;" src="./images/catlogo3.svg">

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
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Food Category Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12 pb-2">
                                                <label for = "catname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Category Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="catname" id="catname" class="form-control"
														value ="<?php echo $formfield['ffcatname']; ?>" pattern="[^'\x40]+" title= "Input cannot contain special characters.">
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
                                    			<img id="catlogo" alt="Category Logo" class = "pt-2" style="height: 240px; vertical-align: middle;" src="./images/catlogo3.svg">

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
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Food Category Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12 pb-2">
                                                <label for = "catname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Category Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="catname" id="catname" class="form-control"
														required value ="<?php echo $row['dbcategoryName']; ?>" pattern="[^'\x40]+" title= "Input cannot contain special characters.">
                                                </div>
                                            </div>
											
											
											<div class="col-12">
                                               <button type="submit" name="thenewsubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">ADD NEW CATEGORY</button>
                                            </div>
									</form>
                                </div>
                            </div>
							
                           <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-2">
                                    			<img id="catlogo" alt="Category Logo" class = "pt-2" style="height: 240px; vertical-align: middle;" src="./images/catlogo3.svg">

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
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Food Category Name</th>
	</tr>
</thead>
<tbody class = "border border-dark">
<?php 
	
			
			echo '<tr><td>' . $row['dbcategoryName'] . '</td></tr>';
		
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