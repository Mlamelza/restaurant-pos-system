<?php
$pagetitle = 'Select Table';
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


/**********************************************************************************************************/
//For our status drop down menu below
$sqlselects = "SELECT * from status";
$results = $db->prepare($sqlselects);
$results->execute();

//For our table section drop down menu
$sqlselectsec = "SELECT * from tableSection";
$resultsec = $db->prepare($sqlselectsec);
$resultsec->execute();

/**Notice here that if the clear button is pressed, the form type 
that is displayed will become the $formtype of 1. The same applies 
if the new submit button is pressed. This means that the user would
essentially start over and select a table record from the DB.
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
	$formfield['ffsectionid'] = trim($_POST['sectionid']);
	$formfield['ffstatusid'] = trim($_POST['statusid']);
	
	//check for all empty fields
	if ((empty($formfield['ffsectionid'])) && (empty($formfield['ffstatusid']))) {
					
				$errormsg .= "You must choose at least one search method! ";
				}
	
	if ($formfield['ffsectionid'] !='') {
		$stringclause .= " AND tableSection.dbtableSectionID = :bvsectionid";
	}
	
	if ($formfield['ffstatusid'] !='') {
		$stringclause .= " AND status.dbstatusID = :bvstatusID";
	}
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
	
	//select query due to our search criteria
	$sqlselect = "SELECT DISTINCT tableNum.*, status.*, tableSection.*
		FROM tableNum, status, tableSection, tableNumDetail 
		WHERE tableNum.dbtableSectionID = tableSection.dbtableSectionID AND 
		tableNumDetail.dbtableNumID = tableNum.dbtableNumID AND 
		tableNumDetail.dbstatusID = status.dbstatusID" . $stringclause .
		" ORDER BY tableNum.dbtableNumID" ;
			
	// We prepare and bind our values so we can gather the appropriate DB records
	$result = $db->prepare($sqlselect);

	if ($formfield['ffstatusid'] !='') {
		$result->bindValue(':bvstatusID', $formfield['ffstatusid']);
		}
		
	if ($formfield['ffsectionid'] !='') {
		$result->bindValue(':bvsectionid', $formfield['ffsectionid']);
		}
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
			$formtype = 2;
		} else if ($count >=1)
		{
			echo '<div class = "container ps-5">';
			echo '<div class="message alert alert-info fs-5 border-dark" role="alert">';
			echo "Results found." . '</div></div>';
			$formtype = 3;
		}
	}
} // ends if isset
	
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
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Table Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                        
											<div class="col-12">
											 <div class="form-group">
												<label for="sectionid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Table Section</label>
												<select class="form-select" id="sectionid" name="sectionid">
												<option value="">SELECT TABLE SECTION</option>
												<?php while ($rowsec = $resultsec->fetch() )
												{
												if ($rowsec['dbtableSectionID'] == $formfield['ffsectionid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowsec['dbtableSectionID'] . '"' . $checker . '>' . 
												$rowsec['dbtableSectionName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12">
											 <div class="form-group">
												<label for="statusid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Table Status</label>
												<select class="form-select" id="statusid" name="statusid">
												<option value="">SELECT STATUS</option>
												<?php while ($rows = $results->fetch() )
												{
												if ($rows['dbstatusID'] == $formfield['ffstatusid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rows['dbstatusID'] . '"' . $checker . '>' . 
												$rows['dbstatusName'] . '</option>';
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
                                    			<img id="tablelogo" alt="Table Logo" class = "pt-4" style="height: 255px; vertical-align: middle;" src="./images/fork2.svg">

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
//Form displayed when the submit button was pressed but an error has occurred
else if (($showform == 5) && ($formtype == 2))
{	
?>
<!--default formtype 2 when error message is displayed---->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Table Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                        
											<div class="col-12">
											 <div class="form-group">
												<label for="sectionid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Table Section</label>
												<select class="form-select" id="sectionid" name="sectionid">
												<option value="">SELECT TABLE SECTION</option>
												<?php while ($rowsec = $resultsec->fetch() )
												{
												if ($rowsec['dbtableSectionID'] == $formfield['ffsectionid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowsec['dbtableSectionID'] . '"' . $checker . '>' . 
												$rowsec['dbtableSectionName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12">
											 <div class="form-group">
												<label for="statusid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Table Status</label>
												<select class="form-select" id="statusid" name="statusid">
												<option value="">SELECT STATUS</option>
												<?php while ($rows = $results->fetch() )
												{
												if ($rows['dbstatusID'] == $formfield['ffstatusid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rows['dbstatusID'] . '"' . $checker . '>' . 
												$rows['dbstatusName'] . '</option>';
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
                                    			<img id="tablelogo" alt="Table Logo" class = "pt-4" style="height: 255px; vertical-align: middle;" src="./images/fork2.svg">

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
//Form displayed when the submit button was pressed but an error has occurred
else if (($showform == 5) && ($formtype == 3))
{	
?>
<!--default formtype 3 when a record has been pulled---->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Table Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                        
											<div class="col-12">
											 <div class="form-group">
												<label for="sectionid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Table Section</label>
												<select class="form-select" id="sectionid" name="sectionid">
												<option value="">SELECT TABLE SECTION</option>
												<?php while ($rowsec = $resultsec->fetch() )
												{
												if ($rowsec['dbtableSectionID'] == $formfield['ffsectionid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowsec['dbtableSectionID'] . '"' . $checker . '>' . 
												$rowsec['dbtableSectionName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12">
											 <div class="form-group">
												<label for="statusid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Table Status</label>
												<select class="form-select" id="statusid" name="statusid">
												<option value="">SELECT STATUS</option>
												<?php while ($rows = $results->fetch() )
												{
												if ($rows['dbstatusID'] == $formfield['ffstatusid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rows['dbstatusID'] . '"' . $checker . '>' . 
												$rows['dbstatusName'] . '</option>';
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
                                    			<img id="tablelogo" alt="Table Logo" class = "pt-4" style="height: 255px; vertical-align: middle;" src="./images/fork2.svg">

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
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Table Number</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Table Section</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Table Section Abbreviation</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Table Status</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">&nbsp;</th>
	</tr>
</thead>
<tbody class = "border border-dark">	
	<?php 
	// We display our table data based on our SQL query above
	while ($row = $result-> fetch() )
		{
				echo '<tr><td>' . $row['dbtableName'] . '</td><td>' . $row['dbtableSectionName'] . '</td><td>' .
				$row['dbtableSectionAbrev'] . '</td><td>' . $row['dbstatusName'] . '</td><td>' .
				//This will make a button that sends the user to another page "updatetable.php"
				'<form action="boot_updatetable.php" method="post">
					<input type="hidden" name="tableid" value="'. $row['dbtableNumID'] .'">
					<input type="submit" class = "rounded-3 bg-dark text-light" name="theedit" value="EDIT">
				</form>' . '</td></tr>';
				
			}
		?>
</tbody>
</table>
</div>
<?php
}#1-3
// Remaining permissible users with default formtype 1
else if ((($showform == 1) || ($showform == 2) || ($showform == 3) ||
	($showform == 6) || ($showform == 7) || ($showform == 8)) && ($formtype == 1))
{	
?>
<!--default formtype 1---->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Table Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                        
											<div class="col-12">
											 <div class="form-group">
												<label for="sectionid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Table Section</label>
												<select class="form-select" id="sectionid" name="sectionid">
												<option value="">SELECT TABLE SECTION</option>
												<?php while ($rowsec = $resultsec->fetch() )
												{
												if ($rowsec['dbtableSectionID'] == $formfield['ffsectionid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowsec['dbtableSectionID'] . '"' . $checker . '>' . 
												$rowsec['dbtableSectionName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12">
											 <div class="form-group">
												<label for="statusid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Table Status</label>
												<select class="form-select" id="statusid" name="statusid">
												<option value="">SELECT STATUS</option>
												<?php while ($rows = $results->fetch() )
												{
												if ($rows['dbstatusID'] == $formfield['ffstatusid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rows['dbstatusID'] . '"' . $checker . '>' . 
												$rows['dbstatusName'] . '</option>';
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
                                    			<img id="tablelogo" alt="Table Logo" class = "pt-4" style="height: 255px; vertical-align: middle;" src="./images/fork2.svg">

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
// Remaining permissible users when submission is made but error message is displayed
else if ((($showform == 1) || ($showform == 2) || ($showform == 3) ||
	($showform == 6) || ($showform == 7) || ($showform == 8)) && ($formtype == 2))
{	
?>
<!--default formtype 2 when error message is displayed---->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Table Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                        
											<div class="col-12">
											 <div class="form-group">
												<label for="sectionid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Table Section</label>
												<select class="form-select" id="sectionid" name="sectionid">
												<option value="">SELECT TABLE SECTION</option>
												<?php while ($rowsec = $resultsec->fetch() )
												{
												if ($rowsec['dbtableSectionID'] == $formfield['ffsectionid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowsec['dbtableSectionID'] . '"' . $checker . '>' . 
												$rowsec['dbtableSectionName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12">
											 <div class="form-group">
												<label for="statusid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Table Status</label>
												<select class="form-select" id="statusid" name="statusid">
												<option value="">SELECT STATUS</option>
												<?php while ($rows = $results->fetch() )
												{
												if ($rows['dbstatusID'] == $formfield['ffstatusid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rows['dbstatusID'] . '"' . $checker . '>' . 
												$rows['dbstatusName'] . '</option>';
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
                                    			<img id="tablelogo" alt="Table Logo" class = "pt-4" style="height: 255px; vertical-align: middle;" src="./images/fork2.svg">

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
// Remaining permissible users when submission is made and records are pulled
else if ((($showform == 1) || ($showform == 2) || ($showform == 3) ||
	($showform == 6) || ($showform == 7) || ($showform == 8)) && ($formtype == 3))
{	
?>
<!--default formtype 3 when a record has been pulled---->
<div class="d-flex flex-column min-vh-100 justify-content-left align-items-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                  <h3 class="mb-3 pt-3" style = "font-family: 'Ysabeau SC', sans-serif;">Table Information</h3>
                    <div class="bg-dark shadow rounded-3">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="left h-100 py-5 px-5">
                        <form name = "theform" id="theform" method= "post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-4">
                                        
											<div class="col-12">
											 <div class="form-group">
												<label for="sectionid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Table Section</label>
												<select class="form-select" id="sectionid" name="sectionid">
												<option value="">SELECT TABLE SECTION</option>
												<?php while ($rowsec = $resultsec->fetch() )
												{
												if ($rowsec['dbtableSectionID'] == $formfield['ffsectionid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rowsec['dbtableSectionID'] . '"' . $checker . '>' . 
												$rowsec['dbtableSectionName'] . '</option>';
												}
												?>
												</select>
											 </div>
											</div>
											<div class="col-12">
											 <div class="form-group">
												<label for="statusid" class ="text-light pb-2" style="font-family: 'Raleway', sans-serif;">Table Status</label>
												<select class="form-select" id="statusid" name="statusid">
												<option value="">SELECT STATUS</option>
												<?php while ($rows = $results->fetch() )
												{
												if ($rows['dbstatusID'] == $formfield['ffstatusid'])
												{$checker = 'selected';}
												else {$checker = '';}
												echo '<option value="'. $rows['dbstatusID'] . '"' . $checker . '>' . 
												$rows['dbstatusName'] . '</option>';
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
                                    			<img id="tablelogo" alt="Table Logo" class = "pt-4" style="height: 255px; vertical-align: middle;" src="./images/fork2.svg">

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
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Table Number</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Table Section</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Table Section Abbreviation</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">Table Status</th>
	<th scope="col" style = "font-family: 'Raleway', sans-serif;">&nbsp;</th>
	</tr>
</thead>
<tbody class = "border border-dark">	
	<?php 
	// We display our table data based on our SQL query above
	while ($row = $result-> fetch() )
		{
				echo '<tr><td>' . $row['dbtableName'] . '</td><td>' . $row['dbtableSectionName'] . '</td><td>' .
				$row['dbtableSectionAbrev'] . '</td><td>' . $row['dbstatusName'] . '</td><td>' .
				//This will make a button that sends the user to another page "updatetable.php"
				'<form action="boot_updatetable.php" method="post">
					<input type="hidden" name="tableid" value="'. $row['dbtableNumID'] .'">
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