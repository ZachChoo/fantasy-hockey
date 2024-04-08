<?php
session_start()
?>
<html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>
        <!--<h2>Reset</h2>
        <p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>
        <form method="POST" action="project.php">
            <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
            <p><input type="submit" value="Reset" name="reset"></p>
        </form>
        <hr />
        <h2>Insert Values into DemoTable</h2>
        <form method="POST" action="project.php">
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Number: <input type="text" name="insNo"> <br /><br />
            Name: <input type="text" name="insName"> <br /><br />
            <input type="submit" value="Insert" name="insertSubmit"></p>
        </form>
        <hr />
        <h2>Update Name in DemoTable</h2>
        <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>
        <form method="POST" action="project.php">
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            Old Name: <input type="text" name="oldName"> <br /><br />
            New Name: <input type="text" name="newName"> <br /><br />
            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>
        <hr />
        <h2>Count the Tuples in DemoTable</h2>
        <form method="GET" action="project.php">
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <input type="submit" name="countTuples"></p>
        </form>
        <h2>Print the Tuples in DemoTable</h2>
        <form method="GET" action="project.php">
            <input type="hidden" id="printTupleRequest" name="printTupleRequest">
            <input type="submit" name="printTuples"></p>
        </form>-->

        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())
        $logged_in = False;

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

			return $statement;
		}

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

			global $db_conn, $success;
			$statement = OCIParse($db_conn, $cmdstr);

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
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
				}

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function printResult($result) { //prints results from a select statement
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] ."</td><td>" . $row[2] ."</td><td>" . $row[3] ."</td><td>" . $row[4] ."</td><td>" . $row[5] ."</td><td>" . $row[6] ."</td><td>" . $row[7] ."</td><td>" . $row[8] ."</td><td>" . $row[9] ."</td><td>" . $row[10] ."</td><td>" . $row[11] ."</td><td>" . $row[12] ."</td><td>" . $row[13] ."</td><td>" . $row[14] ."</td><td>" . $row[15] ."</td><td>" . $row[16] ."</td><td>" . $row[17] ."</td><td>" . $row[18] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_nitahieb", "a55607303", "dbhost.students.cs.ubc.ca:1522/stu");

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        //PROJECTFUNCTION
        function deleteCoach($username) {
            global $db_conn;
            executePlainSQL("delete from Coach WHERE username='" . $username . "'");
            OCICommit($db_conn);
        }
        
        //PROJECTFUNCTION
        function UpdatePlayer($name,$number,$teamname) {
            global $db_conn;


            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE Player SET fantasyTeamName='" . $teamname . "' WHERE playername='" . $name . "' AND playerNumber='" .$number . "'");
            OCICommit($db_conn);
        }

        //PROJECTFUNCTION
        function handleJoinRequest($rows) {
            global $db_conn;

            $result = executePlainSQL("SELECT p.playerName,p.playerNumber,p.goalsScored,p.fantasyTeamName,p.realTeamName,e.skateModel,e.stickModel FROM Player p, EquipR1 e
            WHERE p.playerName = e.playerName AND p.playerNumber = e.playerNumber AND e.lockerNumber=1 AND p.goalsScored>$rows");
            
            $arr = array(
                0 => 'Name',
                1 => 'Number',
                2 => 'Points Scored',
                3 => 'Fantasy Team',
                4 => 'Real Team',
                5 => 'Skate Model',
                6 => 'Stick Model'
            );
            betterPrintResult($result,$arr);
        }

        //PROJECTFUNCTION
        function groupbyaggregation($rows) {
            global $db_conn;
            $arr = array(
                0 => 'Fantasy Team',
                1 => 'Goals'
            );

            $result = executePlainSQL("SELECT p.fantasyTeamName,sum(pl.goals)
            FROM Player p, PlayedIn pl, Match m
            WHERE p.playerName = pl.playerName AND p.playerNumber = pl.playerNumber AND m.id = pl.ID AND p.goalsScored>$rows
            GROUP BY p.fantasyTeamName");

            betterPrintResult($result,$arr);
        }

        //PROJECTFUNCTION
        function groupbyhaving($rows) {
            global $db_conn;

            $result = executePlainSQL("SELECT p.fantasyTeamName,avg(pl.goals)
            FROM Player p, PlayedIn pl, Match m
            WHERE p.playerName = pl.playerName AND p.playerNumber = pl.playerNumber AND m.id = pl.ID
            GROUP BY p.fantasyTeamName
            HAVING avg(pl.goals)>$rows");

            $arr = array(
                0 => 'Fantasy Team',
                1 => 'Average Goals'
            );

            betterPrintResult($result,$arr);
        }

        //PROJECTFUNCTION
        function groupnested($rows) {
            global $db_conn;
            $arr = array(
                0 => 'Player Name',
                1 => 'Fantasy Team',
                2 => 'Points Scored'
            );
            $result = executePlainSQL("SELECT max(p.playerName) as Playername,p.fantasyTeamName,MAX(p.goalsscored)
            FROM Player p, PlayedIn pl
            WHERE p.playerNumber = pl.playerNumber AND p.fantasyTeamName IN
            (SELECT distinct p.fantasyTeamName
            FROM Player p, PlayedIn pl, Match m
            WHERE p.playerName = pl.playerName AND p.playerNumber = pl.playerNumber AND m.id = pl.ID
            GROUP BY p.fantasyTeamName,p.playerName,p.playerNumber
            HAVING count(m.id)>$rows)
            GROUP BY p.fantasyTeamName");

            betterPrintResult($result,$arr);
        }

        //PROJECTFUNCTION
        function display_signup_page() {
            echo "<h2>SIGN UP</h2>
                  <form method='POST' action='project.php' >
                  <input type='hidden' id='signupRequest' name='signupRequest'>
                  Username: <input type='text' name='username'> <br /><br />
                  Email: <input type='text' name='email'> <br /><br />
                  Birthdate: <input type='date' name='birthdate'> <br /><br />
                  <input type='radio' id='coach' name='acc_type' value='Coach'><label for='coach'>Coach</label><br>      
                  <input type='radio' id='viewer' name='acc_type' value='Viewer'><label for='viewer'>Viewer</label><br>      
                  <p><input type='submit' value='Signup' name='signup'></p>
                  </form><br><br>
                  <h3>Or log in:</h3>
                  <form method='POST' action='project.php'>
                  <input type='hidden' id='loginRequest' name='loginRequest'>
                  Username: <input type='text' name='username'> <br /><br />
                  <input type='radio' id='coach' name='acc_type_login' value='Coach'><label for='coach'>Coach</label><br>      
                  <input type='radio' id='viewer' name='acc_type_login' value='Viewer'><label for='viewer'>Viewer</label><br>
                  <p><input type='submit' value='Login' name='login'></p>
                  
                  </form>
                  ";
        }

        function handleLogin() {
            if (isset($_SESSION['username'])) {
                displayCoachInfo();
                return;
            }

            $username = $_POST["username"];
            $result = executePlainSQL("SELECT COUNT(username) FROM Coach WHERE username='$username'");
            $row = OCI_Fetch_Array($result, OCI_BOTH);
            $count=$row[0];

            if ($count > 0) {
                $_SESSION['username'] = $username;
                displayCoachInfo();
            } else {
                echo "<h2>Login failed! </h2>";
                display_signup_page();
            }
        }

        function displayCoachInfo(){
            echo "<h2>SIGNED IN</h2>
                  <form method='POST' action='project.php' >
                  <input type='hidden' id='deleteCoachRequest' name='deleteCoachRequest'>   
                  <p><input type='submit' value='Delete Account' name='deleteaccount'></p>
                  </form>
                  ";

            echo <<< EOD
                <form method='POST' action='project.php'>
                    <p><input type='submit' value='Display Advanced League Stats' name='displayadvstats'></p>
                </form>
                <form method='POST' action='project.php'>
                    <p><input type='submit' value='Display Equipment of High-Scoring Players' name='displayequipstats'></p>
                </form>
                <form method='POST' action='project.php'>
                    <p><input type='submit' value='Manage Teams' name='displayTeam'></p>
                </form>
EOD;

        }

        function handleDeleteCoach() {
            deleteCoach($_SESSION['username']);
            unset($_SESSION['username']);
            display_signup_page();
        }

        function handlesignup() {
            global $db_conn;

            $email = $_POST["email"];
            $username = $_POST["username"];
            $birthDate = $_POST["birthdate"];

            $tuple = array (
                ":bind1" => $username,
                ":bind2" => $birthDate,
                ":bind3" => $email
            );

            $alltuples = array (
                $tuple
            );

            if ($_POST["acc_type"]=="Coach") {
                executeBoundSQL("insert into coach values (:bind1, TO_DATE(:bind2,'YYYY/MM/DD'),:bind3)", $alltuples);
            } else if ($_POST["acc_type"]=="Viewer") {
                executeBoundSQL("insert into viewerr1 values(:bind1, TO_DATE(:bind2, 'YYYY/MM/DD'), :bind3,NULL)", $alltuples);
            }
            OCICommit($db_conn);

            $_SESSION['username'] = $username;
            displayCoachInfo();
        }

        function handleDisplayAdvancedStats($aggWithGroupBy_xGoals, $aggWithHaving_xGoals, $nestedAgg_xGames) {
            echo "<form method='Post' action='project.php'><p><input type='submit' value='Return to Account Page' name='loginRequest'></form>";

            echo "<form  method='Post' action='project.php'>";
            echo "<br><h3>Goals per team including only players who scored more than ($aggWithGroupBy_xGoals) goals </h3>";
            groupbyaggregation($aggWithGroupBy_xGoals);
            echo "<p>Minimum player goals: <input type='number' value=$aggWithGroupBy_xGoals name='aggWithGroupBy_xGoals'></p>";

            echo "<br><h3>Average goals scored by teams who have scored more than average ($aggWithHaving_xGoals) goals</h3>";
            groupbyhaving($aggWithHaving_xGoals);
            echo "<p> Minimum team goals: <input type='number' value=$aggWithHaving_xGoals name='aggWithHaving_xGoals'></p>";

            echo "<br><h3>Player on each team with the most goals on team with more than ($nestedAgg_xGames) matches</h3>";
            groupnested($nestedAgg_xGames);
            echo "<p>Minimum team games: <input type='number' value=$nestedAgg_xGames name='nestedAgg_xGames'></p>";

            echo "<br><h3>Players who have played in all matches their team has played</h3>";
            handleDivision();

            echo "<br><input type='submit' value='Submit' name='displayadvstats'>";
            echo "</form>";
        }

        function handleDivision() {
            $players = executePlainSQL("
                SELECT DISTINCT p.playerName 
                FROM Player p, fantasyTeam f 
                WHERE NOT EXISTS (
                    (SELECT id FROM 
                        (SELECT DISTINCT p2.fantasyTeamName, m.id 
                        FROM Player p2, PlayedIn pl, Match m 
                        WHERE p2.playerName = pl.playerName AND p2.playerNumber = pl.playerNumber AND m.id = pl.ID AND p2.fantasyteamname = f.fantasyTeamName)
                    ) MINUS 
                    (SELECT pi.id 
                    FROM PlayedIn pi 
                    WHERE pi.playername = p.playername AND p.playerNumber = pi.playerNumber)
                )
            ");
            echo "Display Stat?<input type='checkbox' name='divisionCheckbox'><br>";
            if (isset($_POST['divisionCheckbox'])) {
                betterPrintResult($players, array(0 => 'Name'));
            }
        }

        function handleDisplayGeneralStats($moreGoalsThan_x, $displayCols) {
            echo "<form method='Post' action='project.php'><p><input type='submit' value='Return to Account Page' name='loginRequest'></form>";

            echo "<form  method='Post' action='project.php'>";

            echo "<br><h3>Average goals scored by teams who have scored more than x) goals</h3>";
            groupbyhaving(0);
            echo "<p> Minimum team goals: <input type='number' value=0 name='aggWithHaving_xGoals'></p>";

            echo "<br><input type='submit' value='Submit' name='displaygenstats'>";
            echo "</form>";
        }

        function handleDisplayEquipment($equipGoals_x) {
            echo "<form method='Post' action='project.php'><p><input type='submit' value='Return to Account Page' name='loginRequest'></form>";

            echo "<form  method='Post' action='project.php'>";

            echo "<br><h3>Equipment of players who've scored more than x ($equipGoals_x) goals.</h3>";
            handleJoinRequest($equipGoals_x);
            echo "<p> Minimum player goals: <input type='number' value=($equipGoals_x) name='equip_xGoals'></p>";

            echo "<br><input type='submit' value='Submit' name='displayequipstats'>";
            echo "</form>";
        }

        function handleDisplayTeam() {
            //selection
            echo "
            <form method='Post' action='project.php'><p><input type='submit' value='Return to Account Page' name='loginRequest'></form>
            <h1>Manage Teams</h1>
            <h3>Select</h3>
            <form method='POST' action='project.php'>
                View table: <br>
                <input type='hidden' id='selectRequest' name='selectRequest'>
                <input type='radio' name='playerOrTeam' value='Player' checked='checked'> Players<br>
                <input type='radio' name='playerOrTeam' value='FantasyTeam'> Team<br><br>
                Attribute 1: <input type='text' name='att1'>
                Value 1: <input type='text' name='val1'><br><br>
                Attribute 2: <input type='text' name='att2'>
                Value 2: <input type='text' name='val2'><br><br>
                <input type='submit' value='Select' name='submitSelect'>
            </form>";
            handleSelect();
            
            //projection
            echo"
            <br><br>
            <h3>Project</h3>
            <form method='POST' action='project.php'>
                View: <br>
                <input type='hidden' id='projectRequest' name='projectRequest'>
                <input type='radio' name='playerName' value='playerName'>Name<br>
                <input type='radio' name='goalsSaved' value='goalsSaved'>Goals Saved<br>
                <input type='radio' name='goalsScored' value='goalsScored'>Goals Scored<br>
                <input type='radio' name='playerNumber' value='playerNumber'>Number<br>
                <input type='radio' name='fantasyTeamName' value='fantasyTeamName'>Fantasy Team Name<br>
                <input type='submit' value='Project' name='submitProject'>
            </form>";
            handleProject();
            
            //update
            echo"
            <br><br>
            <h3>Update</h3>
            <form method='POST' action='project.php'>
                <input type='hidden' id='updateQueryRequest' name='updateQueryRequest'>
                Update Player: <input type='text' name='playerName2'> <br /><br />
                Jersey Number: <input type='text' name='playerNum'> <br /><br />
                Attribue: <input type='text' name='newAtt'> <br /><br />
                With Value: <input type='text' name='newVal'> <br /><br />
                <input type='submit' value='Make Update' name='tradeToTeam'>
            </form>";
            handleTrade();
        }

        function handleTrade() {
            global $db_conn;

            $playerName = $_POST['playerName2'];
            $playerNum = $_POST['playerNum'];
            $newAtt = $_POST['newAtt'];
            $newVal = $_POST['newVal'];

            if ($playerName && $playerNum && $newAtt && $newVal) {
                
                executePlainSQL("UPDATE Player SET " . $newAtt . "='" . $newVal . "' WHERE playername='" . $playerName . "' AND playernumber=$playerNum");
            }
            
            OCICommit($db_conn);
        } 

        function handleSelect() {
            global $db_conn;

            $playerOrTeam = $_POST['playerOrTeam'];
            $att1 = $_POST['att1'];
            $val1 = $_POST['val1'];
            $att2 = $_POST['att2'];
            $val2 = $_POST['val2'];
            if ($playerOrTeam && $att1 && $val1 && $att2 && $val2) {
                $sel = executePlainSQL("SELECT * FROM " . $playerOrTeam . " WHERE " . $att1 . "='" . $val1 . "' AND " . $att2 . "='" . $val2 . "'");
            }
            if ($playerOrTeam == "Player"){
                $arr = array(
                    0 => 'Name',
                    1 => 'Saves',
                    2 => 'Points',
                    3 => 'Number',
                    4 => 'Real Team',
                    5 => 'Fantasy Team'
                );
            } elseif($playerOrTeam == "FantasyTeam") {
                $arr = array(
                    0 => 'Team Name',
                    1 => 'Record',
                    2 => 'Coach'
                );

            }



            betterPrintResult($sel, $arr);
            OCICommit($db_conn);
        } 

        function handleProject() {
            global $db_conn;
            $attributeArr = array();
            $arr = array(
                0 => $_POST['playerName'] ? $_POST['playerName'] . ", " : "",
                1 => $_POST['goalsSaved'] ? $_POST['goalsSaved'] . ", " : "",
                2 => $_POST['goalsScored'] ? $_POST['goalsScored'] . ", " : "",
                3 => $_POST['playerNumber'] ? $_POST['playerNumber'] . ", " : "",
                4 => $_POST['fantasyTeamName'] ? $_POST['fantasyTeamName'] . ", " : ""
            );
            $labels = array(
                0 => 'Name',
                1 => 'Saves',
                2 => 'Points',
                3 => 'Number',
                4 => 'Fantasy Team'
            );
            
            for ($i = 0; $i < 5; $i++) {
                $selectStr .= $arr[$i];
                if ($arr[$i]) {
                    array_push($attributeArr, $labels[$i]);
                }
            }
            if ($selectStr) {
                $proj = executePlainSQL("SELECT " . substr($selectStr, 0, -2) . " FROM Player");
            }
            betterPrintResult($proj, $attributeArr);

            OCICommit($db_conn);
        }

        function betterPrintResult($result, $arr) { //prints results from a select statement
            echo "<table cellspacing=15>";
            echo "<tr>";
            foreach ($arr as $columnName) {
                echo "<th>" . $columnName . "</th>";
            }
            echo "</tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] ."</td><td>" . $row[2] ."</td><td>" . $row[3] ."</td><td>" . $row[4] ."</td><td>" . $row[5] ."</td><td>" . $row[6] ."</td><td>" . $row[7] ."</td><td>" . $row[8] ."</td><td>" . $row[9] ."</td><td>" . $row[10] ."</td><td>" . $row[11] ."</td><td>" . $row[12] ."</td><td>" . $row[13] ."</td><td>" . $row[14] ."</td><td>" . $row[15] ."</td><td>" . $row[16] ."</td><td>" . $row[17] ."</td><td>" . $row[18] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    deleteCoach("Doobie");
                } else if (array_key_exists('signupRequest', $_POST)) {
                    handlesignup();
                } else if (array_key_exists('loginRequest', $_POST)) {
                    handleLogin();
                } else if (array_key_exists('deleteCoachRequest', $_POST)) {
                    handleDeleteCoach();
                } else if (array_key_exists('displayadvstats', $_POST)) {
                    $aggWithGroupBy_xGoals = (isset($_POST['aggWithGroupBy_xGoals'])) ? $_POST['aggWithGroupBy_xGoals'] : 1;
                    $aggWithHaving_xGoals = (isset($_POST['aggWithHaving_xGoals'])) ? $_POST['aggWithHaving_xGoals'] : 1;
                    $nestedAgg_xGames = (isset($_POST['nestedAgg_xGames'])) ? $_POST['nestedAgg_xGames'] : 1;
                    handleDisplayAdvancedStats($aggWithGroupBy_xGoals, $aggWithHaving_xGoals, $nestedAgg_xGames);
                } else if (array_key_exists('displaygenstats', $_POST)) {
                    handleDisplayGeneralStats();
                } else if (array_key_exists('displayequipstats', $_POST)) {
                    $equip_xGoals = (isset($_POST['equip_xGoals'])) ? $_POST['equip_xGoals'] : 1;
                    handleDisplayEquipment($equip_xGoals);
                } else if (array_key_exists('displayTeam', $_POST)) {
                    handleDisplayTeam();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleDisplayTeam();
                } else if (array_key_exists('selectRequest', $_POST)) {
                    handleDisplayTeam();
                } else if (array_key_exists('projectRequest', $_POST)) {
                    handleDisplayTeam();
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                } else if(array_key_exists('printTuples', $_GET)){
                    #handleprojectRequest(5);
                    #handleJoinRequest(1);
                    #groupbyaggregation(0);
                    #groupbyhaving(5);
                    #groupnested(2);



                } 

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])
            || isset($_POST['signupRequest'])  || isset($_POST['loginRequest']) || isset($_POST['deleteCoachRequest'])
            || isset($_POST['displayadvstats']) || isset($_POST['displaygenstats'])
            || isset($_POST['displayequipstats']) || isset($_POST['displayTeam']) || isset($_POST['tradeToTeam'])
            || (isset($_POST['submitSelect'])) || (isset($_POST['submitProject']))) {
            handlePOSTRequest();

        } else if (isset($_GET['countTupleRequest']) || (isset($_GET['printTupleRequest']))) {
            handleGETRequest();
            
        } else {
            display_signup_page();
        }
		?>
	</body>
</html>