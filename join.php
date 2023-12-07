

<?php
// The preceding tag tells the web server to parse the following text as PHP
// rather than HTML (the default)

// The following 3 lines allow PHP errors to be displayed along with the page
// content. Delete or comment out this block when it's no longer needed.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set some parameters

// Database access configuration
$config["dbuser"] = "u";			// change "cwl" to your own CWL
$config["dbpassword"] = "p";	// change to 'a' + your student number
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$db_conn = NULL;	// login credentials are used in connectToDB()

$success = true;	// keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

// The next tag tells the web server to stop parsing the text as PHP. Use the
// pair of tags wherever the content switches to PHP
?>

<html>

<head>
	<title>Join</title>
</head>

<body>
    <h2>Menu</h2>
    <a href="./home-page.php">Home Page</a>

    <hr />

    <h2>Join Values from PlayFor and Team</h2>
    <form method="GET" action="join.php">
        <input type="hidden" id="displayTeamTuplesRequest" name="displayTeamTuplesRequest">
        <input type="submit" value="Display Teams" name="displayTeamTuples"></p>
    </form>
    <form method="GET" action="join.php">
        <input type="hidden" id="joinRequest" name="joinRequest">
        Team Name: <input type="text" name="teamName" onkeyup="this.value=this.value.replace(/[^a-z0-9A-Z ]/g,'');"><br><br>
        <input type="submit" value="Get" name="join">
    </form>

    <hr />


    <!-- <h2>Display Tuples in All Tables (Debug use only)</h2>
    <form method="GET" action="oracle-template.php">
        <input type="hidden" id="displayTuplesRequest" name="displayTuplesRequest">
        <input type="submit" value="Submit" name="displayTuples"></p>
    </form> -->


	<?php
	// The following code will be parsed as PHP

	function debugAlertMessage($message)
	{
		global $show_debug_alert_messages;

		if ($show_debug_alert_messages) {
			echo "<script type='text/javascript'>alert('" . $message . "');</script>";
		}
	}

	function executePlainSQL($cmdstr)
	{ //takes a plain (no bound variables) SQL command and executes it
		//echo "<br>running ".$cmdstr."<br>";
		global $db_conn, $success;

		$statement = oci_parse($db_conn, $cmdstr);
		//There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work
		if (!$statement) {
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
			echo htmlentities($e['message']);
			$success = False;
		}

		$r = oci_execute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = oci_error($statement); // For oci_execute errors pass the statementhandle
			echo htmlentities($e['message']);
			$success = False;
		}
		return $statement;
	}

    function executeSelectPlainSQL($cmdstr)
    { //takes a plain (no bound variables) SQL command and executes it
        //echo "<br>running ".$cmdstr."<br>";
        global $db_conn, $success;

        $statement = oci_parse($db_conn, $cmdstr);
        //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
            echo htmlentities($e['message']);
            $success = False;
        }

        $r = oci_execute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = oci_error($statement); // For oci_execute errors pass the statementhandle
            echo htmlentities($e['message']);
            $success = False;
        }

        return $statement;
    }

    function executeUpdatePlainSQL($cmdstr)
    { //takes a plain (no bound variables) SQL command and executes it
        //echo "<br>running ".$cmdstr."<br>";
        global $db_conn, $success;

        $statement = oci_parse($db_conn, $cmdstr);
        //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
            echo htmlentities($e['message']);
            $success = False;
        }

        $r = oci_execute($statement, OCI_DEFAULT);

        if (!$r) {
            $success = False;
            $e = oci_error($statement); // For oci_execute errors pass the statementhandle
            $errorMessage = htmlentities($e['message']);
            if (strstr($errorMessage, 'NULL')) {
                echo "<br>";
                echo "<br>";
                echo "Please don't leave black for the feature you want to change!";
                echo "<br>";
            } else if (strstr($errorMessage, 'unique')) {
                echo "<br>";
                echo "<br>";
                echo "Your new name should be unique!";
                echo "<br>";
            }  else if (strstr($errorMessage, 'parent')) {
                echo "<br>";
                echo "<br>";
                echo "Your new sport name should already be in Sport table!";
                echo "<br>";
            } else if (strstr($errorMessage,"maximum")) {
                echo "<br>";
                echo "<br>";
                echo "Please limit input within 255 characters!";
                echo "<br>";
            }
        }
        return $success;
    }

    function executeBoundSQLInsert($cmdstr, $list)
    {
        /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
        In this case you don't need to create the statement several times. Bound variables cause a statement to only be
        parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
        See the sample code below for how this function is used */

        global $db_conn, $success;
        $statement = oci_parse($db_conn, $cmdstr);
        $success = True;

        if (!$statement) {
//            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
//            $e = OCI_Error($db_conn);
//            echo htmlentities($e['message']);
            $success = False;
        }

        foreach ($list as $tuple) {
            foreach ($tuple as $bind => $val) {
                //echo $val;
                //echo "<br>".$bind."<br>";
                oci_bind_by_name($statement, $bind, $val);
                unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
            }

            $r = oci_execute($statement, OCI_DEFAULT);
            if (!$r) {
                $e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
                $success = False;
                $errorMessage = htmlentities($e['message']);
//                echo "<br>";
//                echo $errorMessage;
//                echo "<br>";
                if (strstr($errorMessage, 'invalid number'))
                {
                    echo "<br>";
                    echo "<br>";
                    echo "Please input number for year active!";
                    echo "<br>";
                }
                else if (strstr($errorMessage,"maximum"))
                {
                    $l = explode(".",$errorMessage);
                    $length = count($l);
                    $s = $l[$length-1];
                    $l = explode("(",$s);
                    $length = count($l);
                    $s = $l[0];
                    echo "<br>";
                    echo "<br>";
                    echo "Please limit input of ";
                    echo $s;
                    echo "within 255 characters!";
                    echo "<br>";
                }
                else if (strstr($errorMessage,"NULL"))
                {
                    $l = explode(".",$errorMessage);
                    $length = count($l);
                    $s = $l[$length-1];
                    $l = explode(")",$s);
                    $length = count($l);
                    $s = $l[0];
                    echo "<br>";
                    echo "<br>";
                    echo "Please do not leave blank of ";
                    echo $s;
                    echo "!";
                    echo "<br>";
                }
                else if (strstr($errorMessage,"unique"))
                {
                    echo "<br>";
                    echo "<br>";
                    echo "Your input has already exist !";
                    echo "<br>";
                }
                else if (strstr($errorMessage,"parent"))
                {
                    echo "<br>";
                    echo "<br>";
                    echo "Your input does not exist in SportsPeople table!";
                    echo "<br>";
                    echo "Please input that person to SportsPeople table first!";
                    echo "<br>";
                }
            }
        }
        return $success;
    }
	function executeBoundSQL($cmdstr, $list)
	{
		/* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

		global $db_conn, $success;
		$statement = oci_parse($db_conn, $cmdstr);

		if (!$statement) {
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn);
			echo htmlentities($e['message']);
			$success = False;
		}

		foreach ($list as $tuple) {
			foreach ($tuple as $bind => $val) {
				//echo $val;
				//echo "<br>".$bind."<br>";
				oci_bind_by_name($statement, $bind, $val);
				unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
			}

			$r = oci_execute($statement, OCI_DEFAULT);
			if (!$r) {
				echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
				$e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
				echo htmlentities($e['message']);
				echo "<br>";
				$success = False;
			}
		}
	}

    function executeSQLDocument($name){
        $_sql = file_get_contents($name);
        $_arr = explode(';', $_sql);
        foreach ($_arr as $_value) {
            executePlainSQL($_value);
        }

    }

    function printTeamResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Team:<br>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Location</th><th>Name</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["LOCATION"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printVenueResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Venue:<br>";
        echo "<table>";
        echo "<tr><th>Name</th><th>Location</th><th>Capacity</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["LOCATION"] . "</td><td>" . $row["CAPACITY"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printSportResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Sport:<br>";
        echo "<table>";
        echo "<tr><th>Name</th><th>NumberOfFans</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["NUMBEROFFANS"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function handleDisplayTeamRequest()
    {
        global $db_conn;
        $result = executePlainSQL("SELECT * FROM Team");
        printTeamResult($result);
        oci_commit($db_conn);
    }

    function printSelectSportsPeopleResult($result, $oldRow)
    { //prints results from a select statement
        echo "<br>Retrieved data from table SportsPeople:<br>";
        echo "<table>";
        echo "<tr><th>Name</th><th>DateOfBirth</th><th>Nationality</th></tr>";
        echo "<tr><td>" . $oldRow["NAME"] . "</td><td>" . $oldRow["DATEOFBIRTH"] . "</td><td>" . $oldRow["NATIONALITY"] . "</td></tr>"; //or just use "echo $row[0]"

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["DATEOFBIRTH"] . "</td><td>" . $row["NATIONALITY"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printSportsPeopleResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table SportsPeople:<br>";
        echo "<table>";
        echo "<tr><th>Name</th><th>DateOfBirth</th><th>Nationality</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["DATEOFBIRTH"] . "</td><td>" . $row["NATIONALITY"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printCorporationResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Corporation:<br>";
        echo "<table>";
        echo "<tr><th>Name</th><th>TotalValue</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["TOTALVALUE"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printSponsorResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Sponsor:<br>";
        echo "<table>";
        echo "<tr><th>CorporationName</th><th>TeamID</th><th>NumOfYears</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["CORPORATIONNAME"] . "</td><td>" . $row["TEAMID"] . "</td><td>" . $row["NUMOFYEARS"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printCompetesAtResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table CompetesAt:<br>";
        echo "<table>";
        echo "<tr><th>TeamID</th><th>VenueName</th><th>VenueLocation</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["TEAMID"] . "</td><td>" . $row["VENUENAME"] . "</td><td>" . $row["VENUELOCATION"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }
    function printLeagueResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table League:<br>";
        echo "<table>";
        echo "<tr><th>Name</th><th>Region</th><th>SportName</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["REGION"] . "</td><td>" . $row["SPORTNAME"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printSeasonOrganizesResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table SeasonOrganizes:<br>";
        echo "<table>";
        echo "<tr><th>Year</th><th>LeagueName</th><th>Statistics</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["YEAR"] . "</td><td>" . $row["LEAGUENAME"] . "</td><td>" . $row["STATISTICS"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printParticipatesResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Participates:<br>";
        echo "<table>";
        echo "<tr><th>TeamID</th><th>SeasonYear</th><th>LeagueName</th><th>Ranking</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["TEAMID"] . "</td><td>" . $row["SEASONYEAR"] . "</td><td>" . $row["LEAGUENAME"] . "</td><td>" . $row["RANKING"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printPlayForResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table PlayFor:<br>";
        echo "<table>";
        echo "<tr><th>TeamID</th><th>SportPeopleName</th><th>SportPeopleDateOfBirth</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["TEAMID"] . "</td><td>" . $row["SPORTPEOPLENAME"] . "</td><td>" . $row["SPORTPEOPLEDATEOFBIRTH"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printCoachResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Coach:<br>";
        echo "<table>";
        echo "<tr><th>Name</th><th>DateOfBirth</th><th>YearsActive</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["DATEOFBIRTH"] . "</td><td>" . $row["YEARSACTIVE"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printAthleteResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Athlete:<br>";
        echo "<table>";
        echo "<tr><th>Name</th><th>DateOfBirth</th><th>Position</th><th>InjuryHistory</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["DATEOFBIRTH"] . "</td><td>" . $row["POSITION"] . "</td><td>" . $row["INJURYHISTORY"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printMatch1Result($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Match1:<br>";
        echo "<table>";
        echo "<tr><th>MatchDate</th><th>VenueLocation</th><th>VenueName</th><th>Type</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["MATCHDATE"] . "</td><td>" . $row["VENUELOCATION"] . "</td><td>" . $row["VENUENAME"] . "</td><td>" . $row["TYPE"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printMatch2Result($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Match2:<br>";
        echo "<table>";
        echo "<tr><th>ID</th><th>MatchDate</th><th>Result</th><th>VenueLocation</th><th>VenueName</th><th>LeagueName</th><th>SeasonYear</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["MATCHDATE"] . "</td><td>" . $row["RESULT"] . "</td><td>" . $row["VENUELOCATION"] . "</td><td>" . $row["VENUENAME"] . "</td><td>" . $row["LEAGUENAME"] . "</td><td>" . $row["SEASONYEAR"] .  "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printPlaysResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Plays:<br>";
        echo "<table>";
        echo "<tr><th>MatchID</th><th>TeamID</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>" . $row["MATCHID"] . "</td><td>" . $row["TEAMID"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function printGenericTable($result) {
        if (!$result) {
            echo "No data to display";
            return;
        }

        echo "<br>Retrieved data:<br>";
        echo "<table>";
    
        $firstRow = OCI_Fetch_Array($result, OCI_ASSOC);
        if ($firstRow) {
            echo "<tr>";
            foreach (array_keys($firstRow) as $columnName) {
                echo "<th>" . htmlspecialchars($columnName) . "</th>";
            }
            echo "</tr>";
    
            echo "<tr>";
            foreach ($firstRow as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
    
            while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
        }
    
        echo "</table>";
    }
    

	function connectToDB()
	{
		global $db_conn;
		global $config;

		// Your username is ora_(CWL_ID) and the password is a(student number). For example,
		// ora_platypus is the username and a12345678 is the password.
		// $db_conn = oci_connect("ora_cwl", "a12345678", "dbhost.students.cs.ubc.ca:1522/stu");
		$db_conn = oci_connect($config["dbuser"], $config["dbpassword"], $config["dbserver"]);

		if ($db_conn) {
			debugAlertMessage("Database is Connected");
			return true;
		} else {
			debugAlertMessage("Cannot connect to Database");
			$e = OCI_Error(); // For oci_connect errors pass no handle
			echo htmlentities($e['message']);
			return false;
		}
	}

	function disconnectFromDB()
	{
		global $db_conn;

		debugAlertMessage("Disconnect from Database");
		oci_close($db_conn);
	}

	function handleUpdateRequest()
	{
		global $db_conn;

		$old_name = $_POST['oldName'];
		$new_name = $_POST['newName'];
        $new_Region = $_POST['newRegion'];
        $new_SportName = $_POST['newSportName'];
        $bName = false;
        $bRegion = false;
        $bSportName = false;
        $up=$_POST['up'];
        $success = false;
        if ($up == null) {
            echo "<br>";
            echo "<br>";
            echo "Please select at least one checkbox!";
            echo "<br>";
            return;
        }
        foreach ($up as $e){
            if ($e == "name") {
                $bName = true;
            } else if ($e == "region") {
                $bRegion = true;
            } else if ($e == "sportName") {
                $bSportName = true;
            }
        }
        $result = executePlainSQL("SELECT * FROM League  WHERE name='" . $old_name . "'");
        $result = OCI_Fetch_Array($result, OCI_ASSOC);
        if (!$result) {
            echo "<br>";
            echo "<br>";
            echo "Please input name existing in League Table";
            echo "<br>";
            return;
        }
        if ($bName) {
            if ($bRegion) {
                if ($bSportName) {
                    $success = executeUpdatePlainSQL("UPDATE League SET name='" . $new_name . "', region='" . $new_Region . "', sportName='" . $new_SportName . "'  WHERE name='" . $old_name . "'");
                } else {
                    $success = executeUpdatePlainSQL("UPDATE League SET name='" . $new_name . "', region='" . $new_Region . "'  WHERE name='" . $old_name . "'");
                }
            } else {
                if ($bSportName) {
                    $success = executeUpdatePlainSQL("UPDATE League SET name='" . $new_name . "', sportName='" . $new_SportName . "'  WHERE name='" . $old_name . "'");
                } else {
                    $success = executeUpdatePlainSQL("UPDATE League SET name='" . $new_name . "'  WHERE name='" . $old_name . "'");
                }
            }
        } else {
            if ($bRegion) {
                if ($bSportName) {
                    $success = executeUpdatePlainSQL("UPDATE League SET region='" . $new_Region . "', sportName='" . $new_SportName . "'  WHERE name='" . $old_name . "'");
                } else {
                    $success = executeUpdatePlainSQL("UPDATE League SET region='" . $new_Region . "'  WHERE name='" . $old_name . "'");
                }
            } else {
                if ($bSportName) {
                    $success = executeUpdatePlainSQL("UPDATE League SET sportName='" . $new_SportName . "'  WHERE name='" . $old_name . "'");
                }
            }
        }
        if ($success) {
            echo "<br>";
            echo "<br>";
            echo "Successful!";
            echo "<br>";
            $result = executePlainSQL("SELECT * FROM League");
            printLeagueResult($result);
        }

        // you need the wrap the old name and new name values with single quotations
		oci_commit($db_conn);
	}

	function handleResetRequest()
	{
		global $db_conn;
		// Create new table
		echo "<br> creating new table and loading all data<br>";
        executeSQLDocument("c+i.sql");
		oci_commit($db_conn);
	}

    function handleSelectRequest()
    {
        global $db_conn;
        $s1 = $_GET['S1'];
        $l1 = $_GET['L1'];
        $s2 = $_GET['S2'];
        $l2 = $_GET['L2'];
        $s3 = $_GET['S3'];
        $l3 = $_GET['L3'];
        $s4 = $_GET['S4'];
        $l4 = $_GET['L4'];
        $s5 = $_GET['S5'];
        $t1=$_GET['t1'];
        $t2=$_GET['t2'];
        $t3=$_GET['t3'];
        $t4=$_GET['t4'];
        $t5=$_GET['t5'];
        if ($l1 == "NA") {
            $result = executeSelectPlainSQL("SELECT * from SportsPeople WHERE $s1='" . $t1 . "'");
        } else if ($l2 == "NA") {
            $result = executeSelectPlainSQL("SELECT * from SportsPeople WHERE $s1='" . $t1 . "' $l1 $s2='" . $t2 . "'");
        } else if ($l3 == "NA") {
            $result = executeSelectPlainSQL("SELECT * from SportsPeople WHERE $s1='" . $t1 . "' $l1 $s2='" . $t2 . "' $l2 $s3='" . $t3 . "'");
        } else if ($l4 == "NA") {
            $result = executeSelectPlainSQL("SELECT * from SportsPeople WHERE $s1='" . $t1 . "' $l1 $s2='" . $t2 . "' $l2 $s3='" . $t3 . "' $l3 $s4='" . $t4 . "'");
        } else {
            $result = executeSelectPlainSQL("SELECT * from SportsPeople WHERE $s1='" . $t1 . "' $l1 $s2='" . $t2 . "' $l2 $s3='" . $t3 . "' $l3 $s4='" . $t4 . "' $l4 $s5='" . $t5 . "'");
        }
        $row = OCI_Fetch_Array($result, OCI_ASSOC);
        if (!$row) {
            echo "<br>";
            echo "<br>";
            echo "Your selection is not existing!";
            echo "<br>";
            return;
        } else {
            echo "<br>";
            echo "<br>";
            echo "Successful!";
            echo "<br>";
            printSelectSportsPeopleResult($result, $row);
        }
        oci_commit($db_conn);
    }

    function handleAllInsertRequest()
    {
        global $db_conn;
        // Create new table
        echo "<br> loading all data <br>";
        executeSQLDocument("insert.sql");
        oci_commit($db_conn);
    }

	function handleTeamInsertRequest()
	{
		global $db_conn;

		//Getting the values from user and insert data into the table
		$tuple = array(
			":bind1" => $_POST['insID'],
			":bind2" => $_POST['insLocation'],
            ":bind3" => $_POST['insName']
		);

		$alltuples = array(
			$tuple
		);

		executeBoundSQL("insert into Team values (:bind1, :bind2, :bind3)", $alltuples);
		oci_commit($db_conn);
	}

    function handleVenueInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insName'],
            ":bind2" => $_POST['insLocation'],
            ":bind3" => $_POST['insCapacity']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into Venue values (:bind1, :bind2, :bind3)", $alltuples);
        oci_commit($db_conn);
    }

    function handleSportInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insName'],
            ":bind2" => $_POST['insNumberOfFans']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into Sport values (:bind1, :bind2)", $alltuples);
        oci_commit($db_conn);
    }

    function handleSportsPeopleInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insName'],
            ":bind2" => $_POST['insDateOfBirth'],
            ":bind3" => $_POST['insNationality']
        );

        $alltuples = array(
            $tuple
        );

        $s = executeBoundSQLInsert("insert into SportsPeople values (:bind1, :bind2, :bind3)", $alltuples);
        if ($s) {
            echo "<br>";
            echo "Success!";
            echo "<br>";
            $result = executePlainSQL("SELECT * FROM SportsPeople");
            printSportsPeopleResult($result);
        }
        oci_commit($db_conn);
    }

    function handleCorporationInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insName'],
            ":bind2" => $_POST['insTotalValue']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into Corporation values (:bind1, :bind2)", $alltuples);
        oci_commit($db_conn);
    }

    function handleSponsorInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insCorporationName'],
            ":bind2" => $_POST['insTeamID'],
            ":bind3" => $_POST['insNumOfYears']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into Sponsor values (:bind1, :bind2, :bind3)", $alltuples);
        oci_commit($db_conn);
    }

    function handleCompetesAtInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insTeamID'],
            ":bind2" => $_POST['insVenueName'],
            ":bind3" => $_POST['insVenueLocation']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into CompetesAt values (:bind1, :bind2, :bind3)", $alltuples);
        oci_commit($db_conn);
    }

    function handleLeagueInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insName'],
            ":bind2" => $_POST['insRegion'],
            ":bind3" => $_POST['insSportName']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into League values (:bind1, :bind2, :bind3)", $alltuples);
        oci_commit($db_conn);
    }

    function handleSeasonOrganizesInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insYear'],
            ":bind2" => $_POST['insLeagueName'],
            ":bind3" => $_POST['insStatistics']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into League values (:bind1, :bind2, :bind3)", $alltuples);
        oci_commit($db_conn);
    }

    function handleParticipatesInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insTeamID'],
            ":bind2" => $_POST['insSeasonYear'],
            ":bind3" => $_POST['insLeagueName'],
            ":bind4" => $_POST['insRanking']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into Participates values (:bind1, :bind2, :bind3, :bind4)", $alltuples);
        oci_commit($db_conn);
    }

    function handlePlayForInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insTeamID'],
            ":bind2" => $_POST['insSportPeopleName'],
            ":bind3" => $_POST['insSportPeopleDateOfBirth']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into PlayFor values (:bind1, :bind2, :bind3)", $alltuples);
        oci_commit($db_conn);
    }

    function handleCoachInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insName'],
            ":bind2" => $_POST['insDateOfBirth'],
            ":bind3" => $_POST['insYearsActive']
        );

        $alltuples = array(
            $tuple
        );

        $s = executeBoundSQLInsert("insert into Coach values (:bind1, :bind2, :bind3)", $alltuples);
        if ($s) {
            echo "<br>";
            echo "Success!";
            echo "<br>";
            $result = executePlainSQL("SELECT * FROM Coach");
            printCoachResult($result);
        }
        oci_commit($db_conn);
    }

    function handleAthleteInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insName'],
            ":bind2" => $_POST['insDateOfBirth'],
            ":bind3" => $_POST['insPosition'],
            ":bind4" => $_POST['insInjuryHistory']
        );

        $alltuples = array(
            $tuple
        );

        $s = executeBoundSQLInsert("insert into Athlete values (:bind1, :bind2, :bind3, :bind4)", $alltuples);
        if ($s) {
            echo "<br>";
            echo "Success!";
            echo "<br>";
            $result = executePlainSQL("SELECT * FROM Athlete");
            printAthleteResult($result);
        }
        oci_commit($db_conn);
    }

    function handleMatch1InsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insMatchDate'],
            ":bind2" => $_POST['insVenueLocation'],
            ":bind3" => $_POST['insVenueName'],
            ":bind4" => $_POST['insType']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into Match1 values (:bind1, :bind2, :bind3, :bind4)", $alltuples);
        oci_commit($db_conn);
    }

    function handleMatch2InsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insID'],
            ":bind2" => $_POST['insMatchDate'],
            ":bind3" => $_POST['insResult'],
            ":bind4" => $_POST['insVenueLocation'],
            ":bind5" => $_POST['insVenueName'],
            ":bind6" => $_POST['insLeagueName'],
            ":bind7" => $_POST['insSeasonYear']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into Match2 values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7)", $alltuples);
        oci_commit($db_conn);
    }

    function handlePlaysInsertRequest()
    {
        global $db_conn;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => $_POST['insMatchID'],
            ":bind2" => $_POST['insTeamID']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into Plays values (:bind1, :bind2)", $alltuples);
        oci_commit($db_conn);
    }

	function handleCountRequest()
	{
		global $db_conn;

		$result = executePlainSQL("SELECT Count(*) FROM demoTable");

		if (($row = oci_fetch_row($result)) != false) {
			echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
		}
        oci_commit($db_conn);
    }


    
    function handleDeleteFromPlayFor()
    {
        global $db_conn;
        echo "<br> deleting a record from PlayFor <br>";
        $deleteTeamID = $_POST['deleteTeamID'];
        $deleteSportPeopleName = $_POST['deleteSportPeopleName'];
        $deleteSportPeopleDateOfBirth = $_POST['deleteSportPeopleDateOfBirth'];

        if (empty($deleteTeamID) && $deleteTeamID!=0) {
            echo "TeamID is empty. Please provide a valid TeamID.";
            oci_commit($db_conn);
            return; // Exit the function if deleteTeamID is empty
        }
        $result=executePlainSQL("DELETE FROM PlayFor WHERE TeamID = $deleteTeamID AND SportPeopleName = '$deleteSportPeopleName' AND SportPeopleDateOfBirth = '$deleteSportPeopleDateOfBirth'");
        
        oci_commit($db_conn);
    }

    function handleDeleteFromParticipates()
    {
        global $db_conn;
        echo "<br> deleting a record from Participates <br>";
        $deleteTeamID = $_POST['deleteTeamID'];
        $deleteSeasonYear = $_POST['deleteSeasonYear'];
        $deleteLeagueName = $_POST['deleteLeagueName'];
        if (empty($deleteTeamID) && $deleteTeamID!=0) {
            echo "TeamID is empty. Please provide a valid TeamID.";
            oci_commit($db_conn);
            return; // Exit the function if deleteTeamID is empty
        }
        if (empty($deleteSeasonYear) && $deleteSeasonYear!=0) {
            echo "SeasonYear is empty. Please provide a valid SeasonYear.";
            oci_commit($db_conn);
            return; // Exit the function if deleteTeamID is empty
        }
        $result = executePlainSQL("DELETE FROM Participates WHERE TeamID = $deleteTeamID AND SeasonYear = $deleteSeasonYear AND LeagueName = '$deleteLeagueName'");
        
        oci_commit($db_conn);
    }

    function handleDisplayLeagueRequest()
    {
        global $db_conn;
        $result = executePlainSQL("SELECT * FROM League");
        printLeagueResult($result);
        $result = executePlainSQL("SELECT * FROM Sport");
        printSportResult($result);
        oci_commit($db_conn);
    }

    function handleDivisionRequest()
    {
        global $db_conn;
        $result = executePlainSQL("SELECT * FROM Corporation C WHERE NOT EXISTS ((SELECT S1.teamID FROM Sponsor S1) MINUS (SELECT S2.teamID FROM Sponsor S2 WHERE S2.corporationName = C.name))");
        echo "<br>";
        echo "<br>";
        echo "Successful!";
        echo "<br>";
        printCorporationResult($result);
        oci_commit($db_conn);
    }

    function handleNagbRequest()
    {
        global $db_conn;
        $result = executePlainSQL("SELECT * FROM Venue V1 WHERE V1.capacity > ALL(SELECT AVG(V2.capacity) FROM Venue V2 GROUP BY location)");
        echo "<br>";
        echo "<br>";
        echo "Successful!";
        echo "<br>";
        printVenueResult($result);
        oci_commit($db_conn);
    }

    function handleProjectionRequest(){
        global $db_conn;
        $selectedColumns = $_POST['columns'];

        if (empty($selectedColumns)) {
            echo "Please select at least one column.";
        } else {
            $sql = "SELECT " . implode(", ", $selectedColumns) . " FROM Match2";

            echo "<p>Generated SQL Query: $sql</p>";
            $statement = oci_parse($db_conn, $sql);
            $result = oci_execute($statement);
            if (!$result) {
                $error = oci_error($statement); 
                echo "<p>Error in execute: " . $error['message'] . "</p>";
            } else {
                printGenericTable($statement);
            }
        }
        oci_commit($db_conn);
    }

    function handleCoachQuery() {
        global $db_conn; // Assuming $db_conn is your Oracle database connection
    
        // Retrieve the yearsActive value from the POST request
        $yearsActive = $_POST['yearsActive'];
    
        // Check if yearsActive is provided
        if (empty($yearsActive) && $yearsActive!=0) {
            echo "Please specify the minimum years active.";
        } else {
            // Create the SQL query
            $sql = "SELECT name, dateOfBirth, yearsActive FROM Coach WHERE yearsActive >= :yearsActive";
    
            // Display the generated SQL query (for debugging or verification purposes)
            echo "<p>Generated SQL Query: $sql</p>";
    
            // Prepare the SQL statement
            $statement = oci_parse($db_conn, $sql);
    
            // Bind the yearsActive parameter to the statement
            oci_bind_by_name($statement, ":yearsActive", $yearsActive);
    
            // Execute the statement
            $result = oci_execute($statement);
    
            // Check for errors in execution
            if (!$result) {
                $error = oci_error($statement);
                echo "<p>Error in execute: " . $error['message'] . "</p>";
            } else {
                // Function to print the results in a generic table format
                printGenericTable($statement);
            }
        }
    
        // Commit the transaction
        oci_commit($db_conn);
    }

    function handleJoinRequest() {
        global $db_conn;
        $teamName = $_GET["teamName"];
        
        $result = executeSelectPlainSQL("SELECT t.name, p.teamID, t.location, p.sportPeopleName, p.sportPeopleDateOfBirth FROM PlayFor p, Team t WHERE p.teamID = t.ID AND t.name = '$teamName'");
        
        $err = OCI_Fetch_Array($result, OCI_ASSOC);
        if (!$err) {
            echo "<br>";
            echo "<br>";
            echo "Please input a team name existing in Teams";
            echo "<br>";
            return;
        }
        echo "<br>";
        echo "<br>";
        echo "Successful!";
        echo "<br>";
        $result = executeSelectPlainSQL("SELECT t.name, p.teamID, t.location, p.sportPeopleName, p.sportPeopleDateOfBirth FROM PlayFor p, Team t WHERE p.teamID = t.ID AND t.name = '$teamName'");
        printGenericTable($result);
        oci_commit($db_conn);
    }

    function handleGroupByRequest() {
        global $db_conn;
        $nationality = $_GET["nationality"];
        
        $result = executeSelectPlainSQL("SELECT s.nationality, COUNT(*) AS Total FROM SPORTSPEOPLE s GROUP BY s.nationality HAVING s.nationality = '$nationality'");
        
        $err = OCI_Fetch_Array($result, OCI_ASSOC);
        if (!$err) {
            echo "<br>";
            echo "<br>";
            echo "Please input a nationality existing in Sports People";
            echo "<br>";
            return;
        }
        $result = executeSelectPlainSQL("SELECT s.nationality, COUNT(*) AS Total FROM SPORTSPEOPLE s GROUP BY s.nationality HAVING s.nationality = '$nationality'");
        printGenericTable($result);
        oci_commit($db_conn);
    }
    

    function handleDisplaySportsPeopleRequest()
    {
        global $db_conn;
        $result = executePlainSQL("SELECT * FROM SportsPeople");
        printSportsPeopleResult($result);
        $result = executePlainSQL("SELECT * FROM Coach");
        printCoachResult($result);
        $result = executePlainSQL("SELECT * FROM Athlete");
        printAthleteResult($result);
        oci_commit($db_conn);
    }

    function handleDisplaySportsPeopleOnlyRequest()
    {
        global $db_conn;
        $result = executePlainSQL("SELECT * FROM SportsPeople");
        printSportsPeopleResult($result);
        oci_commit($db_conn);
    }
	function handleDisplayRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT * FROM Team");
        printTeamResult($result);
        $result = executePlainSQL("SELECT * FROM Venue");
        printVenueResult($result);
        $result = executePlainSQL("SELECT * FROM Sport");
        printSportResult($result);
        $result = executePlainSQL("SELECT * FROM SportsPeople");
        printSportsPeopleResult($result);
        $result = executePlainSQL("SELECT * FROM Corporation");
        printCorporationResult($result);
        $result = executePlainSQL("SELECT * FROM Sponsor");
        printSponsorResult($result);
        $result = executePlainSQL("SELECT * FROM CompetesAt");
        printCompetesAtResult($result);
        $result = executePlainSQL("SELECT * FROM League");
        printLeagueResult($result);
        $result = executePlainSQL("SELECT * FROM SeasonOrganizes");
        printSeasonOrganizesResult($result);
        $result = executePlainSQL("SELECT * FROM Participates");
        printParticipatesResult($result);
        $result = executePlainSQL("SELECT * FROM PlayFor");
        printPlayForResult($result);
        $result = executePlainSQL("SELECT * FROM Coach");
        printCoachResult($result);
        $result = executePlainSQL("SELECT * FROM Athlete");
        printAthleteResult($result);
        $result = executePlainSQL("SELECT * FROM Match1");
        printMatch1Result($result);
        $result = executePlainSQL("SELECT * FROM Match2");
        printMatch2Result($result);
        $result = executePlainSQL("SELECT * FROM Plays");
        printPlaysResult($result);
        oci_commit($db_conn);
    }

	// HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('resetTablesRequest', $_POST)) {
				handleResetRequest();
			} else if (array_key_exists('updateQueryRequest', $_POST)) {
				handleUpdateRequest();
			} else if (array_key_exists('insertTeamRequest', $_POST)) {
				handleTeamInsertRequest();
			} else if (array_key_exists('insertVenueRequest', $_POST)) {
                handleVenueInsertRequest();
            } else if (array_key_exists('insertSportRequest', $_POST)) {
                handleSportInsertRequest();
            } else if (array_key_exists('insertSportsPeopleRequest', $_POST)) {
                handleSportsPeopleInsertRequest();
            } else if (array_key_exists('insertCorporationRequest', $_POST)) {
                handleCorporationInsertRequest();
            } else if (array_key_exists('insertSponsorRequest', $_POST)) {
                handleSponsorInsertRequest();
            } else if (array_key_exists('insertCompetesAtRequest', $_POST)) {
                handleCompetesAtInsertRequest();
            } else if (array_key_exists('insertLeagueRequest', $_POST)) {
                handleLeagueInsertRequest();
            } else if (array_key_exists('insertSeasonOrganizesRequest', $_POST)) {
                handleSeasonOrganizesInsertRequest();
            } else if (array_key_exists('insertParticipatesRequest', $_POST)) {
                handleParticipatesInsertRequest();
            } else if (array_key_exists('insertPlayForRequest', $_POST)) {
                handlePlayForInsertRequest();
            } else if (array_key_exists('insertCoachRequest', $_POST)) {
                handleCoachInsertRequest();
            } else if (array_key_exists('insertAthleteRequest', $_POST)) {
                handleAthleteInsertRequest();
            } else if (array_key_exists('insertMatch1Request', $_POST)) {
                handleMatch1InsertRequest();
            } else if (array_key_exists('insertMatch2Request', $_POST)) {
                handleMatch2InsertRequest();
            } else if (array_key_exists('insertPlaysRequest', $_POST)) {
                handlePlaysInsertRequest();
            } else if (array_key_exists('insertAll', $_POST)) {
                handleAllInsertRequest();
            } else if (array_key_exists('deletePlayForRequest', $_POST)){
                handleDeleteFromPlayFor();
            } else if (array_key_exists('deleteParticipatesRequest', $_POST)){
                handleDeleteFromParticipates();
            } else if (array_key_exists('projectionMatch2', $_POST)){
                handleProjectionRequest();
            } else if (array_key_exists('coachQueryRequest',$_POST)){
                handleCoachQuery();
            }

			disconnectFromDB();
		}
	}

    function handleDisplaySelectRequest()
    {
        global $db_conn;
        $s = $_GET['S'];
        if ($s == "Team") {
            $result = executePlainSQL("SELECT * FROM Team");
            printTeamResult($result);
        } else if ($s == "Venue") {
            $result = executePlainSQL("SELECT * FROM Venue");
            printVenueResult($result);
        } else if ($s == "Sport") {
            $result = executePlainSQL("SELECT * FROM Sport");
            printSportResult($result);
        } else if ($s == "SportsPeople") {
            $result = executePlainSQL("SELECT * FROM SportsPeople");
            printSportsPeopleResult($result);
        } else if ($s == "Corporation") {
            $result = executePlainSQL("SELECT * FROM Corporation");
            printCorporationResult($result);
        } else if ($s == "Sponsor") {
            $result = executePlainSQL("SELECT * FROM Sponsor");
            printSponsorResult($result);
        } else if ($s == "CompetesAt") {
            $result = executePlainSQL("SELECT * FROM CompetesAt");
            printCompetesAtResult($result);
        } else if ($s == "League") {
            $result = executePlainSQL("SELECT * FROM League");
            printLeagueResult($result);
        } else if ($s == "SeasonOrganizes") {
            $result = executePlainSQL("SELECT * FROM SeasonOrganizes");
            printSeasonOrganizesResult($result);
        } else if ($s == "Participates") {
            $result = executePlainSQL("SELECT * FROM Participates");
            printParticipatesResult($result);
        } else if ($s == "PlayFor") {
            $result = executePlainSQL("SELECT * FROM PlayFor");
            printPlayForResult($result);
        } else if ($s == "Coach") {
            $result = executePlainSQL("SELECT * FROM Coach");
            printCoachResult($result);
        } else if ($s == "Athlete") {
            $result = executePlainSQL("SELECT * FROM Athlete");
            printAthleteResult($result);
        } else if ($s == "Match1") {
            $result = executePlainSQL("SELECT * FROM Match1");
            printMatch1Result($result);
        } else if ($s == "Match2") {
            $result = executePlainSQL("SELECT * FROM Match2");
            printMatch2Result($result);
        } else if ($s == "Plays") {
            $result = executePlainSQL("SELECT * FROM Plays");
            printPlaysResult($result);
        }
        oci_commit($db_conn);
    }

	// HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('countTuples', $_GET)) {
				handleCountRequest();
			} elseif (array_key_exists('displayTuples', $_GET)) {
                handleDisplayRequest();
            } elseif (array_key_exists('displayLeagueTuples', $_GET)) {
                handleDisplayLeagueRequest();
            } elseif (array_key_exists('displaySportsPeopleTuples', $_GET)) {
                handleDisplaySportsPeopleRequest();
            } elseif (array_key_exists('displaySportsPeopleOnlyTuples', $_GET)) {
                handleDisplaySportsPeopleOnlyRequest();
            } elseif (array_key_exists('selectSubmit', $_GET)) {
                handleSelectRequest();
            } elseif (array_key_exists('division', $_GET)) {
                handleDivisionRequest();
            } elseif (array_key_exists('nagb', $_GET)) {
                handleNagbRequest();
            } elseif (array_key_exists('displaySelectTuples', $_GET)) {
                handleDisplaySelectRequest();
            } elseif (array_key_exists('join', $_GET)) {
                handleJoinRequest();
            } elseif (array_key_exists('groupBy', $_GET)) {
                handleGroupByRequest();
            } elseif (array_key_exists('displayTeamTuples', $_GET)) {
                handleDisplayTeamRequest();
            }


			disconnectFromDB();
		}
	}

	if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['deleteSubmit']) || isset($_POST['projectionSubmit']) || isset($_POST['findCoaches'])) {
		handlePOSTRequest();
	} else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTuplesRequest'])|| isset($_GET['displayLeagueTuplesRequest'])|| isset($_GET['displaySportsPeopleTuplesRequest'])|| isset($_GET['displaySportsPeopleOnlyTuplesRequest'])|| isset($_GET['selectQueryRequest'])|| isset($_GET['divisionRequest'])|| isset($_GET['nagbRequest'])|| isset($_GET['displaySelectTuplesRequest'])|| isset($_GET['displayTeamTuplesRequest']) || isset($_GET['joinRequest']) || isset($_GET['groupByRequest'])) {
		handleGETRequest();
	}

	// End PHP parsing and send the rest of the HTML content
	?>
</body>

</html>