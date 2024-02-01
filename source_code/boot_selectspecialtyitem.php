<?php
$pagetitle = "Select Specialty Menu Item";
require_once 'boot_header.php';
require_once "connect.php";
	
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
//Setting our empty message variables
$stringclause = '';
$errormsg = "";
// We are setting our default formtype variable, which will be 
//displayed to the appropriate personnel
$formtype = 1;
	
/**********************************************************************************************************/	
//For our category drop down menu in our forms below
$sqlselectcat = "SELECT * from category WHERE dbcategoryID 
	BETWEEN 7 AND 16 ORDER BY dbcategoryName";
$resultcat = $db->prepare($sqlselectcat);
$resultcat->execute();

//For our status drop down menu in our forms below
$sqlselectstatus = "SELECT * from foodstatus";
$resultstatus = $db->prepare($sqlselectstatus);
$resultstatus->execute();

//For our item type drop down menu in our forms below
$sqlselecttype = "SELECT * from menuitemType WHERE menuitemType.
	dbmenuitemTypeID = 2";
$resulttype = $db->prepare($sqlselecttype);
$resulttype->execute();
$rowtype = $resulttype->fetch();

//Here we pull locations associated with the user logged in, setting the 
//default value to be the location session variable of the current user.
//As they change their current location, so will the drop down default value.

$sqlselectloc = "SELECT location.* FROM 
						employee, location, locationDetail WHERE 
						locationDetail.dblocationID = location.dblocationID AND 
						locationDetail.dbemployeeID = employee.dbemployeeID AND 
						employee.dbemployeeID = :bvempid ORDER BY CASE WHEN 
						location.dblocationID = :bvlocid THEN 1 ELSE 2 END, dblocationID";
$resultselectloc = $db->prepare($sqlselectloc);
$resultselectloc->bindvalue(':bvempid', $_SESSION['empid']);
$resultselectloc->bindvalue(':bvlocid', $_SESSION['emplocationid']);
$resultselectloc->execute();



/**Notice here that if the clear button is pressed, the form type 
that is displayed will become the $formtype of 1. The same applies 
if the new submit button is pressed. This means that the user would
essentially start over and select a new menu item record from the DB.
This clears the input boxes and drop downs so the user can start over
and search for a new record in our DB table.
**/
if (isset($_POST['theclear']))	{
	$formtype = 1; 
}
/*Here is where we check to see if the submit button is pressed by the user. If so, 
we cleanse our formfield data and attempt to pull the appropriate record from the DB.
*/			
if( isset($_POST['thesubmit']) )
{
	//Now we cleanse our data from our formfields below
	$formfield['ffitemname'] = trim($_POST['itemname']);
	$formfield['ffitemcat'] = trim($_POST['itemcat']);
	$formfield['ffitemloc'] = trim($_POST['itemloc']);
	$formfield['ffitemstat'] = trim($_POST['itemstat']);
	
//Here we check and see if the submit button was pressed with ALL empty fields
	if ((empty($formfield['ffitemname'])) && (empty($formfield['ffitemcat'])) 
		&& (empty($formfield['ffitemloc'])) && (empty($formfield['ffitemstat']))) {
					
				$errormsg .= "You must choose at least one search method! ";	
				}
		
//Notice that if there is an error message, it will simply be because no search criteria was used at all
//so essentially, taking the user to the very beginning, assigning the formtype to equal 1.
	if ($errormsg != "") {
		echo '<div class = "container ps-5">';
		echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
		echo "There are errors! " . $errormsg . '</div></div>';
		//This is essentially starting over since the form has nothing entered to find
		$formtype = 1;	
	} else
		
	{
	//Now we select our menu item based on the following criteria
	$sqlselect = "SELECT menuitem.*, foodstatus.*, category.*, menuitemType.*, 
		location.*, menuitemDetail.* FROM menuitem, foodstatus, category, menuitemType, menuitemDetail,
		location WHERE menuitemDetail.dbmenuitemID = menuitem.dbmenuitemID AND
		menuitem.dbcategoryID = category.dbcategoryID AND category.dbcategoryID = 
		:bvitemcat AND menuitemDetail.dbfoodstatusID = foodstatus.dbfoodstatusID AND
		menuitemDetail.dblocationID = location.dblocationID AND
		menuitem.dbmenuitemTypeID = menuitemType.dbmenuitemTypeID AND 
		location.dblocationID = :bvitemloc AND
		menuitemType.dbmenuitemTypeID = :bvitemtype AND
		foodstatus.dbfoodstatusID = :bvitemstat AND
		menuitem.dbmenuitemName like CONCAT('%', :bvitemname,'%')";
	// We prepare and bind our values so we can gather the appropriate DB records
	$result = $db->prepare($sqlselect);
	$result->bindValue(':bvitemname', $formfield['ffitemname']);
	$result->bindValue(':bvitemcat', $formfield['ffitemcat']);
	$result->bindValue(':bvitemloc', $formfield['ffitemloc']);
	$result->bindValue(':bvitemstat', $formfield['ffitemstat']);
	$result->bindValue(':bvitemtype', 2);
	$result->execute();
	}
	
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
		
} // ends if isset

/***************************************************************************************/
/*The following default formtype 1 is displayed for Administrative users*/
if (($showform == 5) && ($formtype == 1))
{	
?>
<!--------This is the default form that shows up with Administrative users-------------------------->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Specialty Menu Item Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
						<form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "itemtype" class ="text-light" style="font-family: 'Raleway', sans-serif;">Menu Item Type<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="itemtype" id="itemtype" class="form-control" value ="<?php echo $rowtype['dbmenuitemTypeName']; ?>" readonly>
                                                </div>
                                            </div>
											<div class="col-12">
											 <div class="form-group">
												<label for="itemcat" class ="text-light" style="font-family: 'Raleway', sans-serif;">Food Category</label>
												<select class="form-select" id="itemcat" name="itemcat">
											<!--	<option value="">SELECT CATEGORY</option>-->
												<?php while ($rowcat = $resultcat->fetch() )
												{
												if ($rowcat['dbcategoryID'] == $formfield['ffitemcat'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowcat['dbcategoryID'] . '"' . $checker . '>' . 
												$rowcat['dbcategoryName'] . '</option>';
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
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
                                                <label for = "itemname" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="itemname" id="itemname" class="form-control border-dark"
														placeholder = "Search Menu Item Name" value = "<?php echo $formfield['ffitemname']; ?>">
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="itemloc" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Location</label>
												<select class="form-select border-dark" id="itemloc" name="itemloc">
										<!--		<option value="">SELECT LOCATION</option>-->
												<?php //Notice here that our query is pulling total locations
												//that the current logged in user is associated with.
												while ($rowselectloc = $resultselectloc->fetch() )
												{
												if ($rowselectloc['dblocationID'] == $formfield['ffitemloc'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowselectloc['dblocationID'] . '"' . $checker . '>' . 
												$rowselectloc['dblocationName'] . '</option>';
												}
												?>
												</select>
											 </div>
										</div>
										<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="itemstat" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Status</label>
												<select class="form-select border-dark" id="itemstat" name="itemstat">
										<!--		<option value="">SELECT FOOD STATUS</option>-->
												<?php 
												while ($rowstatus = $resultstatus->fetch() )
												{
												if ($rowstatus['dbfoodstatusID'] == $formfield['ffitemstat'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowstatus['dbfoodstatusID'] . '"' . $checker . '>' . 
												$rowstatus['dbfoodstatusName'] . '</option>';
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
}#1-1
/***************************************************************************************/
/*Here, the form that will display indicates the fact that the submit button was pressed 
by the user and the formfields will hold the values entered by the current user logged in.*/
if (($showform == 5) && ($formtype == 2))
{	
?>
<!---form displayed if no records are found---->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Specialty Menu Item Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
						<form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "itemtype" class ="text-light" style="font-family: 'Raleway', sans-serif;">Menu Item Type<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="itemtype" id="itemtype" class="form-control" value ="<?php echo $rowtype['dbmenuitemTypeName']; ?>" readonly>
                                                </div>
                                            </div>
											<div class="col-12">
											 <div class="form-group">
												<label for="itemcat" class ="text-light" style="font-family: 'Raleway', sans-serif;">Food Category</label>
												<select class="form-select" id="itemcat" name="itemcat">
											<!--	<option value="">SELECT CATEGORY</option>-->
												<?php while ($rowcat = $resultcat->fetch() )
												{
												if ($rowcat['dbcategoryID'] == $formfield['ffitemcat'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowcat['dbcategoryID'] . '"' . $checker . '>' . 
												$rowcat['dbcategoryName'] . '</option>';
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
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
                                                <label for = "itemname" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="itemname" id="itemname" class="form-control border-dark"
														value = "<?php echo $formfield['ffitemname']; ?>">
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="itemloc" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Location</label>
												<select class="form-select border-dark" id="itemloc" name="itemloc">
										<!--		<option value="">SELECT LOCATION</option>-->
												<?php //Notice here that our query is pulling total locations
												//that the current logged in user is associated with.
												while ($rowselectloc = $resultselectloc->fetch() )
												{
												if ($rowselectloc['dblocationID'] == $formfield['ffitemloc'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowselectloc['dblocationID'] . '"' . $checker . '>' . 
												$rowselectloc['dblocationName'] . '</option>';
												}
												?>
												</select>
											 </div>
										</div>
										<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="itemstat" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Status</label>
												<select class="form-select border-dark" id="itemstat" name="itemstat">
										<!--		<option value="">SELECT FOOD STATUS</option>-->
												<?php 
												while ($rowstatus = $resultstatus->fetch() )
												{
												if ($rowstatus['dbfoodstatusID'] == $formfield['ffitemstat'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowstatus['dbfoodstatusID'] . '"' . $checker . '>' . 
												$rowstatus['dbfoodstatusName'] . '</option>';
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
}#1-2
/***************************************************************************************/
/*Here, the form that will display indicates the fact that the submit button was pressed 
by the user and the formfields will hold the values entered by the current user logged in.*/
if (($showform == 5) && ($formtype == 3))
{	
?>
<!---form displayed if at least one record are found---->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Specialty Menu Item Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
						<form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "itemtype" class ="text-light" style="font-family: 'Raleway', sans-serif;">Menu Item Type<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="itemtype" id="itemtype" class="form-control" value ="<?php echo $rowtype['dbmenuitemTypeName']; ?>" readonly>
                                                </div>
                                            </div>
											<div class="col-12">
											 <div class="form-group">
												<label for="itemcat" class ="text-light" style="font-family: 'Raleway', sans-serif;">Food Category</label>
												<select class="form-select" id="itemcat" name="itemcat">
											<!--	<option value="">SELECT CATEGORY</option>-->
												<?php while ($rowcat = $resultcat->fetch() )
												{
												if ($rowcat['dbcategoryID'] == $formfield['ffitemcat'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowcat['dbcategoryID'] . '"' . $checker . '>' . 
												$rowcat['dbcategoryName'] . '</option>';
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
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
								            <div class="col-12 pb-3">
                                                <label for = "itemname" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="itemname" id="itemname" class="form-control border-dark"
														value = "<?php echo $formfield['ffitemname']; ?>">
                                                </div>
                                            </div>
											<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="itemloc" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Location</label>
												<select class="form-select border-dark" id="itemloc" name="itemloc">
										<!--		<option value="">SELECT LOCATION</option>-->
												<?php //Notice here that our query is pulling total locations
												//that the current logged in user is associated with.
												while ($rowselectloc = $resultselectloc->fetch() )
												{
												if ($rowselectloc['dblocationID'] == $formfield['ffitemloc'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowselectloc['dblocationID'] . '"' . $checker . '>' . 
												$rowselectloc['dblocationName'] . '</option>';
												}
												?>
												</select>
											 </div>
										</div>
										<div class="col-12 pb-3">
											 <div class="form-group">
												<label for="itemstat" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Status</label>
												<select class="form-select border-dark" id="itemstat" name="itemstat">
										<!--		<option value="">SELECT FOOD STATUS</option>-->
												<?php 
												while ($rowstatus = $resultstatus->fetch() )
												{
												if ($rowstatus['dbfoodstatusID'] == $formfield['ffitemstat'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowstatus['dbfoodstatusID'] . '"' . $checker . '>' . 
												$rowstatus['dbfoodstatusName'] . '</option>';
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
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Menu Item Type</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Category</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Item Name</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Menu Button Name</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Menu Price</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Inventory Unit Cost</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Item Status</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">&nbsp;</th>
	</tr>
</thead>
<tbody class = "border border-dark">	
<?php 
	// We display our table data based on our SQL query above
	while ($row = $result-> fetch() )
			{
				echo '<tr><td>' . $row['dbmenuitemTypeName'] . '</td><td>' . $row['dbcategoryName'] . 
				'</td><td> ' . $row['dbmenuitemName'] . '</td><td>' . $row['dbmenuitemButton'] . 
				'</td><td>$' . $row['dbmenuitemDetailPrice'] . '</td><td>$' . $row['dbmenuitemDetailCost'] .
				'</td><td>' . $row['dbfoodstatusName'] . '</td><td>' .
				
				//This will make a button that sends the user to another page "updatemenuitem.php"
				'<form action="boot_updatespecialtyitem.php" method="post">
					<input type="hidden" name="menuitemid" value="'. $row['dbmenuitemID'] .'">
					<input type="hidden" name="menuitemdetailid" value="'. $row['dbmenuitemDetailID'] .'">
					<input type="hidden" name="itemtypeid" value="'. $row['dbmenuitemTypeID'] .'">
					<input type="hidden" name="locationid" value="'. $row['dblocationID'] .'">
					<input type="hidden" name="statusid" value="'. $row['dbfoodstatusID'] .'">
					<input type="hidden" name="catid" value="'. $row['dbcategoryID'] .'">
					<input type="submit" class = "rounded-3 bg-dark text-light" name="theedit" value="EDIT">
				</form>' . '</td></tr>';
				
			}
?>	
</tbody>
</table>
</div>	
<?php
}#1-3
// If permission level is indicative of the rest of staff members (no authorization)
else if (($showform == 1) || ($showform == 2) || ($showform == 3) ||
		($showform == 6) || ($showform == 7) || ($showform == 8))
{	
echo '<div class = "container ps-5">';
	echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
	echo "You do not have authorization to access this page!" . '</div></div>';
}//ends if else
?>
<?php
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