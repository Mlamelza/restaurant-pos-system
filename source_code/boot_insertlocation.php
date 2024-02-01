<?php
$pagetitle = 'Add Restaurant Location';
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
/*$selectmenuids = 'SELECT DISTINCT menuitemDetail.dbmenuitemID, menuitemDetail.dbfoodstatusID, 
					menuitemDetail.dbmenuitemDetailPrice, menuitemDetail.dbmenuitemDetailCost FROM menuitemDetail, 
					menuitem, menuitemType WHERE menuitem.dbmenuitemID = menuitemDetail.dbmenuitemID AND 
					menuitem.dbmenuitemTypeID = menuitemType.dbmenuitemTypeID AND menuitemType.dbmenuitemTypeID = 1';
					$resultmenuids = $db->prepare($selectmenuids);
					$resultmenuids->execute();
$priceids = array();
// price id values dump
while ($rowmenuids = $resultmenuids->fetch()) {
		$priceids[] = $rowmenuids['dbmenuitemDetailPrice'];
		echo $rowmenuids['dbmenuitemDetailPrice'] . '<br>';
		}*/
/**Notice here that if the clear button is pressed, the form type 
that is displayed will become the $formtype of 1. The same applies 
if the new submit button is pressed. This means that the user would
essentially start over and enter a new location record into the DB.
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
			$formfield['fflocationname'] = trim($_POST['locationname']);
			/*  ****************************************************************************
     		CHECK FOR EMPTY FIELDS
    		Complete the lines below for any REQIURED form fields only.
			Do not do for optional fields.
    		**************************************************************************** */
			if(empty($formfield['fflocationname'])){$errormsg .= "Your Location is empty! ";}
			
			//Looking for duplicate item 
			$checkloc = "SELECT * FROM location WHERE dblocationName = '" . $formfield['fflocationname'] . "' ";
			$result = $db->query($checkloc);
			$count = $result->rowCount();
	
			if ($count > 0) {
				$errormsg .= "Location already exists! Please enter a new location! ";
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
					$sqlinsert = 'INSERT INTO location (dblocationName)
								  VALUES (:bvlocname)';
					$stmtinsert = $db->prepare($sqlinsert);
					$stmtinsert->bindvalue(':bvlocname', $formfield['fflocationname']);
					$stmtinsert->execute();
					
					//Now we select the newest added location record and obtain the id
					$sqlmax = "SELECT MAX(dblocationID) AS maxid FROM location";
					$resultmax = $db->prepare($sqlmax);
					$resultmax->execute();
					$rowmax = $resultmax->fetch();
					$maxid = $rowmax["maxid"]; // Now we have our last location id that was added into the DB
					
					//Next, we pull the employee ids that are associated with an Administrative position
					$selectempids = 'SELECT DISTINCT employee.* FROM jobDetail, employee, job 
						WHERE job.dbjobID = jobDetail.dbjobID AND jobDetail.dbemployeeID = 
						employee.dbemployeeID AND job.dbjobID = 1';
					$resultempids = $db->prepare($selectempids);
					$resultempids->execute();
					//We create an array that will hold our employee id values
					$empids = array();
					//We run a while loop that will dump our values from our sql
					//select statement into our array that was created for emp ids
					while ($rowempids = $resultempids->fetch()) {
						$empids[] = $rowempids['dbemployeeID'];
					}
					/*We will run a foreach loop that will insert into our locationDetail
					table the new location id that was added along with our employee
					ids associated with an Administrative position held within the array.*/
					foreach($empids as $empid) {
					$locationinsert = 'INSERT INTO locationDetail (dblocationID, dbemployeeID)
						VALUES (:bvlocationid, :bvemployeeid)';
					$stmtlocationinsert = $db->prepare($locationinsert);
					$stmtlocationinsert->bindvalue(':bvlocationid', $maxid);
					$stmtlocationinsert->bindvalue(':bvemployeeid', $empid);
					$stmtlocationinsert->execute(); 	
					}
					/*Now we run the same type of operations with our regular menu items. We will
					select all regular menu items and then dump the values into separate arrays.*/
					$selectmenuids = 'SELECT DISTINCT menuitem.*, menuitemDetail.dbmenuitemDetailCost, 
						menuitemDetail.dbmenuitemDetailPrice FROM menuitemDetail, menuitem, menuitemType, 
						location WHERE menuitem.dbmenuitemID = menuitemDetail.dbmenuitemID AND 
						menuitem.dbmenuitemTypeID = menuitemType.dbmenuitemTypeID AND menuitemDetail.dblocationID 
						= location.dblocationID AND menuitemType.dbmenuitemTypeID = 1 AND 
						location.dblocationID != :bvnewlocid';
				/*	$selectmenuids = 'SELECT DISTINCT menuitemDetail.dbmenuitemID, menuitemDetail.dbfoodstatusID, 
					menuitemDetail.dbmenuitemDetailPrice, menuitemDetail.dbmenuitemDetailCost FROM menuitemDetail, 
					menuitem, menuitemType WHERE menuitem.dbmenuitemID = menuitemDetail.dbmenuitemID AND 
					menuitem.dbmenuitemTypeID = menuitemType.dbmenuitemTypeID AND menuitemType.dbmenuitemTypeID = 1';*/
					$resultmenuids = $db->prepare($selectmenuids);
					$resultmenuids->bindvalue(':bvnewlocid', $maxid);
					$resultmenuids->execute();
					//We create an array that will hold our regular menu item id values
					$menuids = array();
					//We create an array that will hold our food status id values
			//		$statusids = array();
					//We create an array that will hold our price id values
					$priceamounts = array();
					//We create an array that will hold our cost id values
					$costamounts = array();
					//We run our while loops that will dump our values from our sql
					//select statement into our arrays created for our id values
					// menu item id values dump
					while ($rowmenuids = $resultmenuids->fetch()) {
					$menuids[] = $rowmenuids['dbmenuitemID'];
					$priceamounts[] = $rowmenuids['dbmenuitemDetailPrice'];
					$costamounts[] = $rowmenuids['dbmenuitemDetailCost'];
					}
					
					$menuidstring = implode(",",$menuids);
					$pricestring = implode(",",$priceamounts);
					$coststring = implode(",",$costamounts);
					//We obtain our array length...notice all arrays have equal lengths
					$arrlength = count($menuids);

					//We start a for loop that will begin with the zero counter
					//and run through the length of our array(s). With each iteration,
					//we will insert the appropriate data into our DB table
					for($x = 0; $x < $arrlength; $x++) {
						$menulocationinsert = 'INSERT INTO menuitemDetail (dbmenuitemid, dblocationID,
							dbfoodstatusID, dbmenuitemDetailPrice, dbmenuitemDetailCost)
						VALUES (:bvmenuitemid, :bvlocationid, :bvfoodstatusid,
							:bvmenuitemprice, :menuitemcost)';
					$stmtmenulocationinsert = $db->prepare($menulocationinsert);
					$stmtmenulocationinsert->bindvalue(':bvlocationid', $maxid);
					$stmtmenulocationinsert->bindvalue(':bvmenuitemid', $menuids[$x]);
					$stmtmenulocationinsert->bindvalue(':bvfoodstatusid', 1);
					$stmtmenulocationinsert->bindvalue(':bvmenuitemprice', $priceamounts[$x]);
					$stmtmenulocationinsert->bindvalue(':menuitemcost', $costamounts[$x]);
					$stmtmenulocationinsert->execute(); 	
				//	}
				//	echo $costamounts[$x];
				//	echo "<br>";
					}
					
					$selectdetailids = 'SELECT menuitemDetail.dbmenuitemDetailID FROM menuitemDetail,
					menuitem, menuitemType, location WHERE menuitem.dbmenuitemID = menuitemDetail.dbmenuitemID 
					AND menuitem.dbmenuitemTypeID = menuitemType.dbmenuitemTypeID AND menuitemDetail.dblocationID = 
					location.dblocationID AND menuitemType.dbmenuitemTypeID = 1 AND location.dblocationID = :bvnewlocid';
					$resultdetailids = $db->prepare($selectdetailids);
					$resultdetailids->bindvalue(':bvnewlocid', $maxid);
					$resultdetailids->execute();
					//We create an array that will hold our regular menu item id values
					$menudetailids = array();
					while ($rowdetailids = $resultdetailids->fetch()) {
					$menudetailids[] = $rowdetailids['dbmenuitemDetailID'];
					}
					//We obtain our array length...notice all arrays have equal lengths
					$arrlength = count($menudetailids);
					
					//We start a for loop that will begin with the zero counter
					//and run through the length of our array(s). With each iteration,
					//we will insert the appropriate data into our DB table
					for($x = 0; $x < $arrlength; $x++) {
						$menuinventoryinsert = 'INSERT INTO inventory (dbmenuitemDetailID,
							dbinventoryAmount, dbinventoryDate)
						VALUES (:bvdetailid, 0, now())';
						$stmtmenuinventoryinsert = $db->prepare($menuinventoryinsert);
						$stmtmenuinventoryinsert->bindvalue(':bvdetailid', $menudetailids[$x]);
						$stmtmenuinventoryinsert->execute(); 	
					}


					// food status id values dump
			/*		while ($rowmenuids = $resultmenuids->fetch()) {
						$statusids[] = $rowmenuids['dbfoodstatusID'];
					}*/
					// price id values dump
			/*		while ($rowmenuids = $resultmenuids->fetch()) {
						$priceids[] = $rowmenuids['dbmenuitemDetailPrice'];
					}*/
					// cost id values dump
			/*		while ($rowmenuids = $resultmenuids->fetch()) {
						$costids[] = $rowmenuids['dbmenuitemDetailCost'];
					}*/
					/*We will run a foreach loop that will insert into our menuitemDetail
					table the new location id that was added along with our menu item
					ids associated with a regular menu item type held within the array.
					Notice we will do this for our other arrays as well.*/
				//	foreach($menuids as $menuid) {
						
				/*	$menulocationinsert = 'INSERT INTO menuitemDetail (dblocationID, dbmenuitemid)
						VALUES (:bvlocationid, :bvmenuitemid)';
					$stmtmenulocationinsert = $db->prepare($menulocationinsert);
					$stmtmenulocationinsert->bindvalue(':bvlocationid', $maxid);
					$stmtmenulocationinsert->bindvalue(':bvmenuitemid', $menuid);
					$stmtmenulocationinsert->execute(); 	*/
				//	}
					//food status ids
		/*			foreach($statusids as $statusid) {
					$foodstatusinsert = 'INSERT INTO  menuitemDetail (dbfoodstatusID)
						VALUES (:bvfoodstatusID)';
					$stmtfoodstatusinsert = $db->prepare($foodstatusinsert);
					$stmtfoodstatusinsert->bindvalue(':bvfoodstatusID', $statusid);
					$stmtfoodstatusinsert->execute(); 	
					}*/
					//menu item price ids
		/*			foreach($priceids as $priceid) {
					$menupriceinsert = 'INSERT INTO  menuitemDetail (dbmenuitemDetailPrice)
						VALUES (:bvpriceid)';
					$stmtmenupriceinsert = $db->prepare($menupriceinsert);
					$stmtmenupriceinsert->bindvalue(':bvpriceid', $priceid);
					$stmtmenupriceinsert->execute(); 	
					}*/
					//menu item cost ids
			/*		foreach($costids as $costid) {
					$menucostinsert = 'INSERT INTO  menuitemDetail (dbmenuitemDetailCost)
						VALUES (:bvcostid)';
					$stmtmenucostinsert = $db->prepare($menucostinsert);
					$stmtmenucostinsert->bindvalue(':bvcostid', $costid);
					$stmtmenucostinsert->execute(); 	
					}*/
		//Successful data insert message
			echo '<div class = "container ps-5">';
			echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
			echo "There are no errors! Form has been submitted." . '</div></div>';
					
					//Now we select the newly added data to our DB
					$sqlselect = 'SELECT * FROM location WHERE dblocationName = :bvlocname';
					$result = $db->prepare($sqlselect);
					$result->bindvalue(':bvlocname', $formfield['fflocationname']);
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
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Restaurant Location Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12 pb-2">
                                                <label for = "locationname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Location Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="locationname" id="locationname" class="form-control" placeholder = "Enter Location Name" pattern="[^'\x30]+"
														value ="<?php echo $formfield['fflocationname']; ?>" title= "Input cannot contain special characters.">
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
                                    			<img id="rloclogo" alt="Location Logo" class = "pt-2" style="height: 240px; vertical-align: middle;" src="./images/restloclogo4.svg">

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
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Restaurant Location Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12 pb-2">
                                                <label for = "locationname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Location Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="locationname" id="locationname" class="form-control" pattern="[^'\x30]+"
														value ="<?php echo $formfield['fflocationname']; ?>" title= "Input cannot contain special characters.">
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
                                    			<img id="rloclogo" alt="Location Logo" class = "pt-2" style="height: 240px; vertical-align: middle;" src="./images/restloclogo4.svg">

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
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Restaurant Location Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12 pb-2">
                                                <label for = "locationname" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Location Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="locationname" id="locationname" class="form-control" pattern="[^'\x30]+"
														value ="<?php echo $row['dblocationName']; ?>" title= "Input cannot contain special characters.">
                                                </div>
                                            </div>
											
											
											<div class="col-12 pt-2">
                                               <button type="submit" name="thenewsubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">ADD NEW LOCATION</button>
                                            </div>
									</form>
                                </div>
                            </div>
							
                           <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right border border-dark border-2 rounded-3 h-100 bg-white text-dark text-center pt-2">
                                    			<img id="rloclogo" alt="Location Logo" class = "pt-2" style="height: 240px; vertical-align: middle;" src="./images/restloclogo4.svg">

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
	</tr>
</thead>
<tbody class = "border border-dark">
<?php 
	
			
			echo '<tr><td>' . $row['dblocationName'] . '</td></tr>';
		
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