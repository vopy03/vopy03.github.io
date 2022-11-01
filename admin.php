<?php include 'dbconnect.php';

include 'data/userCheck.php';

if (!isset($_SESSION['language'])) $_SESSION['language'] = 'ua';
include 'data/lang/'.$_SESSION['language'].'.php';


$mainDir = $_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF'])-9);

/* login */

if (isset($_POST['enterf'])) { // Перевіряє, чи натиснута кнопка "Увійти"
$_SESSION['loginError'] = false;
$login = filter_var(trim($_POST['login']), FILTER_SANITIZE_STRING);
$password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);
$result = $pdo->prepare("SELECT password FROM users WHERE login = :login");
$params = [
"login" => $login,
]; 
$result->execute($params);
$result = $result->fetchAll();
@$pp = $result[0]['password'];
$result = $pdo->prepare("SELECT * FROM users WHERE login = :login");
$params = [
"login" => $login,
]; 
$result->execute($params); 
$row_count =$result->fetchColumn();

if ($row_count == false && isset($_POST['enterf'])) {
	$_SESSION['loginError'] = true;
	echo "<pre>В базі ноль, старий</pre>";
}
else {
	if (password_verify($password, $pp)) {
		$_SESSION['login'] = $login;
		$result = $pdo->prepare("SELECT * FROM users WHERE login = :login");
		$params = [
		"login" => $_SESSION['login'],
		]; 
		$result->execute($params); 
		$user = $result->fetchAll();
		$_SESSION = $user[0];
		header('Location: admin.php');
	} else 
		$_SESSION['loginError'] = true; 
		echo "<pre>А пароль то ти не знаєш</pre>";
	} 
}

/* login end */


/* ---------- services ---------- */


if (isset($_POST['addService'])) {
	if(isset($_FILES['picture'])) {

		$fileformat = substr($_FILES['picture']['name'], strlen($_FILES['picture']['name'])-4,strlen($_FILES['picture']['name']));
		echo "<pre>". $fileformat ."</pre>";
		echo "<pre>". var_dump($_FILES['picture']) ."</pre>";
		if(str_contains($fileformat, 'png') || str_contains($fileformat, 'jpg') || str_contains($fileformat, 'jpeg')) $ok = true;
		else {
			$ok = false;
			$formatNotOk = true;
		}
		if($ok) {

			$picPath = $_FILES['picture']['tmp_name'];
			$picName = $_FILES['picture']['name'];
			$endPath = 'img/services/'.$_POST["title"];
			$endPathToFile = $endPath . '/' . $picName .'';
			echo '<pre>';
			echo $picPath;
			echo '<br>';
			echo $endPathToFile;
			echo '</pre>';

			mkdir($endPath, 0777, true);
			move_uploaded_file($picPath, $endPathToFile);
			
		}
	}
		
	if($formatNotOk) $endPathToFile = "img/no-image.svg"; 

	if(!isset($_POST['is_hidden'])) $_POST['is_hidden'] = false;
	else $_POST['is_hidden'] = true;

	$sql = "INSERT INTO `services` (`title`, `price`, `description`, `picture`, `is_hidden`) VALUES (:title, :price, :description, :picture, :is_hidden)";
	$result = $pdo->prepare($sql);
	$params = [
		"title" => $_POST["title"], 
		"price" => $_POST["price"], 
		"description" => $_POST["description"], 
		"picture" => $endPathToFile, 
		"is_hidden" => $_POST['is_hidden'],
	]; 
	$result->execute($params);

	header('Location: admin.php');

}


if (isset($_POST['editService'])) {

	if(!isset($_POST['is_hidden'])) $_POST['is_hidden'] = false;
	else $_POST['is_hidden'] = true;


	if($_FILES['picture']['tmp_name'] != '') {


		$fileformat = substr($_FILES['picture']['name'], strlen($_FILES['picture']['name'])-4,strlen($_FILES['picture']['name']));
		if(str_contains($fileformat, 'png') || str_contains($fileformat, 'jpg') || str_contains($fileformat, 'jpeg')) $ok = true;
		else {
			$ok = false;
			$formatoNotOk = true;
		}
		if($ok) {

			remDir("img/services/". $_POST['oldTitle']);

			$picPath = $_FILES['picture']['tmp_name'];
			$picName = $_FILES['picture']['name'];
			$endPath = 'img/services/'.$_POST["title"];
			$endPathToFile = $endPath . '/' . $picName .'';

			mkdir($endPath, 0777, true);
			move_uploaded_file($picPath, $endPathToFile);
			
		}


		$sql = "UPDATE `services` SET `title` = :title, `price` = :price, `description` = :description, `picture` = :picture, `is_hidden` = :is_hidden WHERE `id` = :id";
		$params = [
		"id" => $_POST["sId"],
		"title" => $_POST['title'],
		"price" => $_POST['price'],
		"description" => $_POST['description'],
		"picture" => $endPathToFile,
		"is_hidden" => $_POST['is_hidden'],
		]; 
		
		$result = $pdo->prepare($sql);
		$result->execute($params);

	}
	else {
		$sql = "UPDATE `services` SET `title` = :title, `price` = :price, `description` = :description, `is_hidden` = :is_hidden WHERE `id` = :id";
		$params = [
		"id" => $_POST["sId"],
		"title" => $_POST['title'],
		"price" => $_POST['price'],
		"description" => $_POST['description'],
		"is_hidden" => $_POST['is_hidden'],
		]; 
		
		$result = $pdo->prepare($sql);
		$result->execute($params);
	}


	header('Location: admin.php');

}


if (isset($_POST['hideService'])) {

	$result = $pdo->prepare("SELECT is_hidden FROM services WHERE id = :id");
	$params = [
	"id" => $_POST['sId'],
	]; 
	$result->execute($params);
	$r = $result->fetchAll();
	$r = $r[0][0];

	if ($r == "0") $r = true;
	else $r = false;

	$sql = "UPDATE `services` SET `is_hidden` = :is_hidden WHERE `id` = :id";

	$result = $pdo->prepare($sql);
	$params = [
	"id" => $_POST['sId'],
	"is_hidden" => $r,
	]; 
	$result->execute($params);

	header('Location: admin.php');

}


if (isset($_POST['deleteService'])) {

	remDir("img/services/". $_POST['sTitle']);

	$sql = "DELETE FROM services WHERE id = :id";

	$statement = $pdo->prepare($sql);
	$params = [
	"id" => $_POST['sId'],
	];
	$statement->execute($params);

}

/*services end*/




/*projects*/

if (isset($_POST['addProject'])) {

	$allPictures = '';

	if(isset($_FILES['picture'])) {

		$fileformat = substr($_FILES['picture']['name'], strlen($_FILES['picture']['name'])-4,strlen($_FILES['picture']['name']));
		echo "<pre>". $fileformat ."</pre>";
		echo "<pre>". var_dump($_FILES) ."</pre>";
		if(str_contains($fileformat, 'png') || str_contains($fileformat, 'jpg') || str_contains($fileformat, 'jpeg')) $ok = true;
		else {
			$ok = false;
			$formatNotOk = true;
		}
		if($ok) {

			$picPath = $_FILES['picture']['tmp_name'];
			$picName = $_FILES['picture']['name'];
			$endPath = 'img/services/'.$_POST["title"];
			$endPathToFile = $endPath . '/' . $picName .'';
			echo '<pre>';
			echo $picPath;
			echo '<br>';
			echo $endPathToFile;
			echo '</pre>';

			mkdir($endPath, 0777, true);
			move_uploaded_file($picPath, $endPathToFile);
			
		}

		$allPictures .= $endPathToFile;
	}

	

	for($i = 0; $i <= intval($_POST['lastElId']); $i++) {

		if (isset($_FILES['extraPicture'.$i]) && @!($_FILES['extraPicture'.$i]['name'] == '' ) ) {

			$fileFMT = substr($_FILES['extraPicture'.$i]['name'], strlen($_FILES['extraPicture'.$i]['name'])-4,strlen($_FILES['extraPicture'.$i]['name']));
			if(str_contains($fileFMT, 'png') || str_contains($fileFMT, 'jpg') || str_contains($fileFMT, 'jpeg')) $oke = true;
			else {
				$oke = false;
			}
			if($oke) {

				$picPath = $_FILES['extraPicture'.$i]['tmp_name'];
				$picName = $_FILES['extraPicture'.$i]['name'];
				$endPath = 'img/projects/'.$_POST["title"].'/extraPics';
				$endPathToFile = $endPath . '/' . $_FILES['extraPicture'.$i]["name"] .'';
				echo '<pre>';
				echo $picPath;
				echo '<br>';
				echo $endPathToFile;
				echo '</pre>';

				if ($i == 1 and $allPictures != '') $allPictures .= ',' . $endPathToFile . ',';
				else if ($i == intval($_POST['lastElId'])) $allPictures .= $endPathToFile;
				else $allPictures .= $endPathToFile.',';

				@mkdir($endPath, 0777, true);
				move_uploaded_file($picPath, $endPathToFile);
				
			}

		}


	}

	// echo $allPictures;

	$arrPics = explode(',', $allPictures);

		
	if($formatNotOk) {
		if (intval($_POST['lastElId']) != 0) $endPathToFile = $arrPics[0];
		else $endPathToFile = "img/no-image.svg";
	}

	if(!isset($_POST['is_hidden'])) $_POST['is_hidden'] = false;
	else $_POST['is_hidden'] = true;

	$sql = "INSERT INTO `projects` (`title`, `description`, `picture`, `pictures`, `is_hidden`) VALUES (:title, :description, :picture, :pictures, :is_hidden)";
	$result = $pdo->prepare($sql);
	$params = [
		"title" => $_POST["title"], 
		"description" => $_POST["description"], 
		"picture" => $endPathToFile,
		"pictures" => $allPictures, 
		"is_hidden" => $_POST['is_hidden'],
	]; 
	$result->execute($params);


	header('Location: admin.php?la=proj');
}
if (isset($_POST['editProject'])) {

	var_dump($_POST);
	var_dump($_FILES);

	$result = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
	$params = [
	"id" => $_POST['pId'],
	]; 
	$result->execute($params);
	$r = $result->fetchAll();
	$projs = $r[0];

	$projsArrPics = explode(',', $projs['pictures']);
	$projsEPics = [];


	$allPictures = '';

	if(isset($_FILES['picture']) && @$_FILES['picture']['tmp_name'] != '') {

		$fileformat = substr($_FILES['picture']['name'], strlen($_FILES['picture']['name'])-4,strlen($_FILES['picture']['name']));
		echo "<pre>". $fileformat ."</pre>";
		echo "<pre>". var_dump($_FILES) ."</pre>";
		if(str_contains($fileformat, 'png') || str_contains($fileformat, 'jpg') || str_contains($fileformat, 'jpeg')) $ok = true;
		else {
			$ok = false;
			$formatNotOk = true;
		}
		if($ok) {

			$picPath = $_FILES['picture']['tmp_name'];
			$picName = $_FILES['picture']['name'];
			$endPath = 'img/services/'.$_POST["title"];
			$endPathToFile = $endPath . '/' . $picName .'';
			echo '<pre>';
			echo $picPath;
			echo '<br>';
			echo $endPathToFile;
			echo '</pre>';
			echo "ok eptf:". $endPathToFile;
			array_push($projsEPics, $endPathToFile);

			@mkdir($endPath, 0777, true);
			move_uploaded_file($picPath, $endPathToFile);
			
		}	
	}

	if($_FILES['picture']['tmp_name'] == '') {
		$endPathToFile = $projs['picture'];
		array_push($projsEPics, $endPathToFile);
	}


	
	$shift = 1;
	for($i = 0; $i <= intval($_POST['lastElId']); $i++) {

		if (isset($_FILES['extraPicture'.$i]) && @!($_FILES['extraPicture'.$i]['name'] == '' ) ) {

			$fileFMT = substr($_FILES['extraPicture'.$i]['name'], strlen($_FILES['extraPicture'.$i]['name'])-4,strlen($_FILES['extraPicture'.$i]['name']));
			if(str_contains($fileFMT, 'png') || str_contains($fileFMT, 'jpg') || str_contains($fileFMT, 'jpeg')) $oke = true;
			else {
				$oke = false;
			}
			if($oke) {

				$picPath = $_FILES['extraPicture'.$i]['tmp_name'];
				$picName = $_FILES['extraPicture'.$i]['name'];
				$endPath = 'img/projects/'.$_POST["title"].'/extraPics';
				$endPathToFileEx = $endPath . '/' . $_FILES['extraPicture'.$i]["name"] .'';
				echo '<pre>';
				echo $picPath;
				echo '<br>';
				echo $endPathToFile;
				echo '</pre>';

				$shift++;

				array_push($projsEPics, $endPathToFileEx);

				@mkdir($endPath, 0777, true);
				move_uploaded_file($picPath, $endPathToFileEx);
				
			}

		}
		if (isset($_FILES['extraPicture'.$i]) && @($_FILES['extraPicture'.$i]['name'] == '' )) {
			array_push($projsEPics, $projsArrPics[$shift]);
		}



	}


	$allPictures = implode(',', $projsEPics);

		
	if(@!$oke) {
		if (intval($_POST['lastElId']) != 0 && $endPathToFile == '') $endPathToFile = $projsEPics[0];
	}

	if(!isset($_POST['is_hidden'])) $_POST['is_hidden'] = false;
	else $_POST['is_hidden'] = true;

	$sql = "UPDATE `projects` SET `title` = :title, `description` = :description, `picture` = :picture, `pictures` = :pictures, `is_hidden` = :is_hidden WHERE `id` = :id";

	$result = $pdo->prepare($sql);
	$params = [
		"id" => $_POST["pId"],
		"title" => $_POST["title"], 
		"description" => $_POST["description"], 
		"picture" => $endPathToFile,
		"pictures" => $allPictures, 
		"is_hidden" => $_POST['is_hidden'],
	]; 
	$result->execute($params);



	header('Location: admin.php?la=proj');
}
if (isset($_POST['hideProject'])) {

	$result = $pdo->prepare("SELECT is_hidden FROM projects WHERE id = :id");
	$params = [
	"id" => $_POST['pId'],
	]; 
	$result->execute($params);
	$r = $result->fetchAll();
	$r = $r[0][0];

	if ($r == "0") $r = true;
	else $r = false;

	$sql = "UPDATE `projects` SET `is_hidden` = :is_hidden WHERE `id` = :id";

	$result = $pdo->prepare($sql);
	$params = [
	"id" => $_POST['pId'],
	"is_hidden" => $r,
	]; 
	$result->execute($params);


	header('Location: admin.php?la=proj');
}
if (isset($_POST['deleteProject'])) {

	remDir("img/projects/". $_POST['pTitle']);

	$sql = "DELETE FROM projects WHERE id = :id";

	$statement = $pdo->prepare($sql);
	$params = [
	"id" => $_POST['pId'],
	];
	$statement->execute($params);

	header('Location: admin.php?la=proj');
}

/*projects end*/


/*reviews*/

if (isset($_POST['addReview'])) {

	if(isset($_FILES['picture'])) {

		$fileformat = substr($_FILES['picture']['name'], strlen($_FILES['picture']['name'])-4,strlen($_FILES['picture']['name']));
		echo "<pre>". var_dump($_POST) ."</pre>";
		echo "<pre>". var_dump($_FILES['picture']) ."</pre>";
		if(str_contains($fileformat, 'png') || str_contains($fileformat, 'jpg') || str_contains($fileformat, 'jpeg')) $ok = true;
		else {
			$ok = false;
			$formatNotOk = true;
		}
		if($ok) {

			$picPath = $_FILES['picture']['tmp_name'];
			$picName = $_FILES['picture']['name'];
			$endPath = 'img/reviews/'.$_POST["fname"].'_'.$_POST["lname"];
			$endPathToFile = $endPath . '/' . $picName .'';
			echo '<pre>';
			echo $picPath;
			echo '<br>';
			echo $endPathToFile;
			echo '</pre>';

			mkdir($endPath, 0777, true);
			move_uploaded_file($picPath, $endPathToFile);
			
		}
	}
		
		if($formatNotOk) $endPathToFile = "img/no-image.svg"; 

		if(!isset($_POST['is_hidden'])) $_POST['is_hidden'] = false;
		else $_POST['is_hidden'] = true;

		$sql = "INSERT INTO `reviews` (`fname`, `lname`, `position`, `description`, `picture`, `is_hidden`) VALUES (:fname, :lname, :position, :description, :picture, :is_hidden)";
		$result = $pdo->prepare($sql);
		$params = [
			"fname" => $_POST["fname"], 
			"lname" => $_POST["lname"], 
			"position" => $_POST["position"], 
			"description" => $_POST["description"], 
			"picture" => $endPathToFile, 
			"is_hidden" => $_POST['is_hidden'],
		]; 
		$result->execute($params);

		header('Location: admin.php?la=rev');

}
if (isset($_POST['editReview'])) {

	if(!isset($_POST['is_hidden'])) $_POST['is_hidden'] = false;
	else $_POST['is_hidden'] = true;

	echo "<pre>". var_dump($_POST) ."</pre>";
	echo "<pre>". var_dump($_FILES) ."</pre>";

	if($_FILES['picture']['name'] != '') {


		$fileformat = substr($_FILES['picture']['name'], strlen($_FILES['picture']['name'])-4,strlen($_FILES['picture']['name']));
		if(str_contains($fileformat, 'png') || str_contains($fileformat, 'jpg') || str_contains($fileformat, 'jpeg')) $ok = true;
		else {
			$ok = false;
			$formatoNotOk = true;
		}
		if($ok) {

			remDir("img/reviews/". $_POST['oldfname']."_".$_POST['oldlname']);

			$picPath = $_FILES['picture']['tmp_name'];
			$picName = $_FILES['picture']['name'];
			$endPath = 'img/reviews/'. $_POST["fname"].'_'.$_POST["lname"];
			$endPathToFile = $endPath . '/' . $picName .'';

			mkdir($endPath, 0777, true);
			move_uploaded_file($picPath, $endPathToFile);
			
		}


		$sql = "UPDATE `reviews` SET `fname` = :fname, `lname` = :lname, `position` = :position, `description` = :description, `picture` = :picture, `is_hidden` = :is_hidden WHERE `id` = :id";
		$params = [
		"id" => $_POST["rId"],
		"fname" => $_POST['fname'],
		"lname" => $_POST['lname'],
		"position" => $_POST['position'],
		"description" => $_POST['description'],
		"picture" => $endPathToFile,
		"is_hidden" => $_POST['is_hidden'],
		]; 
		
		$result = $pdo->prepare($sql);
		$result->execute($params);

	}
	else {
		$sql = "UPDATE `reviews` SET `fname` = :fname, `lname` = :lname, `position` = :position, `description` = :description, `is_hidden` = :is_hidden WHERE `id` = :id";
		$params = [
		"id" => $_POST["rId"],
		"fname" => $_POST['fname'],
		"lname" => $_POST['lname'],
		"position" => $_POST['position'],
		"description" => $_POST['description'],
		"is_hidden" => $_POST['is_hidden'],
		]; 
		
		$result = $pdo->prepare($sql);
		$result->execute($params);
	}


	header('Location: admin.php?la=rev');

}
if (isset($_POST['hideReview'])) {

	$result = $pdo->prepare("SELECT is_hidden FROM reviews WHERE id = :id");
	$params = [
	"id" => $_POST['rId'],
	]; 
	$result->execute($params);
	$r = $result->fetchAll();
	$r = $r[0][0];

	if ($r == "0") $r = true;
	else $r = false;

	$sql = "UPDATE `reviews` SET `is_hidden` = :is_hidden WHERE `id` = :id";

	$result = $pdo->prepare($sql);
	$params = [
	"id" => $_POST['rId'],
	"is_hidden" => $r,
	]; 
	$result->execute($params);

	header('Location: admin.php?la=rev');

}
if (isset($_POST['deleteReview'])) {

	remDir("img/reviews/". $_POST['fname']."_".$_POST['lname']);

	$sql = "DELETE FROM reviews WHERE id = :id";

	$statement = $pdo->prepare($sql);
	$params = [
	"id" => $_POST['rId'],
	];
	$statement->execute($params);

	header('Location: admin.php?la=rev');

}

/*reviews end*/



/*contacts*/
if (isset($_POST['updDataOnPage'])) {
	$sql = "UPDATE `basic_info` SET `phone` = :phone, `whatsapp` = :whatsapp, `facebook` = :facebook, `fb_link` = :fb_link, `mail` = :mail, `address` = :address, `about_us` = :about_us";

	$result = $pdo->prepare($sql);
	$params = [
	"phone" => $_POST['phone'],
	"whatsapp" => $_POST['whatsapp'],
	"facebook" => $_POST['facebook'],
	"fb_link" => $_POST['fb_link'],
	"mail" => $_POST['mail'],
	"address" => $_POST['address'],
	"about_us" => $_POST['about_us'],
	]; 
	$result->execute($params);

	header('Location: admin.php?la=cont');
}
/*contacts end*/


/*settings*/

if (isset($_POST['authToggleLang'])) {
	if ($_SESSION['language'] == 'ua') $_SESSION['language'] = 'ch';
	else $_SESSION['language'] = 'ua';
	header('Location: admin.php');
} 



if (isset($_POST['changeLang'])) {

	$result = $pdo->prepare("SELECT * FROM users");
	$result->execute();
	$usr = $result->fetchAll();
	$usr = $usr[0];

	$sql = "UPDATE `users` SET `language` = :language WHERE `login` = :login";

	$result = $pdo->prepare($sql);
	$params = [
	"login" => $_SESSION['login'],
	"language" => $_POST['language'],
	]; 
	$result->execute($params);

	$_SESSION['language'] = $_POST['language'];


	header('Location: admin.php?la=sett');
}



if (isset($_POST['updateUserPassword'])) {

	$login = $_SESSION['login'];
	$oldpass = $_POST['oldpass'];
	$newpass = filter_var($_POST['newpass'], FILTER_SANITIZE_STRING);

	$result = $pdo->prepare("SELECT password FROM users WHERE login = :login");
	$params = [
	"login" => $login,
	]; 
	$result->execute($params);
	$result = $result->fetchAll();

	@$pp = $result[0]['password'];

	if (password_verify($oldpass, $pp)) {
		$oldpassVerify = true;
		// зміна паролю
		$sql = "UPDATE `users` SET `password` = :password WHERE `login` = :login";

		$result = $pdo->prepare($sql);
		$params = [
		"login" => $login,
		"password" => password_hash($newpass, PASSWORD_DEFAULT),
		]; 
		$result->execute($params);

		//header('Location: http:../settings');
	} else $oldpassVerify = false;
}


if(isset($_POST['updateLogin'])) {

		$login = $_POST['login'];

		$result = $pdo->prepare("SELECT * FROM users WHERE login = :login");
		$params = [
		"login" => $_SESSION['login'],
		]; 
		$result->execute($params); 
		$row_count = $result->fetchAll();
		$row_count = count($row_count);
		if ($login != $_SESSION['login']) {
		$sameName = false;


		$sql = "UPDATE `users` SET `login` = :login WHERE `login` = :oldLogin";
		$params = [
		"login" => $login,
		"oldLogin" => $_SESSION['login'],
		];
		$prepare = $pdo -> prepare($sql);
		$prepare -> execute($params);

		$_SESSION['login'] = $_POST['login'];

		$result = $pdo->prepare("SELECT * FROM users WHERE login = :login");
		$params = [
		"login" => $_SESSION['login'],
		]; 
		$result->execute($params); 
		$user = $result->fetchAll();

		header('Location: admin.php?la=sett');

		} else $sameName = true;
		if ($sameName) {
			echo "<pre>То саме імя</pre>";
		}
}







/*settings end*/






?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/x-icon" href="img/favicon.png">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/admin.css">
	<link rel="stylesheet" type="text/css" href="css/media.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script defer>
		var picId = 0;
		tooBigMsg = 'Розмір файлу повинен бути не більше ніж ';
		window.onload = function() {
			if(window.innerWidth < 600) {
				document.querySelector('.side-panel .sp-header span').click();
			}
		    var divLoader = document.getElementById('main-loader');
		    divLoader.classList.add('hidden-loader');
		    setInterval(function() {
		        divLoader.remove();
		    },1300);

		    var showPassButton = document.getElementById('auth-show-pass-button');
			var showPassButtonIcon = document.getElementById('auth-show-pass-button-icon');

			var passInput = document.getElementById('auth-pass-input')

			function showHidePass() {
				if (passInput.type == 'password') {
					passInput.type = 'text';
					showPassButtonIcon.innerHTML = 'visibility_off';
				}
				else if (passInput.type == 'text') {
					passInput.type = 'password';
					showPassButtonIcon.innerHTML = 'visibility';
				}
			}
			showPassButton.onclick = function() {
				showHidePass();
			}
	    }

	</script>
	<script src="js/admin.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous" defer></script>
	<title>Admin Panel</title>
</head>
<body>
	<div class="mLoader" id="main-loader">
		<div class="spinner-border text-light" role="status">
		  <span class="visually-hidden">Loading...</span>
		</div>
	</div>

	<?php if(!isLogged()) { ?>

	<!-- auth form -->

	<form method="POST" action="admin.php">
		<label for="auth-toggle-lang" class="auth-change-lang">
			<p>
				<span class="material-icons">language</span>
				<span><?php if($_SESSION['language'] == 'ua') echo "Če"; else echo "Ua"  ?>
				</span>
			</p>
		</label>
		<input type="submit" id="auth-toggle-lang" name="authToggleLang" hidden>
	</form>


	<div class="admin-auth-form-container">
		<form class="admin-auth-form" method="POST" action="admin.php">
		  <div class="mb-3">
		    <label for="exampleInputLogin" class="form-label"><?php echo $authLogin ?></label>
		    <input type="text" class="form-control" name="login" id="exampleInputLogin" autocomplete="off" aria-describedby="emailHelp"required>
		    <div class="form-text"><?php echo $authLoginUnder ?></div>
		  </div>
		  <div class="mb-3">
		    <label for="auth-pass-input" class="form-label"><?php echo $authPass ?></label>
		    <div class="input-group">
			    <input type="password" class="form-control" autocomplete="off" name="password" required id="auth-pass-input">
			    <button class="input-group-text" type="button" id="auth-show-pass-button">
			    	<span class="material-icons" id="auth-show-pass-button-icon">visibility</span>
			    </button>
		    </div>
		  </div>
		  <button type="submit" name="enterf" class="btn btn-primary"><?php echo $authLoginBtn ?></button>
		</form>
	</div>

	<!-- auth form end -->

	<?php } else { 
 	
 	/* --- pull up the data --- */

 	/* user */

	$result = $pdo->prepare("SELECT * FROM users");
	$result->execute();
	$user = $result->fetchAll();
	$user = $user[0];

	/* user end */

	/* services */

	$result = $pdo->prepare("SELECT * FROM services");
	$result->execute();
	$allServices = $result->fetchAll();

	/* services end */

	/* projects */
	$result = $pdo->prepare("SELECT * FROM projects");
	$result->execute();
	$allProjects = $result->fetchAll();
	/* projects end */

	/* reviews */
	$result = $pdo->prepare("SELECT * FROM reviews");
	$result->execute();
	$allReviews = $result->fetchAll();
	/* reviews end */


	/* contacts */
	$result = $pdo->prepare("SELECT * FROM basic_info");
	$result->execute();
	$contactsinfo = $result->fetchAll();
	$contactsinfo = $contactsinfo[0];
	/* contacts end */






	if (isset($_GET['la'])) {
		if ($_GET['la'] == 'proj') { ?>
			<script type="text/javascript">
	  			setTimeout(function() {
	  				document.querySelectorAll(".sp-option")[1].click();
	  			},300);
	  			
	  		</script>
  		<?php
		}
		if ($_GET['la'] == 'rev') { ?>
			<script type="text/javascript">
	  			setTimeout(function() {
	  				document.querySelectorAll(".sp-option")[2].click();
	  			},300);
	  			
	  		</script>
  		<?php
		}
		if ($_GET['la'] == 'cont') { ?>
			<script type="text/javascript">
	  			setTimeout(function() {
	  				document.querySelectorAll(".sp-option")[3].click();
	  			},100);
	  			
	  		</script>
  		<?php
		}
		if ($_GET['la'] == 'sett') { ?>
			<script type="text/javascript">
	  			setTimeout(function() {
	  				document.querySelectorAll(".sp-option")[4].click();
	  			},300);
	  			
	  		</script>
  		<?php
		}
	}







	?>

	<div class="side-panel" id="side-panel">
		<div class="sp-header">
			<span class="material-icons" onclick="hideSP(this.parentNode.parentNode)">menu</span>
		</div>

		<div class="sp-option sp-selected">
			<span class="material-icons sp-option-icon">construction</span>
			<span class="sp-option-label"><?php echo $spServices ?></span>
		</div>
		<div class="sp-option sp-selected">
			<span class="material-icons sp-option-icon">apartment</span>
			<span class="sp-option-label"><?php echo $spProjects ?></span>
		</div>
		<div class="sp-option">
			<span class="material-icons sp-option-icon">reviews</span>
			<span class="sp-option-label"><?php echo $spReviews ?></span>
		</div>




		<div class="sp-bottom-option">
			<div class="sp-option">
				<span class="material-icons sp-option-icon">article</span>
				<span class="sp-option-label"><?php echo $spContacts ?></span>
			</div>
			<div class="sp-option">
				<span class="material-icons sp-option-icon">settings</span>
				<span class="sp-option-label"><?php echo $spSettings ?></span>
			</div>
		</div>
	</div>
	<content class="content">

		<div class="top-panel">
			<a href="../" target="_blank" class="btn btn-danger"><?php echo $tpOpenInNew ?><span class="material-icons">open_in_new</span></a>
			<form method="POST" action="admin.php">
			<input type="submit" name="logout" id="logoutInp"  hidden>
			<label for="logoutInp" class="btn btn-danger"><?php echo $tpLogout ?><span class="material-icons">logout</span></label>
			</form>
		</div>

		<div class="main-content-section">
			<div class="actual-content">

				


				<!-- goods content  -->
				<div class="option-content">
					<h3><span class="material-icons">construction</span><?php echo $spServices ?></h3>
					<div class="oc-header">
						<a class="btn" data-bs-toggle="modal" data-bs-target="#add-goods-modal"><span class="material-icons">add</span><?php echo $sBtnAdd ?></a>
					</div>

					<!-- add work modal -->

					<div class="modal fade " id="add-goods-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-m">
							<div class="modal-content">
							  <div class="modal-header">
							    <h5 class="modal-title" id="staticBackdropLabel"><span class="material-icons">add</span><?php echo $sModalHeader ?></h5>
							    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							  </div>
							  <div class="modal-body">
							    <form method="POST" action="admin.php" enctype="multipart/form-data">
							    	<div class="short-data-inputs">
							    		<div class="div-img">
								        	<input type="file" id="img-input-add-serv"  accept="image/png, image/jpeg, image/jpg" name="picture" hidden onchange="if(verifyFileSize(this, tooBigMsg)) showUPMain(this);">
								        	<label for="img-input-add-serv" class="img-input-label">
								        		<img src="img/no-image.svg">
								        	</label>
							    		</div>
									
										<div class="div-short-info">
											<div class="input-group">
												<input type="text" class="form-control" name="title" pattern=".{5,}" title="<?php echo $ModalItemNameHint ?>" required placeholder="<?php echo $ModalItemName ?>">
											</div>
										
											<div class="input-group">
												<input type="number" class="form-control" name="price" required placeholder="<?php echo $ModalItemPrice ?>">
												<span class="input-group-text">Kč</span>
											</div>
											<div class="input-group">
												<div class="form-check">
												  <input class="form-check-input" type="checkbox" name='is_hidden' id="flexCheck-add">
												  <label class="form-check-label" for="flexCheck-add">
												    <?php echo $ModalItemHideLabel ?>
												  </label>
												</div>
											</div>
										</div>
									</div>
									<textarea class="form-control" placeholder="<?php echo $ModalDesc ?>" name="description"></textarea>
							    	
							    
							  </div>
							  <div class="modal-footer">
							    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $ModalBtnClose ?></button>
							    <input type="submit" name="addService" class="btn btn-primary" value="<?php echo $ModalBtnAdd ?>">
							    </form>
							  </div>
							</div>
						</div>
					</div>

					<!-- add work modal end -->

					<div class="oc-card-container">

						<?php foreach ($allServices as $allService => $service) { ?>


						<div class="card text-white bg-dark" style="max-width: 18rem;">

							<img src="<?php echo $service['picture'] ?>" class="card-img-top" alt="...">
							<div class="card-body">
								<h5 class="card-title"><?php echo $service['title'] ?><i class="serv-title-price"><?php echo $service['price'] ?> Kč</i></h5>
								<p class="card-text">
									<?php 
										if(strlen($service['description']) > 50 ) echo mb_substr($service['description'], 0 , 50)."...";
										else echo $service['description'];
									?>
								</p>
							</div>
							<div class="oc-card-actions-list">
								
								<a data-bs-toggle="modal" data-bs-target="#edit-serv-modal<?php echo $service['id'] ?>" class="oc-list-action" title="<?php echo $CALTEdit ?>">
									<span class="material-icons-outlined" >edit</span>
								</a>
								<form method="POST" action="admin.php">
									<label for="toggleServVisibility<?php echo $service['id'] ?>" class="oc-list-action" >
										<?php if($service['is_hidden'] == false) { ?>
										<span class="material-icons" title="<?php echo $CALTHide ?>" >visibility_off</span>
										<?php } else { ?>
										<span class="material-icons" title="<?php echo $CALTShow ?>" >visibility</span>
										<?php } ?>
									</label>
									<input type="number" name="sId" value="<?php echo $service['id'] ?>" hidden>
									<input type="submit" id="toggleServVisibility<?php echo $service['id'] ?>" name="hideService" hidden>
								</form>
								<a data-bs-toggle="modal" data-bs-target="#delete-serv-modal<?php echo $service['id'] ?>" class="oc-list-action" title="<?php echo $CALTDelete ?>">
									<span class="material-icons" >delete</span>
								</a>
							</div>

			      			<div class="modal fade" id="edit-serv-modal<?php echo $service['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
							  <div class="modal-dialog modal-dialog-centered modal-m">
							    <div class="modal-content">
							      <div class="modal-header">
							        <h5 class="modal-title" id="staticBackdropLabel"><span class="material-icons">edit</span><?php echo $sModalEditHeader ?></h5>
							        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							      </div>
							      <div class="modal-body">
							        <form method="POST" action="admin.php" enctype="multipart/form-data">
							        	<div class="short-data-inputs">
							        		<div class="div-img">
									        	<input type="file" id="img-input-edit-serv<?php echo $service['id'] ?>" type="image/png, image/jpg, image/jpeg" name="picture" hidden onchange="if(verifyFileSize(this, tooBigMsg)) showUPMain(this);">
									        	<label for="img-input-edit-serv<?php echo $service['id'] ?>" class="img-input-label">
									        		<img src="<?php echo $service['picture'] ?>">
									        	</label>
							        		</div>
										
											<div class="div-short-info">
												<div class="input-group">
													<input type="text" class="form-control" name="title" pattern=".{5,}" title="<?php echo $ModalItemNameHint ?>" required value="<?php echo $service['title'] ?>" placeholder="<?php echo $ModalItemName ?>" >
												</div>
											
												<div class="input-group">
													<input type="number" class="form-control" name="price" required value="<?php echo $service['price'] ?>" placeholder="<?php echo $ModalItemPrice ?>">
													
													<span class="input-group-text">Kč</span>
												</div>
												<div class="input-group">
													<div class="form-check">
													  <input class="form-check-input" type="checkbox" <?php if($service['is_hidden'] == true) echo "checked"; ?> name="is_hidden"  id="flexCheckDefault<?php echo $service['id'] ?>">
													  <label class="form-check-label" for="flexCheckDefault<?php echo $service['id'] ?>">
													    <?php echo $ModalItemHideLabel ?>
													  </label>
													</div>
												</div>
											</div>
										</div>
										<textarea class="form-control" name="description" placeholder="<?php echo $ModalDesc ?>"><?php echo $service['description'] ?></textarea>
							        	
							        
							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $ModalBtnClose ?></button>
							        <input type="number" name="sId" value="<?php echo $service['id'] ?>" hidden>
							        <input type="number" name="oldTitle" value="<?php echo $service['title'] ?>" hidden>
							        <input type="submit" name="editService" class="btn btn-warning" value="<?php echo $ModalBtnEdit ?>">
							        </form>
							      </div>
							    </div>
							  </div>
							</div>

							<!-- delete service -->

							<div class="modal fade " id="delete-serv-modal<?php echo $service['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
							  <div class="modal-dialog modal-dialog-centered modal-m">
							    <div class="modal-content">
							      <div class="modal-header">
							        <h4 class="modal-title" id="staticBackdropLabel"><span class="material-icons">delete</span><?php echo $sModalDeleteHeader ?></h4>
							        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							      </div>
							      <div class="modal-body">
							      	<p><?php echo $ModalDeleteText ?></p>
							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"><?php echo $ModalBtnClose ?></button>
							        <form method="POST" action="admin.php">
							        <input type="text" name="sTitle" value="<?php echo $service['title'] ?>" hidden>
						        	<input type="number" name="sId" value="<?php echo $service['id'] ?>" hidden>
							        <input type="submit" name="deleteService" class="btn btn-danger" value="<?php echo $ModalBtnDelete ?>">
							        </form>

							      </div>
							    </div>
							  </div>
							</div>

						</div>

						<?php } ?>
						
					</div>
				</div>


				
				<!-- service content end  -->

				
				<div class="option-content">
					<h3><span class="material-icons">apartment</span><?php echo $spProjects ?></h3>
					<br>

					<div class="oc-header">
						<a class="btn" data-bs-toggle="modal" data-bs-target="#add-project-modal"><span class="material-icons">add</span><?php echo $pBtnAdd ?></a>
					</div>

					<!-- add project modal -->

					<div class="modal fade " id="add-project-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered modal-m">
							<div class="modal-content">
							  <div class="modal-header">
							    <h5 class="modal-title" id="staticBackdropLabel"><span class="material-icons">add</span><?php echo $pModalHeader ?></h5>
							    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							  </div>
							  <div class="modal-body">
							    <form method="POST" action="admin.php" enctype="multipart/form-data">
							    	<div class="short-data-inputs">
							    		<div class="div-img">
								        	<input type="file" id="img-input" accept="image/png, image/jpg, image/jpeg" name="picture" hidden onchange="if(verifyFileSize(this, tooBigMsg)) showUPMain(this);">
								        	<label for="img-input" class="img-input-label">
								        		<img src="img/no-image.svg">
								        	</label>
							    		</div>
									
										<div class="div-short-info">
											<div class="input-group">
												<input type="text" class="form-control" name="title" pattern=".{5,}" title="<?php echo $ModalItemNameHint ?>" required placeholder="<?php echo $ModalItemName ?>" >
											</div>
											<div class="input-group">
												<div class="form-check">
												  <input class="form-check-input" type="checkbox" name="is_hidden" id="flexCheck-add-proj">
												  <label class="form-check-label" for="flexCheck-add-proj">
												    <?php echo $ModalItemHideLabel ?>
												  </label>
												</div>
											</div>
										</div>
									</div>
									<h6><?php echo $pModalOtherPics ?></h6>
									<div class="div-other-pics">
									</div>
								  	<div class="add-pic-btn-div" onclick="addPicInput(this, 'Add')">
								  		<span class="add-pic-btn"><span class="material-icons">add</span><?php echo $pModalAddOtherPicsBtn ?></span>
								  	</div>
									
								<textarea class="form-control" name="description" placeholder="<?php echo $ModalDesc ?>"></textarea>	    
							  </div>
							  <div class="modal-footer">
							    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $ModalBtnClose ?></button>
							    <input type="num" name="lastElId" value="0" hidden>
							    <input type="submit" class="btn btn-primary" name="addProject" value="<?php echo $ModalBtnAdd ?>">
							    </form>
							  </div>
							</div>
						</div>
					</div>

					<!-- add project modal end -->

					<div class="oc-card-container">

					<?php foreach ($allProjects as $allProject => $project) { ?>


						<div class="card text-white bg-dark" style="max-width: 18rem;">

							<img src="<?php echo $project['picture'] ?>" class="card-img-top" alt="...">
							<div class="card-body">
								<h5 class="card-title"><?php echo $project['title'] ?></h5>
								<p class="card-text">
									<?php 
										if(strlen($project['description']) > 50 ) echo mb_substr($project['description'], 0 , 50)."...";
										else echo $project['description'];
									?>
								</p>
							</div>
							<div class="oc-card-actions-list">
								<a data-bs-toggle="modal" data-bs-target="#edit-proj-modal<?php echo $project['id'] ?>" class="oc-list-action" title="<?php echo $CALTEdit ?>">
									<span class="material-icons-outlined" >edit</span>
								</a>
								<form method="POST" action="admin.php">
									<label for="toggleProjVisibility<?php echo $project['id'] ?>" class="oc-list-action" >
										<?php if($project['is_hidden'] == false) { ?>
										<span class="material-icons" title="<?php echo $CALTHide ?>" >visibility_off</span>
										<?php } else { ?>
										<span class="material-icons" title="<?php echo $CALTShow ?>" >visibility</span>
										<?php } ?>
									</label>
									<input type="number" name="pId" value="<?php echo $project['id'] ?>" hidden>
									<input type="submit" id="toggleProjVisibility<?php echo $project['id'] ?>" name="hideProject" hidden>
								</form>
								<a data-bs-toggle="modal" data-bs-target="#delete-proj-modal<?php echo $project['id'] ?>" class="oc-list-action" title="<?php echo $CALTDelete ?>">
									<span class="material-icons" >delete</span>
								</a>
							</div>

			      			<div class="modal fade" id="edit-proj-modal<?php echo $project['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
							  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-m">
							    <div class="modal-content">
							      <div class="modal-header">
							        <h5 class="modal-title" id="staticBackdropLabel"><span class="material-icons">edit</span><?php echo $pModalEditHeader ?></h5>
							        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							      </div>
							      <div class="modal-body">
							        <form method="POST" action="admin.php" enctype="multipart/form-data">
							        	<div class="short-data-inputs">
							        		<div class="div-img">
									        	<input type="file" id="img-proj-input<?php echo $project['id'] ?>" accept="image/png, image/jpg, image/jpeg" name="picture" hidden onchange="if(verifyFileSize(this, tooBigMsg)) showUPMain(this);">
									        	<label for="img-proj-input<?php echo $project['id'] ?>" class="img-input-label">
									        		<img src="<?php echo $project['picture'] ?>">
									        	</label>
							        		</div>
										
											<div class="div-short-info">
												<div class="input-group">
													<input type="text" class="form-control" name="title" value="<?php echo $project['title'] ?>" placeholder="<?php echo $ModalItemName ?>" >
												</div>

												<div class="input-group">
													<div class="form-check">
													  <input class="form-check-input" type="checkbox" name="is_hidden" <?php if($project['is_hidden'] == true) echo "checked"; ?> id="flexCheck-edit-proj<?php echo $project['id'] ?>">
													  <label class="form-check-label" for="flexCheck-edit-proj<?php echo $project['id'] ?>">
													    <?php echo $ModalItemHideLabel ?>
													  </label>
													</div>
												</div>
											</div>
										</div>
										<h6><?php echo $pModalOtherPics ?></h6>
									<div class="div-other-pics">
										<?php 


										$arrProjPics = explode(',',$project['pictures']);


										for ($i=1; $i < count($arrProjPics); $i++) { ?>
											<script type="text/javascript" id="tempScript<?php echo $project['id']; echo $i ?>" defer>

												setTimeout(() => {
													var tmpFile;
													document.querySelector('#addPicButton<?php echo $project['id'] ?>').click();

													document.querySelector('#edit-proj-modal<?php echo $project['id'] ?> .div-other-pics').childNodes[<?php echo $i; ?>].childNodes[0].setAttribute('src', "<?php echo $arrProjPics[$i] ?>");
													var str = "<?php echo $arrProjPics[$i] ?>";
													document.querySelector('#edit-proj-modal<?php echo $project['id'] ?> .div-other-pics').childNodes[<?php echo $i; ?>].childNodes[1].childNodes[0].innerHTML = str.substr((str.lastIndexOf("/")+1));

													document.querySelector('#edit-proj-modal<?php echo $project['id'] ?> .div-other-pics').childNodes[<?php echo $i; ?>].childNodes[2].src = "<?php echo $arrProjPics[$i] ?>";


													document.querySelector('#addPicButton<?php echo $project['id'] ?> .add-pic-btn').style.display = 'flex';

												},300);

												document.querySelector('#tempScript<?php echo $project['id']; echo $i ?>').remove();
											</script>
										<?php }

										?>
									</div>
								  	<div class="add-pic-btn-div" id="addPicButton<?php echo $project['id'] ?>" onclick="addPicInput(this, 'Ed<?php echo $project['id'] ?>')">
								  		<span class="add-pic-btn"><span class="material-icons">add</span><?php echo $pModalAddOtherPicsBtn ?></span>
								  	</div>
										<textarea class="form-control" name="description" placeholder="<?php echo $ModalDesc ?>"><?php echo $project['description'] ?></textarea>
							        	
							        
							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $ModalBtnClose ?></button>
							        <input type="num" name="lastElId" value="0" hidden>
							        <input type="num" name="pId" value="<?php echo $project['id'] ?>" hidden>
							        <input type="submit" name="editProject" value="<?php echo $ModalBtnEdit ?>" class="btn btn-warning">
							        </form>
							      </div>
							    </div>
							  </div>
							</div>

							<!-- delete project -->

							<div class="modal fade " id="delete-proj-modal<?php echo $project['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
							  	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-m">
							    	<div class="modal-content">
							      		<div class="modal-header">
							        		<h4 class="modal-title" id="staticBackdropLabel"><span class="material-icons">delete</span><?php echo $pModalDeleteHeader ?></h4>
							        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							      		</div>
							      		<div class="modal-body">
							      		<p><?php echo $ModalDeleteText ?></p>
							      		</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-dark" data-bs-dismiss="modal"><?php echo $ModalBtnClose ?></button>
											<form method="POST" action="admin.php">
										        <input type="text" name="pTitle" value="<?php echo $project['title'] ?>" hidden>
									        	<input type="number" name="pId" value="<?php echo $project['id'] ?>" hidden>
										        <input type="submit" name="deleteProject" class="btn btn-danger" value="<?php echo $ModalBtnDelete ?>">
									        </form>
										</div>
							    	</div>
							  	</div>
							</div>

							<!-- delete project end -->

						</div>
					


					<?php } ?>



					</div>
				</div>

				<div class="option-content">
					<h3><span class="material-icons">reviews</span><?php echo $spReviews ?></h3>
					<br>

					<div class="oc-header">
						<a class="btn" data-bs-toggle="modal" data-bs-target="#add-rev-modal"><span class="material-icons">add</span><?php echo $rBtnAdd ?></a>
					</div>


					<!-- add review modal -->

					<div class="modal fade " id="add-rev-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered modal-m">
							<div class="modal-content">
							  <div class="modal-header">
							    <h5 class="modal-title" id="staticBackdropLabel"><span class="material-icons">add</span><?php echo $rModalHeader ?></h5>
							    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							  </div>
							  <div class="modal-body">
							    <form method="POST" action="admin.php" enctype="multipart/form-data">
							    	<div class="short-data-inputs">
							    		<div class="div-img">
								        	<input type="file" id="img-input-add-rev" type="image/png, image/jpg, image/jpeg" name="picture" hidden onchange="if(verifyFileSize(this, tooBigMsg)) showUPMain(this);">
								        	<label for="img-input-add-rev" class="img-input-label iil-review">
								        		<img src="img/no-image.svg">
								        	</label>
							    		</div>
									
										<div class="div-short-info">
											<div class="input-group">
													<input type="text" class="form-control" name="fname" placeholder="<?php echo $rModalFName ?>" autocomplete="off">
													<input type="text" class="form-control" name="lname" placeholder="<?php echo $rModalLName ?>" autocomplete="off">
												</div>
											
												<div class="input-group">
													<input type="text" class="form-control" name="position" placeholder="<?php echo $rModalPosition ?>" autocomplete="off">
												</div>
											<div class="input-group">
												<div class="form-check">
												  <input class="form-check-input" type="checkbox" name="is_hidden" id="flexCheck-add-rev">
												  <label class="form-check-label" for="flexCheck-add-rev">
												    <?php echo $ModalItemHideLabel ?>
												  </label>
												</div>
											</div>
										</div>
									</div>
									<textarea class="form-control" name="description" placeholder="<?php echo $rModalDesc ?>"></textarea>
							    	
							    
							  </div>
							  <div class="modal-footer">
							    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $ModalBtnClose ?></button>
							    <input type="submit" class="btn btn-primary" name="addReview" value="<?php echo $ModalBtnAdd ?>">
							    </form>
							  </div>
							</div>
						</div>
					</div>



					<div class="container-review-cards">


						<?php foreach ($allReviews as $allReview => $review) { ?>


						<div class="review-card">
							<div class="review-card-main">
								<div class="review-header">
									<div class="review-img">
										<img src="<?php echo $review['picture'] ?>" >
									</div>
									<div class="review-info">
										<p class="review-name"><?php echo $review['fname'] ?> <?php echo $review['lname'] ?></p>
										<p class="review-position"><?php echo $review['position'] ?></p>
									</div>
								</div>
								<blockquote class="review-text"><?php echo $review['description'] ?></blockquote>
							</div>
							<div class="review-card-actions">
								<div class="oc-list-actions-list">
									<a data-bs-toggle="modal" data-bs-target="#edit-rev-modal<?php echo $review['id'] ?>" class="oc-list-action" title="<?php echo $CALTEdit ?>">
										<span class="material-icons-outlined" >edit</span>
									</a>
									<form method="POST" action="admin.php">
									<label for="toggleReviewVisibility<?php echo $review['id'] ?>" class="oc-list-action" >
										<?php if($review['is_hidden'] == false) { ?>
										<span class="material-icons" title="<?php echo $CALTHide ?>" >visibility_off</span>
										<?php } else { ?>
										<span class="material-icons" title="<?php echo $CALTShow ?>" >visibility</span>
										<?php } ?>
									</label>
									<input type="number" name="rId" value="<?php echo $review['id'] ?>" hidden>
									<input type="submit" id="toggleReviewVisibility<?php echo $review['id'] ?>" name="hideReview" hidden>
								</form>
									<a data-bs-toggle="modal" data-bs-target="#delete-rev-modal<?php echo $review['id'] ?>" class="oc-list-action" title="<?php echo $CALTDelete ?>">
										<span class="material-icons" >delete</span>
									</a>
								</div>

								<!-- edit rev card  -->
								<div class="modal fade" id="edit-rev-modal<?php echo $review['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
							  <div class="modal-dialog modal-dialog-centered modal-m">
							    <div class="modal-content">
							      <div class="modal-header">
							        <h5 class="modal-title" id="staticBackdropLabel"><span class="material-icons">edit</span><?php echo $rModalEditHeader ?></h5>
							        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							      </div>
							      <div class="modal-body">
							        <form method="POST" action="admin.php" enctype="multipart/form-data">
							        	<div class="short-data-inputs">
							        		<div class="div-img">
									        	<input type="file" id="img-input-edit-rev<?php echo $review['id'] ?>" type="image/png, image/jpg, image/jpeg" name="picture" hidden onchange="if(verifyFileSize(this, tooBigMsg)) showUPMain(this);">
									        	<label for="img-input-edit-rev<?php echo $review['id'] ?>" class="img-input-label iil-review">
									        		<img src="<?php echo $review['picture'] ?>">
									        	</label>
							        		</div>
										
											<div class="div-short-info">
												<div class="input-group">
													<input type="text" class="form-control" name="fname" value="<?php echo $review['fname'] ?>" placeholder="<?php echo $rModalFName ?>" autocomplete="off" >
													<input type="text" class="form-control" name="lname" value="<?php echo $review['lname'] ?>" placeholder="<?php echo $rModalLName ?>" autocomplete="off" >
												</div>
											
												<div class="input-group">
													<input type="text" class="form-control" value="<?php echo $review['position'] ?>" name="position" placeholder="<?php echo $rModalPosition ?>" autocomplete="off">
												</div>
												<div class="input-group">
													<div class="form-check">
													  <input class="form-check-input" type="checkbox" <?php if($review['is_hidden'] == true) echo "checked"; ?> name="is_hidden"  id="flexCheck-edit-rev<?php echo $review['id'] ?>">
													  <label class="form-check-label" for="flexCheck-edit-rev<?php echo $review['id'] ?>">
													    <?php echo $ModalItemHideLabel ?>
													  </label>
													</div>
												</div>
											</div>
										</div>
										<textarea class="form-control" name="description" placeholder="Відгук"><?php echo $review['description'] ?></textarea>
							        	
							        
							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $ModalBtnClose ?></button>
							        <input type="text" name="oldfname" value="<?php echo $review['fname'] ?>" hidden>
							        <input type="text" name="oldlname" value="<?php echo $review['lname'] ?>" hidden>
							        <input type="text" name="rId" value="<?php echo $review['id'] ?>" hidden>
							        <input type="submit" name="editReview" class="btn btn-warning" value="<?php echo $ModalBtnEdit ?>">
							        </form>
							      </div>
							    </div>
							  </div>
							</div>

							<!-- delete goods -->

							<div class="modal fade " id="delete-rev-modal<?php echo $review['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
							  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-m">
							    <div class="modal-content">
							      <div class="modal-header">
							        <h4 class="modal-title" id="staticBackdropLabel"><span class="material-icons">delete</span><?php echo $rModalDeleteHeader ?></h4>
							        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							      </div>
							      <div class="modal-body">
							      	<p><?php echo $ModalDeleteText ?></p>
							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"><?php echo $ModalBtnClose ?></button>
							        <form method="POST" action="admin.php">
						        	<input type="text" name="fname" value="<?php echo $review['fname'] ?>" hidden>
						        	<input type="text" name="lname" value="<?php echo $review['lname'] ?>" hidden>
						        	<input type="number" name="rId" value="<?php echo $review['id'] ?>" hidden>
							        <input type="submit" name="deleteReview" class="btn btn-danger" value="<?php echo $ModalBtnDelete ?>">
							        </form>

							      </div>
							    </div>
							  </div>
							</div>





							</div>
						</div>



					
						<?php } ?>


						
					</div>

				</div>


				<div class="option-content">
					<h3><span class="material-icons">article</span><?php echo $spContacts ?></h3>
					<br>
					<form method="POST" action="admin.php">
					<div class="card bg-dark">
						<div class="card-body">
							<div class="oc-sett-data">
								<h5><?php echo $cTitle ?></h5>			
								<div class="input-group">
								  <span class="input-group-text material-icons">call</span>
								  <input type="text" value="<?php echo $contactsinfo['phone'] ?>" required name="phone" placeholder="<?php echo $cPhone ?>" class="form-control">
								</div>
								<div class="input-group">
								  <span class="input-group-text material-icons"><img src="img/waicon.svg"></span>
								  <input type="text" value="<?php echo $contactsinfo['whatsapp'] ?>" required name="whatsapp" placeholder="<?php echo $cWApp ?>" class="form-control">
								</div>
								<div class="input-group">
								  <span class="input-group-text material-icons"><img src="img/fbicon.svg"></span>
								  <input type="text" value="<?php echo $contactsinfo['facebook'] ?>" required name="facebook" placeholder="<?php echo $cFB ?>" class="form-control">
								</div>
								<div class="input-group" style="padding: 0 0 10px 10px;">
								  <span class="input-group-text material-icons">insert_link</span>
								  <input type="text" value="<?php echo $contactsinfo['fb_link'] ?>" required name="fb_link" placeholder="<?php echo $cFBLink ?>" class="form-control">
								</div>
								<div class="input-group">
								  <span class="input-group-text material-icons">mail</span>
								  <input type="text" value="<?php echo $contactsinfo['mail'] ?>" required name="mail" placeholder="<?php echo $cMail ?>" class="form-control">
								</div>
								<h6><?php echo $cGeoTitle ?></h6>
								<div class="input-group">
								  <span class="input-group-text material-icons">place</span>
								  <input type="text" value="<?php echo $contactsinfo['address'] ?>" required name='address' placeholder="<?php echo $cGeo ?>" class="form-control">
								</div>
								<div class="input-group ig-about-us">
								  <span class="input-group-text material-icons">groups_3</span>
								  <textarea name='about_us' placeholder="<?php echo $cAboutUs ?>" class="form-control"><?php echo $contactsinfo['about_us'] ?></textarea>
								</div>
							</div>
						</div>
					</div>
					<input class="btn btn-dark oc-rb-button" type="submit" value="<?php echo $cSaveBtn ?>" name="updDataOnPage">
					</form>
				</div>


				<div class="option-content">
					<h3><span class="material-icons">settings</span><?php echo $spSettings ?></h3>
					<br>
					<div class="oc-content">
						<div class="oc-sett-data">
							<h5><?php echo $setLangTitle ?></h5>
							<form method="POST" action="admin.php">
								<div class="input-group ig-sett">		
									<select class="form-select" name="language" onchange="document.querySelector('#changeLangSubmit').click()" aria-label="Default select example">
									  <option value="ua" <?php if($_SESSION['language'] == 'ua') echo "selected"; ?>>Українська</option>
									  <option value="ch" <?php if($_SESSION['language'] == 'ch') echo "selected"; ?>>Čeština</option>
									</select>
								</div>
								<input type="submit" name="changeLang" id="changeLangSubmit" hidden>
							</form>
						</div>
						<div class="oc-sett-data">
							<h5><?php echo $setAccTitle ?></h5>			
							<div class="input-group ig-sett">
							  <span class="input-group-text"><?php echo $setDataLogin ?></span>
							  <p class="form-control"><?php echo $_SESSION['login'] ?></p>
							</div>
						</div>
						<button class="btn btn-dark userDataButton"  data-bs-toggle="modal" data-bs-target="#acc-change-data"><?php echo $setEditDataBtn ?></button>
						<button  class="btn btn-dark userDataButton"  data-bs-toggle="modal" data-bs-target="#acc-change-pass"><?php echo $setEditPassBtn ?></button>

					</div>



					<div class="modal fade " id="acc-change-data" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
					  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h4 class="modal-title" id="staticBackdropLabel"><span class="material-icons">edit</span><?php echo $setModalEditDataHeader ?></h4>
					        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					      </div>
					      <form method="POST" action="admin.php">
						      <div class="modal-body">
						      	
						      	<div class="acc-data">
						      		<div class="input-group">
									  <span class="input-group-text"><?php echo $setDataLogin ?></span>
									  <input type="text" placeholder="<?php echo $setDataLogin ?>" name="login" autocomplete="off" value="<?php echo $_SESSION['login'] ?>" class="form-control">
									</div>
								</div>
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $ModalBtnClose ?></button>
						        <input type="submit" name="updateLogin" class="btn btn-warning" value="<?php echo $ModalBtnEdit ?>">
						      </div>
					      </form>
					    </div>
					  </div>
					</div>


					<div class="modal fade " id="acc-change-pass" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
					  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h4 class="modal-title" id="staticBackdropLabel"><span class="material-icons">key</span><?php echo $setModalEditPassHeader ?></h4>
					        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					      </div>
					      <div class="modal-body">

					      	<?php if(isset($_POST['updateUserPassword']) && !$oldpassVerify) {?>

				      		<script type="text/javascript">
				      			setTimeout(function() {
				      				document.querySelectorAll(".sp-option")[4].click();
				      			document.querySelector("button[data-bs-target='#acc-change-pass']").click();
				      			},500);
				      			
				      		</script>
					      	<p class="alert-custom"><i class="bi bi-x-circle-fill"></i><?php echo $setEditPassIncorrect ?></p>

					      	<?php } 
					      	if(isset($_POST['updateUserPassword']) && $oldpassVerify) { ?>

					      	<script type="text/javascript">
				      			setTimeout(function() {
				      				document.querySelectorAll(".sp-option")[4].click();
				      			document.querySelector("button[data-bs-target='#acc-change-pass']").click();
				      			},500);
				      			
				      		</script>
					      	<p class="alert-custom"><i class="bi bi-check-circle-fill"></i></i><?php echo $setEditPassSuccess ?></p>

					      	<?php } ?>
					      	<form method="POST" action="admin.php">
					        <div class="input-group mb-2">
							  <span class="input-group-text"><span style="color:black" class="material-icons-outlined" >lock_clock</span></span>
							  <input type="password" name="oldpass" placeholder="<?php echo $setDataOldPass ?>" id="oldPassInput" aria-label="First name" class="form-control">
							  <button type="button" onclick="showPass('oldPassInput')" class="input-group-text" ><span style="color:black" class="material-icons" >visibility</span></button>
							</div>
							
							<div class="input-group mb-2">
							  <span class="input-group-text"><span style="color:black" class="material-icons" >key</span></span>
							  <input type="password" name="newpass" placeholder="<?php echo $setDataNewPass ?>" id="newPassInput" aria-label="Login" class="form-control">
							  <button type="button" onclick="showPass('newPassInput')"  class="input-group-text" ><span style="color:black" class="material-icons" >visibility</span></button>
							  <script type="text/javascript">
							  	function showPass(passInputId) {
							  		var passInput = document.getElementById(passInputId);
							  		if (passInput.type == 'password') passInput.type = 'text';
							  		else passInput.type = 'password';
							  	}
							  </script>
							</div>
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $ModalBtnClose ?></button>
					        <input type="submit" name="updateUserPassword" class="btn btn-warning" value="<?php echo $ModalBtnEdit ?>">
					        </form>

					      </div>
					    </div>
					  </div>
					</div>






					</div>
				</div>



			</div>
		</div>

	</content>

	<script type="text/javascript">
		function hideSP(el) {
			if (el.classList.contains('side-panel-hidden')) {
				if(window.innerWidth < 600) {
					document.documentElement.style.setProperty('--side-panel-width', `100vw`);
					el.style.position = "fixed";
				}
				else document.documentElement.style.setProperty('--side-panel-width', `250px`);
				el.classList.remove('side-panel-hidden');
			}
			else {
				if(window.innerWidth < 600) el.style.position = "absolute";
				document.documentElement.style.setProperty('--side-panel-width', `60px`)
				el.classList.add('side-panel-hidden');
			}
		}
		tabContent = document.getElementsByClassName('option-content');
		tab = document.getElementsByClassName('sp-option');
		tabIcon = document.getElementsByClassName('sp-option-icon');
		tabTitle = document.getElementsByClassName('sp-option-label');	
		hideTabsContent(0);
		showTabsContent(0);
		// showTabsContentAJAX(0);
	function hideTabsContent(a) {
		for (var i = a; i < tabContent.length; i++) {
			tabContent[i].classList.remove('oc-showed');
			tabContent[i].classList.add('oc-hidden');
			setTimeout(function(i) {
				tabContent[i].style.display = 'none';
			},500, i)
			tab[i].classList.remove('sp-option-selected');
		}
	}
	document.getElementById('side-panel').onclick = function (event) {
		var target = event.target;
		if (target.classList.contains('sp-option') || target.classList.contains('sp-option-icon') || target.classList.contains('sp-option-label')) {
			for(var i=0; i < tab.length; i++) {
				if (target==tab[i] || target==tabIcon[i] || target==tabTitle[i]) {
					showTabsContent(i);
					break;
				}
			}
		}
	}
	function showTabsContent(b) {
		if (tabContent[b].classList.contains('oc-hidden')) {
			hideTabsContent(0);
			tab[b].classList.add('sp-option-selected');
			setTimeout(function(b) {
				tabContent[b].style.display = 'block';
			},500, b)
					
			tabContent[b].classList.remove('oc-hidden');
			tabContent[b].classList.add('oc-showed');
			// showTabsContentAJAX(b);
		}
	}
	</script>
	
	<?php } ?>
</body>
</html>