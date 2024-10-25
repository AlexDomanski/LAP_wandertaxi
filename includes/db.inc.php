<?php
function dbConnect():mysqli {
	try {
		$conn = new MySQLi(DB["host"],DB["user"],DB["pwd"],DB["name"]);
		if($conn->connect_errno>0) {
			if(TESTMODUS) {
				ta("Fehler im Verbindungsaufbau: " . $conn->connect_error);
				die("Abbruch");
			}
			else {
				header("Location: errors/dbconnect.html");
			}
		}
		$conn->set_charset("utf8mb4");
	}
	catch(Exception $e) {
		if(TESTMODUS) {
			ta("Fehler im Verbindungsaufbau:");
			ta($e);
			die("Abbruch");
		}
		else {
			header("Location: errors/dbconnect.html");
		}
	}
	
	return $conn;
}

function dbQuery(mysqli $conn, string $sql):mysqli_result|bool {
	try {
		$ergebnis = $conn->query($sql);
		if($ergebnis===false) {
			if(TESTMODUS) {
				ta("Fehler im SQL-Statement: " . $conn->error . "<br>" . $sql);
				die("Abbruch");
			}
			else {
				header("Location: errors/dbquery.html");
			}
		}
	}
	catch(Exception $e) {
		if(TESTMODUS) {
			ta("Fehler im SQL-Statement:<br>" . $sql);
			ta($e);
			die("Abbruch");
		}
		else {
			header("Location: errors/dbquery.html");
		}
	}
	
	return $ergebnis;
}
?>