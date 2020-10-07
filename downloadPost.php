<?php
	include_once ('config.php');
	include_once ('isLogin.php');
	if($_SERVER["REQUEST_METHOD"] === "POST") {
		$arr = array();

		if($log_status == 0) {
			$arr["error"] = "Not Login Yet!";
			echo json_encode($arr);
			exit(0);
		}

		$link = mysqli_connect(db_host, db_user, db_password, db_name);
		$sql = "SELECT * FROM `Users` WHERE `Id` = ".$userId;

		if($result = mysqli_query($link, $sql)) {
			if(mysqli_num_rows($result) != 1) {
				$arr["error"] = "Database user error";
				echo json_encode($arr);
				exit(0);
			}
			$user = mysqli_fetch_assoc($result);
		}
		else {
			$arr["error"] = "Database connection error";
			echo json_encode($arr);
			exit(0);
		}

		if( $user["Quota"] <= 0 ) {
			$arr["error"] = "No Quota!";
			echo json_encode($arr);
			exit(0);
		}

		$sql = "SELECT * FROM `Lessons` WHERE `Id` = ".$_POST["lesson"];

		if($result = mysqli_query($link, $sql)) {
			if(mysqli_num_rows($result) != 1) {
				$arr["error"] = "Database lesson error";
				echo json_encode($arr);
				exit(0);
			}	
			$lesson = mysqli_fetch_assoc($result);
		}
		else {
			$arr["error"] = "Database connection error";
			echo json_encode($arr);
			exit(0);
		}

		if($lesson["UserId"] == $user["UserId"]) {
			$arr["error"] = "That is your lesson!";
			echo json_encode($arr);
			exit(0);
		}

		$arr["user"] = $user;
		$arr["lesson"] = $lesson;


		// quota --
		$sql = "UPDATE `Users` SET `Quota` = `Quota` - 1 WHERE `Id` = ". $user["Id"];

		if($result = mysqli_query($link, $sql)) {
		}
		else {
			$arr["error"] = "SQL error when cost quota";
			echo json_encode($arr);
			exit(0);
		}

		// downloadtime ++

		$sql = "UPDATE `Lessons` SET `DownloadTime` = `DownloadTime` + 1 WHERE `Id` = ". $lesson["Id"];

		if($result = mysqli_query($link, $sql)) {
		}
		else {
			$arr["error"] = "SQL error when adding download time";
			echo json_encode($arr);
			exit(0);
		}
		
		// force download

		echo json_encode($arr);
		exit(0);


	}	
	else {
		$arr["error"] = "Not post!";
		echo json_encode($arr);
		exit(0);
	}
?>