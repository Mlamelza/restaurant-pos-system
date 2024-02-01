<?php
// If employee is currently logged in
if((isset($_SESSION['empid'])) && (isset($_SESSION['empjobid']))) {

/*When the menu page loads, we first check if the session has been created, meaning if the user
is currently logged in. If so, we then check what permission level the current user has. If the 
user is an Administrator or Regional Manager, we further divide privileges here.**/ 
    if ($_SESSION['emppermitid'] == 7)
    {
		//Administrative privileges
		if ($_SESSION['empjobid'] == 1) {
echo '<nav class="navbar navbar-expand-lg bg-dark" style = "min-height: 50px;">
        <div class="container-fluid">
            <div id="navbarCollapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-2 text-white" data-bs-toggle="dropdown">Customers</a>
                        <div class="dropdown-menu">
                            <a href="boot_insertcustomer.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Customer</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selectcustomer.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Customer</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Orders</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Order</a>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search Order</a>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Make Order Page</a>
							<a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Ready Order Page</a>
							<a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Open Order Page</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Edit Order</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Menu Items</a>
                        <div class="dropdown-menu">
                            <a href="boot_insertmenuitem.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Regular Menu Item</a>
							<a href="boot_insertspecialtyitem.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Specialty Item</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selectmenuitem.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Regular Menu Item</a>
							<a href="boot_selectspecialtyitem.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Specialty Item</a>
                        </div>
                    </li>
					
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Food Categories</a>
                        <div class="dropdown-menu">
                            <a href="boot_insertcategory.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Category</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selectcategory.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Food Category</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Inventory</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Inventory</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Inventory</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Employees</a>
                        <div class="dropdown-menu">
                            <a href="boot_insertemployee.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Employee</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selectemployee.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Employee</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Permissions</a>
                        <div class="dropdown-menu">
                            <a href="boot_insertpermission.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Permission</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selectpermission.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Permission</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Job Titles</a>
                        <div class="dropdown-menu">
                            <a href="boot_insertjobtitle.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Job Title</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selectjobtitle.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Job Title</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Tables</a>
                        <div class="dropdown-menu">
                            <a href="boot_inserttable.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Table Number</a>
							<a href="boot_inserttablesection.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Table Section</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selecttable.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Table Number</a>
							<a href="boot_selecttablesection.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Table Section</a>
                        </div>
                    </li>
					
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Locations</a>
                        <div class="dropdown-menu">
                            <a href="boot_insertlocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Location</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selectlocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Location</a>
                        </div>
                    </li>
                </ul>
                <ul class="nav navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link pe-3 text-white" data-bs-toggle="dropdown">Admin</a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="changelocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Change Home Location</a>
                            <a href="account_manager.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Manage Account</a>
                            <div class="dropdown-divider"></div>
							<a href="boot_viewchart.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">View Seating Chart</a>
							<a href="boot_viewmenu.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">View Menu Chart</a>
                            <a href="boot_logout.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>';
			
		}//ends Admin privileges
		
		//Regional Manager privileges
		else if ($_SESSION['empjobid'] == 18) {
echo '<nav class="navbar navbar-expand-lg bg-dark">
        <div class="container-fluid">
            <div id="navbarCollapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-2 text-white" data-bs-toggle="dropdown">Customers</a>
                        <div class="dropdown-menu">
                            <a href="boot_insertcustomer.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Customer</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selectcustomer.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Customer</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Orders</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Order</a>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search Order</a>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Make Order Page</a>
							<a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Ready Order Page</a>
							<a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Open Order Page</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Edit Order</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Menu Items</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectmenuitem.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Menu Item</a>
                        </div>
                    </li>
					
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Food Categories</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectcategory.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Category</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Inventory</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Inventory</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Inventory</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Employees</a>
                        <div class="dropdown-menu">
                            <a href="boot_insertemployee.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Employee</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selectemployee.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Employee</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Permissions</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectpermission.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Permission</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Job Titles</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectjobtitle.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Job Title</a>                            
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Tables</a>
                        <div class="dropdown-menu">
                            <a href="boot_selecttable.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Table Number</a>
							<div class="dropdown-divider"></div>
							<a href="boot_selecttablesection.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Table Section</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Locations</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectlocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Location</a>                            
                        </div>
                    </li>
                </ul>
                <ul class="nav navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link pe-3 text-white" data-bs-toggle="dropdown">Admin</a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="changelocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Change Home Location</a>
                            <a href="account_manager.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Manage Account</a>
                            <div class="dropdown-divider"></div>
							<a href="boot_viewchart.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">View Seating Chart</a>
                            <a href="boot_logout.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>';
		}// ends Regional Manager privileges
		
    } // ends our isset for permission value of 7 (Admin & Regional)
	
	// Here we check if the permission level is set to GM or another Managerial status type
	else if (($_SESSION['emppermitid'] == 1) || ($_SESSION['emppermitid'] == 9)) 
	{
		echo '<nav class="navbar navbar-expand-lg bg-dark">
        <div class="container-fluid">
            <div id="navbarCollapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-2 text-white" data-bs-toggle="dropdown">Customers</a>
                        <div class="dropdown-menu">
                            <a href="boot_insertcustomer.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Customer</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selectcustomer.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Customer</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Orders</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Order</a>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search Order</a>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Make Order Page</a>
							<a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Ready Order Page</a>
							<a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Open Order Page</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Edit Order</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Menu Items</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectmenuitem.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Menu Item</a>					
                        </div>
                    </li>
					
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Food Categories</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectcategory.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Category</a>                           
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Inventory</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Inventory</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Inventory</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Employees</a>
                        <div class="dropdown-menu">
                            <a href="boot_insertemployee.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Employee</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selectemployee.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Employee</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Job Titles</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectjobtitle.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Job Title</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Tables</a>
                        <div class="dropdown-menu">
                            <a href="boot_selecttable.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Table Number</a>
							<div class="dropdown-divider"></div>
							<a href="boot_selecttablesection.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Table Section</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Locations</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectlocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Location</a>                        
                        </div>
                    </li>
                </ul>
                <ul class="nav navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link pe-3 text-white" data-bs-toggle="dropdown">Admin</a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="changelocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Change Home Location</a>
                            <a href="account_manager.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Manage Account</a>
                            <div class="dropdown-divider"></div>
							<a href="boot_viewchart.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">View Seating Chart</a>
                            <a href="boot_logout.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>';
	}// ends our isset for permission value of 1 or 9 (GM and other Managerial Titles)
	
	// Now we set the links for the permission level of 2 (Kitchen Manager)
	else if ($_SESSION['emppermitid'] == 2)
	{
		echo '<nav class="navbar navbar-expand-lg bg-dark">
        <div class="container-fluid">
            <div id="navbarCollapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-2 text-white" data-bs-toggle="dropdown">Customers</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectcustomer.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Customer</a>                            
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Orders</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Order</a>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search Order</a>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Make Order Page</a>
							<a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Ready Order Page</a>
							<a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Open Order Page</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Edit Order</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Menu Items</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectmenuitem.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Menu Item</a>					
                        </div>
                    </li>
					
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Food Categories</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectcategory.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Category</a>                           
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Inventory</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Inventory</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Inventory</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Employees</a>
                        <div class="dropdown-menu">
                            <a href="boot_insertemployee.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Employee</a>
                            <div class="dropdown-divider"></div>
                            <a href="boot_selectemployee.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | Edit Employee</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Job Titles</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectjobtitle.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Job Title</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Tables</a>
                        <div class="dropdown-menu">
                            <a href="boot_selecttable.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Table Number</a>
							<div class="dropdown-divider"></div>
							<a href="boot_selecttablesection.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Table Section</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Locations</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectlocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Location</a>                        
                        </div>
                    </li>
                </ul>
                <ul class="nav navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link pe-3 text-white" data-bs-toggle="dropdown">Admin</a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="changelocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Change Home Location</a>
                            <a href="account_manager.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Manage Account</a>
                            <div class="dropdown-divider"></div>
							<a href="boot_viewchart.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">View Seating Chart</a>
                            <a href="boot_logout.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>';
	} // ends our isset for permitid of 2 (Kitchen Manager)
	
	// Now we check and see if our permission level is set to 4, 5, 6, 10, or 11 (bar, waitstaff, host, togo, merch)
    else if (($_SESSION['emppermitid'] == 4) || ($_SESSION['emppermitid'] == 5) || ($_SESSION['emppermitid'] == 6)
			|| ($_SESSION['emppermitid'] == 10) || ($_SESSION['emppermitid'] == 11)) 
	{
		echo '<nav class="navbar navbar-expand-lg bg-dark">
        <div class="container-fluid">
            <div id="navbarCollapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-2 text-white" data-bs-toggle="dropdown">Customers</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectcustomer.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Customer</a>                          
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Orders</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Order</a>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search Order</a>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Make Order Page</a>
							<a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Ready Order Page</a>
							<a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Open Order Page</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Edit Order</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Menu Items</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectmenuitem.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Menu Item</a>					
                        </div>
                    </li>
					
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Food Categories</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectcategory.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Category</a>                           
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Inventory</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Inventory</a>                    
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Job Titles</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectjobtitle.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Job Title</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Tables</a>
                        <div class="dropdown-menu">
                            <a href="boot_selecttable.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Table Number</a>
							<div class="dropdown-divider"></div>
							<a href="boot_selecttablesection.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Table Section</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Locations</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectlocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Location</a>                        
                        </div>
                    </li>
                </ul>
                <ul class="nav navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link pe-3 text-white" data-bs-toggle="dropdown">Admin</a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="changelocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Change Home Location</a>
                            <a href="account_manager.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Manage Account</a>
                            <div class="dropdown-divider"></div>
							<a href="boot_viewchart.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">View Seating Chart</a>
                            <a href="boot_logout.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>';
	} // ends our isset for permitid of 4, 5, 6, 10, or 11

  // Now we check and see if our permission level is set to 3 (HOH staff including line cooks, prep, and expo)
  else if ($_SESSION['emppermitid'] == 3) 
	{
        echo '<nav class="navbar navbar-expand-lg bg-dark">
        <div class="container-fluid">
            <div id="navbarCollapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Orders</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Add Order</a>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search Order</a>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Make Order Page</a>
							<a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Ready Order Page</a>
							<a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Open Order Page</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Edit Order</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Menu Items</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectmenuitem.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Menu Item</a>					
                        </div>
                    </li>
					
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Food Categories</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectcategory.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Category</a>                           
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Inventory</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Inventory</a>                    
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Job Titles</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectjobtitle.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Job Title</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Tables</a>
                        <div class="dropdown-menu">
                            <a href="boot_selecttable.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Table Number</a>
							<div class="dropdown-divider"></div>
							<a href="boot_selecttablesection.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Table Section</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Locations</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectlocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Location</a>                        
                        </div>
                    </li>
                </ul>
                <ul class="nav navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link pe-3 text-white" data-bs-toggle="dropdown">Admin</a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="changelocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Change Home Location</a>
                            <a href="account_manager.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Manage Account</a>
                            <div class="dropdown-divider"></div>
							<a href="boot_viewchart.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">View Seating Chart</a>
                            <a href="boot_logout.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>';
	} // ends our isset for permitid of 3 (HOH staff)

// Now we check and see if our permission level is set to 8 (support staff including busser/SA, dishwasher)
  else if ($_SESSION['emppermitid'] == 8) 
	{
		echo '<nav class="navbar navbar-expand-lg bg-dark">
        <div class="container-fluid">
            <div id="navbarCollapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Menu Items</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectmenuitem.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Menu Item</a>					
                        </div>
                    </li>
					
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Food Categories</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectcategory.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Category</a>                           
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Inventory</a>
                        <div class="dropdown-menu">
                            <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Inventory</a>                    
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Job Titles</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectjobtitle.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Job Title</a>
                        </div>
                    </li>
					<li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Tables</a>
                        <div class="dropdown-menu">
                            <a href="boot_selecttable.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Table Number</a>
							<div class="dropdown-divider"></div>
							<a href="boot_selecttablesection.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Table Section</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link ps-3 text-white" data-bs-toggle="dropdown">Locations</a>
                        <div class="dropdown-menu">
                            <a href="boot_selectlocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Search | View Location</a>                        
                        </div>
                    </li>
                </ul>
                <ul class="nav navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a href="#" style = "font-family: Ysabeau SC, sans-serif;" class="nav-link pe-3 text-white" data-bs-toggle="dropdown">Admin</a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="changelocation.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Change Home Location</a>
                            <a href="account_manager.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Manage Account</a>
                            <div class="dropdown-divider"></div>
							<a href="boot_viewchart.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">View Seating Chart</a>
                            <a href="boot_logout.php" style = "font-family: Ysabeau SC, sans-serif;" class="dropdown-item">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>';
	} // ends our isset for permitid of 3 (HOH staff)

    $visible = 1;	
} // ends our isset for our employee id session
else
{
    // echo "<a href='login.php'>Log In</a>";
    $visible = 0;
}
?>
