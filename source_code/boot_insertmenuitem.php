<?php
$pagetitle = "Add Menu Item: All Locations";
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
// Setting our variable to an empty string, which will be used to display messages later
$errormsg = "";
$formtype = 1;
	
//For our category drop down menu
$sqlselectc = "SELECT * from category WHERE category.dbcategoryID 
	BETWEEN 7 AND 16 ORDER BY dbcategoryName";
$resultc = $db->prepare($sqlselectc);
$resultc->execute();

//For our item type drop down menu in our forms below
$sqlselecttype = "SELECT * from menuitemType WHERE menuitemType.
	dbmenuitemTypeID = 1";
$resulttype = $db->prepare($sqlselecttype);
$resulttype->execute();
$rowtype = $resulttype->fetch();

/*This will pull all locations associated with the user logged in, setting
the default location to be the current one the user is associated with.
In this case, since Administrators will be the only users allowed to enter
new menu items into the system, all locations will be given the same menu
item when added. When a regular menu item is added, it will automatically be 
associated with the Administrator's locations, which will be all locations.  */
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
//create an array that will hold locations associated with the user
$emplocations = array();
//Run a loop to dump the result set into the array
while ($rowselectloc = $resultselectloc->fetch()) {
	$emplocations[] = $rowselectloc['dblocationID'];
}

/**Notice here that if the clear button is pressed, the form type 
that is displayed will become the $formtype of 1. The same applies 
if the new submit button is pressed. This means that the user would
essentially start over and enter a new menu item record into the DB.
This clears the input boxes and drop downs so the user can start over
and add a new record to our DB table.
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
		$formfield['ffcatid'] = trim($_POST['catid']);
		$formfield['ffitemname'] = trim($_POST['itemname']);
		$formfield['ffitemdescr'] = trim($_POST['itemdescr']);
		$formfield['ffitemcost'] = trim($_POST['itemcost']);
		$formfield['ffitembutton'] = trim($_POST['itembutton']);
		$formfield['ffitemprice'] = trim($_POST['itemprice']);
	//	$itemlocations = $_POST['itemlocations'];
		
		/*  ****************************************************************************
     	CHECK FOR EMPTY FIELDS
    	Complete the lines below for any REQIURED form fields only.
		Do not do for optional fields.
    	**************************************************************************** */
			
		if(empty($formfield['ffitemname'])){$errormsg .= "Your Menu Item Name is empty! ";}
	//	if(empty($formfield['ffitemdescr'])){$errormsg .= "<p>Your item description field is empty.</p>";}
		if(empty($formfield['ffitemprice'])){$errormsg .= "Your Menu Item Price is empty! ";}
		if(empty($formfield['ffitemcost'])){$errormsg .= "Your Item Unit Cost is empty! ";}
		if(empty($formfield['ffcatid'])){$errormsg .= "Your Category is empty! ";}
		if(empty($formfield['ffitembutton'])){$errormsg .= "Your Menu Item Button is empty! ";}
		
	/*Notice here that I am checking to see if the array holding our 
	location values are either NULL or have a value of 0 within it. 
	This would indicate the user did not properly choose options
	since none of the location values equal 0.*/
	//if(($itemlocations == NULL) || (in_array(0, $itemlocations))){
	//	$errormsg .= "<p>Your location choice is empty!</p>";
	//}
	//Looking for duplicate item 
    $checkitem = "SELECT * FROM menuitem WHERE dbmenuitemName = '" . $formfield['ffitemname'] . "' ";
    $result = $db->query($checkitem);
    $count = $result->rowCount();
	
	if ($count > 0) {
        $errormsg .= "Menu item already exists. Please enter a new menu item! ";
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
		//	echo $errormsg;
				$formtype = 2;
			}
			else
			{
				try
				
				//If there are no errors, we will try to insert our data into the database. 
				{
				$sqlinsert = 'INSERT INTO menuitem (dbmenuitemName, dbcategoryID, 
								dbmenuitemDescript, dbmenuitemButton, dbmenuitemTypeID)
						VALUES (:bvitemname, :bvcatid, :bvitemdescr, :bvitembutton, :bvitemtype)';
				$stmtinsert = $db->prepare($sqlinsert);
				$stmtinsert->bindvalue(':bvitemname', $formfield['ffitemname']);
				$stmtinsert->bindvalue(':bvcatid', $formfield['ffcatid']);
				$stmtinsert->bindvalue(':bvitemdescr', $formfield['ffitemdescr']);
				$stmtinsert->bindvalue(':bvitembutton', $formfield['ffitembutton']);
				$stmtinsert->bindvalue(':bvitemtype', 1);
				$stmtinsert->execute();
					
				$sqlmax = "SELECT MAX(dbmenuitemID) AS maxid FROM menuitem";
				$resultmax = $db->prepare($sqlmax);
				$resultmax->execute();
				$rowmax = $resultmax->fetch();
				$maxid = $rowmax["maxid"]; // Now we have our last menuitem id that was inserted into the menuitem table
					
/*Now we take the current input within our $itemlocations array and we run
a loop to insert the location data for our new menu item inserted into the DB.*/
/*	foreach($itemlocations as $itemlocation) {
			$itemlocationinsert = 'INSERT INTO menuitemDetail (dbmenuitemID, dblocationID, 
								dbfoodstatusID, dbmenuitemDetailPrice)
					       VALUES (:bvmenuitem, :bvlocationid, :bvfoodstatus, :bvmenuprice)';
            $stmtlocationinsert = $db->prepare($itemlocationinsert);
			$stmtlocationinsert->bindvalue(':bvmenuitem', $maxid);
            $stmtlocationinsert->bindvalue(':bvlocationid', $itemlocation);
			$stmtlocationinsert->bindvalue(':bvfoodstatus', 1);
			$stmtlocationinsert->bindvalue(':bvmenuprice', $formfield['ffitemprice']);
			$stmtlocationinsert->execute(); 
			
	}*/
/*Now we take the current input within our $emplocations array and we run
a loop to insert the location data for our new menu item inserted into the DB.*/
	foreach($emplocations as $emplocation) {
			$itemlocationinsert = 'INSERT INTO menuitemDetail (dbmenuitemID, dblocationID, 
							dbfoodstatusID, dbmenuitemDetailPrice, dbmenuitemDetailCost)
			VALUES (:bvmenuitem, :bvlocationid, :bvfoodstatus, :bvmenuprice, :bvitemcost)';
            $stmtlocationinsert = $db->prepare($itemlocationinsert);
			$stmtlocationinsert->bindvalue(':bvmenuitem', $maxid);
            $stmtlocationinsert->bindvalue(':bvlocationid', $emplocation);
			$stmtlocationinsert->bindvalue(':bvfoodstatus', 1);
			$stmtlocationinsert->bindvalue(':bvmenuprice', $formfield['ffitemprice']);
			$stmtlocationinsert->bindvalue(':bvitemcost', $formfield['ffitemcost']);
			$stmtlocationinsert->execute(); 
	}
	
			$sqldetailmax = "SELECT MAX(dbmenuitemDetailID) AS maxdetailid FROM menuitemDetail";
			$resultdetailmax = $db->prepare($sqldetailmax);
			$resultdetailmax->execute();
			$rowdetailmax = $resultdetailmax->fetch();
			$maxdetailid = $rowdetailmax["maxdetailid"]; 
			// Now we have our last menuitemdetail id that was inserted into DB


			//Now we select our menuitemdetailid(s) that was/were inserted into the DB
			$selectdetailid = 'SELECT menuitem.*, menuitemDetail.* FROM menuitem,
				menuitemDetail WHERE menuitemDetail.dbmenuitemID = menuitem.dbmenuitemID
				AND menuitem.dbmenuitemID = :bvmenuitem';
			$resultdetailid = $db->prepare($selectdetailid);
			$resultdetailid->bindvalue(':bvmenuitem', $maxid);
			$resultdetailid->execute();
			//Now we create an array that will hold these values
			$itemdetails = array();
			/*We will run a while loop here and dump the results from our
			query into our array. Notice the assigned menuitemdetail ids to the array.*/
			while ($rowdetailid = $resultdetailid->fetch()) {
				$itemdetails[] = $rowdetailid['dbmenuitemDetailID'];
				}
	//Now for each menu item detail id entry, the inventory data will be entered in the DB
	foreach($itemdetails as $itemdetail){
	 		$sqlinvinsert = 'INSERT INTO inventory (dbmenuitemDetailID, dbinventoryAmount, 
									dbinventoryDate)
					         VALUES (:bvitemdetailid, 12, now())';
            $stmtinvinsert = $db->prepare($sqlinvinsert);
			$stmtinvinsert->bindvalue(':bvitemdetailid', $itemdetail);
			$stmtinvinsert->execute();
	}//ends foreach
				
			
		//Successful data insert message
			echo '<div class = "container ps-5">';
			echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
			echo "There are no errors! Form has been submitted." . '</div></div>';
		
		//We are now selecting the newly added data so that we can display it below
	/*	$sqlselect = 'SELECT menuitem.*, inventory.*, location.*, category.*, 
			foodstatus.*, menuitemDetail.*, menuitemType.* FROM menuitem, inventory, 
			location, menuitemDetail, category, foodstatus, menuitemType WHERE 
			menuitemDetail.dbmenuitemID = menuitem.dbmenuitemID AND 
			menuitemDetail.dblocationID = location.dblocationID AND
			inventory.dbmenuitemDetailID = menuitemDetail.dbmenuitemDetailID 
			AND category.dbcategoryID = menuitem.dbcategoryID AND 
			menuitem.dbmenuitemTypeID = menuitemType.dbmenuitemTypeID AND
			menuitemDetail.dbfoodstatusID = foodstatus.dbfoodstatusID AND 
			menuitem.dbmenuitemID = :bvmenuitem';*/
		$sqlselect = 'SELECT menuitem.*, inventory.*, category.*, foodstatus.*, 
			menuitemDetail.*, menuitemType.* FROM menuitem, inventory, menuitemDetail, 
			category, foodstatus, menuitemType WHERE menuitemDetail.dbmenuitemID = 
			menuitem.dbmenuitemID AND inventory.dbmenuitemDetailID = 
			menuitemDetail.dbmenuitemDetailID AND category.dbcategoryID = 
			menuitem.dbcategoryID AND menuitem.dbmenuitemTypeID = 
			menuitemType.dbmenuitemTypeID AND menuitemDetail.dbfoodstatusID = 
			foodstatus.dbfoodstatusID AND menuitem.dbmenuitemID = :bvmenuitem AND 
			menuitemDetail.dbmenuitemDetailID = :bvmenuitemdetail';	
		$result = $db->prepare($sqlselect);
		$result->bindvalue(':bvmenuitem', $maxid);
		$result->bindvalue(':bvmenuitemdetail', $maxdetailid);
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
}//ends if isset submit

// Indicates Administrative positions with default formtype 1
if (($showform == 5)  && ($formtype == 1))
{
?>
<!--default formtype 1---->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Menu Item Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "itemtype" class ="text-light" style="font-family: 'Raleway', sans-serif;">Menu Item Type<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="itemtype" id="itemtype" class="form-control" value ="<?php echo $rowtype['dbmenuitemTypeName']; ?>" required readonly>
                                                </div>
                                            </div>
											<div class="col-12 pt-2">
											 <div class="form-group">
												<label for="catid" class ="text-light" style="font-family: 'Raleway', sans-serif;">Food Category<span class="text-light">*</span></label>
												<select class="form-select" id="catid" name="catid" required>
												<option value="">SELECT CATEGORY</option>
												<?php while ($rowc = $resultc->fetch() )
												{
												if ($rowc['dbcategoryID'] == $formfield['ffcatid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowc['dbcategoryID'] . '"' . $checker . '>' . 
												$rowc['dbcategoryName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pt-2">
                                                <label for = "itemname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Menu Item Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="itemname" id="itemname" class="form-control" placeholder = "Enter Menu Item Name" required>
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
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
										<div class="col-12 pb-3">
                                                <label for = "itembutton" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Button Name<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="itembutton" id="itembutton" class="form-control border-dark" placeholder = "Enter Item Button Name" required >
                                                </div>
                                            </div>
			<!---Note! textarea label tags need to be on the same line in order for placeholder attribute to work!---->								
										<div class="col-12 pb-3">	
											<div class="form-group">
											<label for="itemdescr" class = "text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Description</label>
											<textarea style = "resize: none;" class="form-control border-dark" rows="3" cols="25" name = "itemdescr" id="itemdescr" placeholder = "Brief Description Here"></textarea>
											</div>
										</div>
								            <div class="col-12 pb-4">
                                                <label for = "itemprice" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Price<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="number" min="0.00" max="100" step="0.01" name ="itemprice" id="itemprice" pattern = "^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$"
														class="form-control border-dark" placeholder = "99.99" required>
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "itemcost"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Inventory Unit Cost<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="number" min="0.00" max="100" step="0.01" name ="itemcost" id="itemcost" pattern = "^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$"
														class="form-control border-dark" placeholder = "99.99" required>
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
// Indicates Administrative position with $formtype 2, meaning, the submit button 
// was pressed and some sort of error has occurred and will be displayed.
else if (($showform == 5)  && ($formtype == 2))
{
?>
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Menu Item Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                            <div class="col-12">
                                                <label for = "itemtype" class ="text-light" style="font-family: 'Raleway', sans-serif;">Menu Item Type<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="itemtype" id="itemtype" class="form-control" value ="<?php echo $rowtype['dbmenuitemTypeName']; ?>"  readonly>
                                                </div>
                                            </div>
											<div class="col-12 pt-2">
											 <div class="form-group">
												<label for="catid" class ="text-light" style="font-family: 'Raleway', sans-serif;">Food Category<span class="text-light">*</span></label>
												<select class="form-select" id="catid" name="catid" >
												<option value="">SELECT CATEGORY</option>
												<?php while ($rowc = $resultc->fetch() )
												{
												if ($rowc['dbcategoryID'] == $formfield['ffcatid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowc['dbcategoryID'] . '"' . $checker . '>' . 
												$rowc['dbcategoryName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pt-2">
                                                <label for = "itemname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Menu Item Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="itemname" id="itemname" class="form-control"
														value="<?php echo $formfield['ffitemname']; ?>" >
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
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
										<div class="col-12 pb-3">
                                                <label for = "itembutton" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Button Name<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="itembutton" id="itembutton" class="form-control border-dark"
													value="<?php echo $formfield['ffitembutton']; ?>"  >
                                                </div>
                                            </div>
			<!---Note! textarea label tags need to be on the same line in order for placeholder attribute to work!---->								
										<div class="col-12 pb-3">	
											<div class="form-group">
											<label for="itemdescr" class = "text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Description</label>
											<textarea style = "resize: none;" class="form-control border-dark" rows="3" cols="25" name = "itemdescr" id="itemdescr"><?php echo $formfield['ffitemdescr']; ?></textarea>
											</div>
										</div>
								            <div class="col-12 pb-4">
                                                <label for = "itemprice" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Price<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="number" min="0.00" max="100" step="0.01" name ="itemprice" id="itemprice" pattern = "^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$"
														class="form-control border-dark" value="<?php echo $formfield['ffitemprice']; ?>" >
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "itemcost"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Inventory Unit Cost<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="number" min="0.00" max="100.00" step="0.01" name ="itemcost" id="itemcost" pattern = "^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$"
														class="form-control border-dark" value="<?php echo $formfield['ffitemcost']; ?>" >
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
 // Indicates Administrative position with $formtype 3, which means a successful
// entry has been made into the database and be displayed in table below the form.
else if (($showform == 5)  && ($formtype == 3))
{
//pulling location, food status, price, and inventory details about current menu item		
$sqlselect3 = 'SELECT location.* FROM foodstatus, location, 
				menuitem, menuitemDetail WHERE menuitemDetail.dblocationID 
				= location.dblocationID AND menuitemDetail.dbfoodstatusID 
				= foodstatus.dbfoodstatusID AND menuitemDetail.dbmenuitemID 
				= menuitem.dbmenuitemID AND menuitem.dbmenuitemID = :bvmenuitem';
			$result3 = $db->prepare($sqlselect3);
			$result3->bindValue(':bvmenuitem', $maxid);
			$result3->execute();
			$locationcount = $result3->rowCount();
$sqlselect4 = 'SELECT foodstatus.* FROM foodstatus, location, 
				menuitem, menuitemDetail WHERE menuitemDetail.dblocationID 
				= location.dblocationID AND menuitemDetail.dbfoodstatusID 
				= foodstatus.dbfoodstatusID AND menuitemDetail.dbmenuitemID 
				= menuitem.dbmenuitemID AND menuitem.dbmenuitemID = :bvmenuitem';
			$result4 = $db->prepare($sqlselect4);
			$result4->bindValue(':bvmenuitem', $maxid);
			$result4->execute();
			$statuscount = $result4->rowCount();
			
$sqlselect5 = 'SELECT menuitemDetail.*, inventory.* FROM inventory, 
				menuitem, menuitemDetail WHERE inventory.dbmenuitemDetailID 
				= menuitemDetail.dbmenuitemDetailID AND menuitemDetail.dbmenuitemID 
				= menuitem.dbmenuitemID AND menuitem.dbmenuitemID = :bvmenuitem';
			$result5 = $db->prepare($sqlselect5);
			$result5->bindValue(':bvmenuitem', $maxid);
			$result5->execute();
			$invcount = $result5->rowCount();
			
$sqlselect6 = 'SELECT menuitemDetail.*, inventory.* FROM inventory, 
				menuitem, menuitemDetail WHERE inventory.dbmenuitemDetailID 
				= menuitemDetail.dbmenuitemDetailID AND menuitemDetail.dbmenuitemID 
				= menuitem.dbmenuitemID AND menuitem.dbmenuitemID = :bvmenuitem';
			$result6 = $db->prepare($sqlselect6);
			$result6->bindValue(':bvmenuitem', $maxid);
			$result6->execute();
			$pricecount = $result6->rowCount();
			
?>
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Menu Item Information</h3>
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
											<div class="col-12 pt-2">
											 <div class="form-group">
												<label for="catid" class ="text-light" style="font-family: 'Raleway', sans-serif;">Food Category<span class="text-light">*</span></label>
												<select class="form-select" id="catid" name="catid">
												<option value="">SELECT CATEGORY</option>
												<?php while ($rowc = $resultc->fetch() )
												{
												if ($rowc['dbcategoryID'] == $formfield['ffcatid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowc['dbcategoryID'] . '"' . $checker . '>' . 
												$rowc['dbcategoryName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12 pt-2">
                                                <label for = "itemname" class ="text-light" style="font-family: 'Raleway', sans-serif;">Menu Item Name<span class="text-light">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"></div>
                                                    <input type="text" name ="itemname" id="itemname" class="form-control" 
														value="<?php echo $row['dbmenuitemName']; ?>">
                                                </div>
                                            </div>
											
											<div class="col-12 pt-2">
                                               <button type="submit" name="thenewsubmit" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">ADD NEW ITEM</button>
                                            </div>
									<!--		<div class="col-sm-3 pt-2">
                                               <button type="reset" name="theclear" style = "background-color: #D3D3D2;"
												class="btn px-4 float-end mt-4">CLEAR</button>
                                            </div>		-->	
                                </div>
                            </div>
							
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="right h-100 py-5 px-5 border border-dark border-2 rounded-3 h-100 bg-white text-dark text-left pt-5">
										<div class="col-12 pb-3">
                                                <label for = "itembutton" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Button Name<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="text" name ="itembutton" id="itembutton" class="form-control border-dark"
													value="<?php echo $row['dbmenuitemButton']; ?>" >
                                                </div>
                                            </div>
			<!---Note! textarea label tags need to be on the same line in order for placeholder attribute to work!---->								
										<div class="col-12 pb-3">	
											<div class="form-group">
											<label for="itemdescr" class = "text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Description</label>
											<textarea style = "resize: none;" class="form-control border-dark" rows="3" cols="25" name = "itemdescr" id="itemdescr"><?php echo $row['dbmenuitemDescript']; ?></textarea>
											</div>
										</div>
								            <div class="col-12 pb-4">
                                                <label for = "itemprice" class ="text-dark" style="font-family: 'Raleway', sans-serif;">Menu Item Price<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="number" min="0.00" max="100" step="0.01" name ="itemprice" id="itemprice" pattern = "^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$"
														class="form-control border-dark" value="<?php echo $row['dbmenuitemDetailPrice']; ?>">
                                                </div>
                                            </div>
											<div class="col-12 pb-4">
                                                <label for = "itemcost"class ="text-dark" style="font-family: 'Raleway', sans-serif;">Inventory Unit Cost<span class="text-dark">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-text border-dark bg-dark"></div>
                                                    <input type="number" min="0.00" max="100.00" step="0.01" name ="itemcost" id="itemcost" pattern = "^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$"
														class="form-control border-dark" value="<?php echo $row['dbmenuitemDetailCost']; ?>">
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
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Description</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Menu Button Name</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Menu Price</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Inventory Date</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Inventory Unit Cost</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Item Status</th>
	</tr>
</thead>
<tbody class = "border border-dark">

<?php
	
	echo '<tr><td>' . $row['dbmenuitemTypeName'] . '</td><td>' . $row['dbcategoryName'] . '</td><td>' . $row['dbmenuitemName'] . 
		'</td><td>' . $row['dbmenuitemDescript'] . '</td><td>' . $row['dbmenuitemButton'] . '</td><td>$' . $row['dbmenuitemDetailPrice'] . 
		'</td><td>' . $row['dbinventoryDate'] . '</td><td>$' . $row['dbmenuitemDetailCost'] . '</td><td>' . $row['dbfoodstatusName'] . '</td></tr>';
?>
</tbody>
</table>
</div>
<?php
}#1-3

//////////////////////////////////////////////////////////////////////////
// Indicates remaining staff permission levels unauthorized to add menu item
else if  (($showform == 1) || ($showform == 2) || ($showform == 3) || 
		($showform == 6) || ($showform == 7) || ($showform == 8)) {
	echo '<div class = "container ps-5">';
	echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
	echo "You do not have authorization to access this page!" . '</div></div>';
}
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