<?php
$pagetitle = "Welcome : Back End Site";
require_once 'boot_header.php';
require_once 'connect.php';
if ((isset($_SESSION['empid'])) && (isset($_SESSION['empjobid']))
	&& (($_SESSION['empstatusid'])== 1))
{	$name = $_POST['empid'];
echo $name;



	include_once 'boot_menu.php';
    include_once 'boot_footer.php';
    exit();
} 
else {
//If the user is not logged in, the login form will display.
$showform = 1;
}
//Here we set our errormsg variable to an empty string
$errormsg = '';
//If the submit button on our login form is pressed, we proceed with gathering
//the data entered in the formfields below. We check and cleanse our data.
if(isset ($_POST['submit'])) {
	//Cleanse our data from our formfields
    $formfield['ffempusername'] = strtolower(htmlspecialchars(stripslashes(trim($_POST['emp_username']))));
    $formfield['ffemppassword'] = trim($_POST['emp_password']);
	
	//If either input field is empty, we set our message variable accordingly
    if(empty($formfield['ffempusername'])) { $errormsg .= '<p>USERNAME IS MISSING</p>';}
    if(empty($formfield['ffemppassword'])) { $errormsg .= '<p>PASSWORD IS MISSING</p>';}
	//The appropriate message is displayed if the variable is not empty
    if($errormsg != '') {
        echo '<p><span id = "display_message">THERE ARE ERRORS!</span></p>' . $errormsg;
    }
	//If there are no error messages, we try to gather our records from the DB
    else
    {
        try
        {
			//We begin our sql query
			$sql = 'SELECT employee.*, job.*, status.*, state.*, location.*
						FROM employee, status, job, jobDetail, location, 
						locationDetail, state WHERE jobDetail.dbjobID = job.dbjobID 
						AND jobDetail.dbemployeeID = employee.dbemployeeID AND 
						employee.dbstatusID = status.dbstatusID AND employee.dbstateID =
						state.dbstateID AND locationDetail.dblocationID = location.dblocationID 
						AND locationDetail.dbemployeeID = employee.dbemployeeID AND employee.dbstatusID
						= :bvstatusID AND dbemployeeUsername = :bvempusername';
			
            $s = $db->prepare($sql);
            $s->bindValue(':bvempusername', $formfield['ffempusername']);
			$s->bindValue(':bvstatusID', 1);
            $s->execute();
            $count = $s->rowCount();
        }
        catch (PDOException $e)
        {
            echo "ERROR!!!" . $e->getMessage();
            exit();
        }
		//If no matching record is found
        if($count < 1)
        {
            echo '<p><span id = "display_message">The username or password is incorrect.</span></p>';
        }
        else
			//Otherwise, if a matching record is found, we set our variables
        {
            $row = $s->fetch();
            $confirmeduname = $row['dbemployeeUsername'];
            $confirmedpw = $row['dbemployeePassword'];
			$confirmedempfirstname = $row['dbemployeeFirstName'];
			$confirmedemplastname = $row['dbemployeeLastName'];

			//$confirmedemplocation = $row['dblocationname'];
			
			//echo '<p>Logging in for user: </p>' . $confirmedempfirstname . ' ' . $confirmedemplastname;

            /* Remember I will fix this and verify the password utilizing this method, once I get the insert
			file addressed. Until then, we will simply pull what's in the database and compare it to what the
			user has entered in the formfield, without hashing--------temporary! */
			
         //   if (password_verify($formfield['ffemppassword'], $confirmedpw))
			 
		//	Notice, here we are just comparing entries, without the proper password validation
			if ($formfield['ffemppassword'] == $confirmedpw)
            {
                //Creates session variables for our user
				$_SESSION['empid'] = $row['dbemployeeID']; 
				$_SESSION['empfirstname'] = $row['dbemployeeFirstName'];
				$_SESSION['emplastname'] = $row['dbemployeeLastName'];
				$_SESSION['empstatusid'] = $row['dbstatusID'];
				
			//Here I am gathering the first job that is associated with the user
				//currently logged in. Essentially, this will indicate the main job for
				//the employee when logged in, thus setting the permission levels.
			$sqlmin = "SELECT MIN(jobDetail.dbjobDetailID) AS minid FROM employee, 
					job, jobDetail WHERE jobDetail.dbjobID = job.dbjobID AND 
					jobDetail.dbemployeeID = employee.dbemployeeID AND 
					employee.dbemployeeID = :bvempid";
				$resultmin = $db->prepare($sqlmin);
				$resultmin->bindValue(':bvempid', $_SESSION['empid']);
				$resultmin->execute();
				$rowmin = $resultmin->fetch();
				$min = $rowmin["minid"];
				
				//This takes that minimum entry associated with the employee logged in,
				//thus allowing permission levels to be set. Depending on which job the 
				//user is logged in as will determine what the user can view and do.
				
			$sqlselectjob = 'SELECT job.*, employee.* FROM employee, job, jobDetail
					WHERE jobDetail.dbjobID = job.dbjobID AND jobDetail.dbemployeeID = 
					employee.dbemployeeID AND employee.dbemployeeID = :bvempid 
					AND jobDetail.dbjobDetailID = :bvminid';
				$selectjob = $db->prepare($sqlselectjob);
				$selectjob->bindValue(':bvempid', $_SESSION['empid']);
				$selectjob->bindValue(':bvminid', $min);
				$selectjob->execute();
				$selectrow = $selectjob->fetch();
				
				//Here I am gathering the first location that is associated with the user
				//currently logged in. Essentially, this will indicate the home store for
				//the employee when logged in, assigning their credentials to that location.
				$sqlminloc = "SELECT MIN(locationDetail.dblocationDetailID) AS minlocid 
					FROM employee, location, locationDetail WHERE locationDetail.dblocationID = 
					location.dblocationID AND locationDetail.dbemployeeID = employee.dbemployeeID 
					AND employee.dbemployeeID = :bvempid";
				$resultlocmin = $db->prepare($sqlminloc);
				$resultlocmin->bindValue(':bvempid', $_SESSION['empid']);
				$resultlocmin->execute();
				$rowlocmin = $resultlocmin->fetch();
				$minloc = $rowlocmin["minlocid"];
				
				$sqlselectlocation = 'SELECT location.*, employee.* FROM employee, location, locationDetail
					WHERE locationDetail.dblocationID = location.dblocationID AND locationDetail.dbemployeeID = 
					employee.dbemployeeID AND employee.dbemployeeID = :bvempid 
					AND locationDetail.dblocationDetailID = :bvminid';
				$selectlocation = $db->prepare($sqlselectlocation);
				$selectlocation->bindValue(':bvempid', $_SESSION['empid']);
				$selectlocation->bindValue(':bvminid', $minloc);
				$selectlocation->execute();
				$selectlocrow = $selectlocation->fetch();
				
				//This takes that minimum entry associated with the user logged in,
				//thus allowing permission levels to be set at that location.
				
				$_SESSION['empmainjob'] = $selectrow['dbjobName'];
				$_SESSION['emppermitid'] = $selectrow['dbpermissionID'];
				$_SESSION['empjobid'] = $selectrow['dbjobID'];
				$_SESSION['emplocationname'] = $selectlocrow['dblocationName'];
				$confirmedempfirstname = $selectrow['dbemployeeFirstName'];
				$confirmedemplastname = $selectrow['dbemployeeLastName'];
				
				
				
		// Notice here that if the login credentials are successful for our back end site,
		// the session is created so according to the code up top, you see that if the session
		// is created, the menu page will display, this is why we call the backindex page again.
				echo '<p>Logging in for user: </p>' . $confirmedempfirstname . ' ' . $confirmedemplastname;
				echo "<script>location='boot_backindex.php'</script>";
				// The $showform variable is changed so to hide our login form
                $showform = 0;
            }
            else
            {
                echo '<p><span id = "display_message">The employee name or password is incorrect.</span></p>';
            }
        }
    }
}
if($showform == 1)
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
include_once 'boot_footer.php';
?>