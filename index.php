<?php include 'dbconnect.php'; 

/* --- pull up the data --- */

	/* services */

	$result = $pdo->prepare("SELECT * FROM services WHERE `is_hidden` = 0");
	$result->execute();
	$allServices = $result->fetchAll();

	/* services end */

	/* projects */
	$result = $pdo->prepare("SELECT * FROM projects WHERE `is_hidden` = 0");
	$result->execute();
	$allProjects = $result->fetchAll();
	/* projects end */

	/* reviews */
	$result = $pdo->prepare("SELECT * FROM reviews WHERE `is_hidden` = 0");
	$result->execute();
	$allReviews = $result->fetchAll();
	/* reviews end */

	/* contacts */
	$result = $pdo->prepare("SELECT * FROM basic_info");
	$result->execute();
	$contactsinfo = $result->fetchAll();
	$contactsinfo = $contactsinfo[0];
	/* contacts end */

?>
<!DOCTYPE html>
<html style="--scrollTop: 0px">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/x-icon" href="img/favicon.png">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/media.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<script src="js/script.js" defer></script>
	<script type="text/javascript" defer>
		window.onscroll = function() {
		    var hdr = document.getElementById('header');
		    hdr.classList.remove('header-scroll-top');
		    if(window.scrollY == 0) {
		    	hdr.classList.add('header-scroll-top');
		    }
		}
	</script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous" defer></script>


	<link rel="stylesheet" type="text/css" href="slick/slick.css">
	<link rel="stylesheet" type="text/css" href="slick/slick-theme.css">
	<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js" defer></script>

	


	<!-- 
	про нас
	ціни на роботу
	контакти
	наші роботи
	відгуки 
	-->

	<title>Magnus stavservis | 🏗️ stavební firma </title>
</head>
<body>
	<div class="loader" id="main-loader">
		<div class="spinner-border text-light" role="status">
		  <span class="visually-hidden">Loading...</span>
		</div>
	</div>
	<header class="header header-scroll-top" id="header">
		<div class="header-logo">
			<!-- <p>Logo</p> -->
			<a href="index.html"><img src="img/logo.png"></a>
		</div>
		<div id="navbar-pc" class="header-links h-links-pc">
			<a href="#link0main" class="nav-link"><span class="material-icons">home</span>Hlavní</a>
			<a href="#link1about" class="nav-link"><span class="material-icons">groups_3</span>O nás</a>
			<a href="#link2services" class="nav-link"><span class="material-icons">construction</span>Služby</a>
			<a href="#link3contacts" class="nav-link"><span class="material-icons">call</span>Kontakty</a>
			<a href="#link4works" class="nav-link"><span class="material-icons">apartment</span>Naše díla</a>
			<a href="#link5reviews" class="nav-link"><span class="material-icons">reviews</span>Recenze</a>
			
		</div>
		<div class="header-links h-links-mobile">
			<button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><span class="material-icons-outlined">menu</span></button>
		</div>


	</header>

	<!-- burger menu -->

	<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
		  <div class="offcanvas-header">
		    <h5 id="offcanvasRightLabel">Menu</h5>
		    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		  </div>
		  <div class="offcanvas-body">
		  	<div class="burger-main-links">

				<a href="#link0main" data-bs-dismiss="offcanvas">Hlavní</a>
				<a href="#link1about" data-bs-dismiss="offcanvas">O nás</a>
				<a href="#link2services">Služby</a>
				<a href="#link3contacts" data-bs-dismiss="offcanvas">Kontakty</a>
				<a href="#link4works" data-bs-dismiss="offcanvas">Naše díla</a>
				<a href="#link5reviews" data-bs-dismiss="offcanvas">Recenze</a>
			</div>
		  </div>
	</div>

	<!-- burger menu -->

	<content class="main-content">

		<!-- <div> </div> -->
		<!-- <div class="main-contacts">
			<p>
				<a href="tel:+380000000000">
					<span class="material-icons">phone</span>
					+380000000000
				</a>
			</p>
		</div> -->
		<i class="container-text" id="link0main"></i>
		<div class="main-section" >

			<div class="section-text main-centered-text">
				<div>
					<h1>Ahoj !</h1>
					<h5>Jsme rádi, že vás vidíme na našem webu !</h5>
				</div>
			</div>
			<div class="ms-person"></div>
		</div>
		
		<div class="container-text" id="link1about">
			<div class="text">
				<h3><i class="iAnchor" ></i><span class="material-icons">groups_3</span>O nás</h3>
				<p><?php echo $contactsinfo['about_us'] ?>
			</p>

			</div>
		</div>
<!-- 
		<div class="second-section">
			<div class="main-centered-text">
				<div>
					<h1>Ahoj !</h1>
					<h5>Nejlepší stavební firma přímo před vámi</h5>
				</div>	
			</div>
		</div>
 -->
		<div class="container-text" id="link2services">
			<div class="text">
				<h3><i class="iAnchor" ></i><span class="material-icons-outlined" style="color:black">construction</span>Služby</h3>
			</div>
			<div class="container-cards">


				<?php foreach ($allServices as $allService => $service) { ?>

				<div class="card" onclick="servLabel(this, <?php echo $service['id'] ?>)" data-bs-toggle="modal" data-bs-target="#serviceModal">
				  <img src="<?php echo $service['picture'] ?>" class="card-img-top" alt="...">
				  <div class="card-body">
				    <h5 class="card-title ctitle-service"><?php echo $service['title'] ?> <i><?php echo $service['price'] ?> Kč</i></h5>
					    <p class="card-text">
					    	<?php 
									if(strlen($service['description']) > 50 ) echo mb_substr($service['description'], 0 , 50)."...";
									else echo $service['description'];
								?>
							</p>
				    <a href="#" class="btn btn-danger">Více informací</a>
				  </div>
				</div>

				<?php } ?>

			</div>


			<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title" id="servModalLabel">Modal title</h5>
			        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			      </div>
			      <div class="modal-body">
			      	<div class="modal-serv-body">
			        	<div class="m-s-image-div">
				        		<img id="m-s-img" src="img/good.jpg">
			        	</div>


				        <h5><b id="m-s-b-title">Nazva</b></h5>
				        <i style="display: block; margin-bottom: 10px;">Cena: <b id="m-s-b-price"></b> Kč</i>
			        	<h5><b>Popis</b></h5>
			        	<span id="m-s-b-desc"></span>
			        </div>

			      </div>
			      
			    </div>
			  </div>
			</div>




		</div>
		
		<!-- <div class="second-section">
			<div class="main-centered-text">
				<div>
					<h1>Ahoj !</h1>
					<h5>Nejlepší stavební firma přímo před vámi</h5>
				</div>	
			</div>
		</div> -->

		<div class="container-text" id="link3contacts">
			<div class="text">
				<h3><i class="iAnchor" ></i><span class="material-icons">call</span>Kontakty</h3>
				<div class="container-contacts">
					<a href="tel:<?php echo $contactsinfo['phone'] ?>"><span class="material-icons">phone</span><?php echo $contactsinfo['phone'] ?></a>
					<a href="whatsapp://send?abid=<?php echo $contactsinfo['phone'] ?>"><img src="img/waicon.svg" ><?php echo $contactsinfo['whatsapp'] ?></a>
					<a href="<?php echo $contactsinfo['fb_link'] ?>" target="_blank"><img src="img/fbicon.svg" ><?php echo $contactsinfo['facebook'] ?></a>
					<h5><span class="material-icons" >place</span>Adresa</h5>
					<a href="https://goo.gl/maps/F9ob76VMEjzEfTVD7" target="_blank"><span class="material-icons">place</span><?php echo $contactsinfo['address'] ?></a>
					<h5><span class="material-icons">question_mark</span>Máte nějaké dotazy?, napište na tento e-mail a my vám rádi odpovíme!</h5>
					<i>(Kliknutím níže odešlete zprávu)</i>
					<a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo $contactsinfo['mail'] ?>&su=Zpráva z webu společnosti&body=Dobrý den, tuto zprávu píši po kliknutí na odkaz ze stránky společnosti. Píši Vám s následující otázkou:" target="_blank"><img src="img/gmicon.svg" ><?php echo $contactsinfo['mail'] ?></a>

				</div>


			</div>
			
		</div>

		<div class="second-section" id="link4works">
			<div class="main-centered-text">
				<div>
					<h1>Naše díla !</h1>
					<h5>Prohlédněte si naše projekty, abyste se seznámili s našimi zkušenostmi a kvalitou práce</h5>
				</div>	
			</div>
		</div>

		<div class="container-text" >
			<div class="text">
				<h3><i class="iAnchor" ></i><span class="material-icons">apartment</span>Naše díla</h3>
			</div>
			<div class="container-goods-cards">

				<?php foreach ($allProjects as $allProject => $project) {  ?>

				<div class="card" onclick="mLabel(this, <?php echo $project['id'] ?>)" data-bs-toggle="modal" data-bs-target="#worksModal">
				  <img src="<?php echo $project['picture'] ?>" class="card-img-top" alt="...">
				  <div class="card-body">
				    <h5 class="card-title"><?php echo $project['title'] ?></h5>
					    <p class="card-text">
					    	<?php 
									if(strlen($project['description']) > 50 ) echo mb_substr($project['description'], 0 , 50)."...";
									else echo $project['description'];
								?>
							</p>
				    <a class="btn btn-danger" >Více informací</a>
				  </div>
				</div>

				<?php } ?>



			</div>

			<div class="modal fade" id="worksModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title" id="modalLabel">Modal title</h5>
			        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			      </div>
			      <div class="modal-body">


			      	<div class="loader" id="proj-loader">
								<div class="spinner-border text-light" role="status">
								  <span class="visually-hidden">Loading...</span>
								</div>
							</div>


			        <div class="modal-container-work-pics">
			        	<div class="m-c-work-pic-container">
				        	<div class="m-c-work-pic" >
				        		

				        		<img src="img/no-image.svg">
				        	</div>
			        	</div>
			        	<div class="m-c-work-slider">
			        		

			        	</div>
			        </div>
			        <div class="m-c-work-description">
			        	<h5><b>Popis</b></h5>
			        	<span>Some quick example text to build on the card title and make up the bulk of the card's content.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla sollicitudin purus non mauris euismod, eu consectetur lacus luctus. Aenean auctor tortor nec diam convallis, ut egestas mi volutpat. Nullam molestie, felis sit amet laoreet vestibulum, ante urna malesuada arcu, sed tristique massa mi vitae massa. Morbi dictum felis ac facilisis finibus. Curabitur cursus consequat dapibus. Donec ante felis, dignissim a tincidunt vel, consequat et augue. Sed a nisl sagittis, vulputate eros ut, scelerisque libero. Phasellus iaculis et eros ac mollis.</span>
			        </div>
			      </div>
			      
			    </div>
			  </div>
			</div>

			


		</div>


		<div class="container-text" id="link5reviews">
			<div class="text">
				<h3><i class="iAnchor" ></i><span class="material-icons">reviews</span>Recenze</h3>
			</div>
			<div class="container-review-cards">

				<?php foreach ($allReviews as $allReview => $review) {  ?>

				<div class="review-card">
					<div class="review-header">
						<div class="review-img">
							<img src="<?php echo $review['picture'] ?>">
						</div>
						<div class="review-info">
							<p class="review-name"><?php echo $review['fname'].' '.$review['lname']  ?></p>
							<p class="review-position"><?php echo $review['position'] ?></p>
						</div>
					</div>
					<blockquote class="review-text"><?php echo $review['description'] ?></blockquote>
				</div>
				
				<?php } ?>
				
			</div>
		</div>


	</content>
	<div class="footer">
		<a href="<?php echo $_SERVER['SCRIPT_NAME'] ?>"><img class="footer-logo" src="img/logo.png"></a>
		<p>© 2022-<?php echo date("Y"); ?> MAGNUS stavservis s.r.o.</p>
	</div>
	<script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
  <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
  <script type="text/javascript" src="slick/slick.min.js"></script>
  <script type="text/javascript">
  	var projArr = [[]];
  	var servArr = [[]];


  	<?php for ($i=0; $i < count($allProjects); $i++) {
  		?>
  		projArr[<?php echo $allProjects[$i][0] ?> ] = [];
  		<?php
  		for ($b=0; $b < 5; $b++) {
  	 ?>
  	 	projArr[<?php echo $allProjects[$i][0] ?>].push("<?php echo $allProjects[$i][$b] ?>");
  	<?php } } ?>


  	<?php for ($i=0; $i < count($allServices); $i++) {
  		?>
  		servArr[<?php echo $allServices[$i][0] ?> ] = [];
  		<?php
  		for ($b=0; $b < 5; $b++) {
  	 ?>
  	 	servArr[<?php echo $allServices[$i][0] ?>].push("<?php echo $allServices[$i][$b] ?>");
  	<?php } } ?>


  	function mLabel(el, id) {
  		var wModalLabel = document.getElementById('modalLabel');
  		wModalLabel.innerHTML = el.children[1].children[0].innerHTML;
  		var currPics = projArr[id][4].split(',');

  		document.querySelector('#proj-loader').classList.remove('hidden-loader');
  		document.querySelector('#proj-loader').style.display = 'flex';
  		setTimeout(() => {
	    	document.querySelector('#proj-loader').classList.add('hidden-loader');
	    	
	    }, 1000);
	    setTimeout(() => {
	    	document.querySelector('#proj-loader').style.display = 'none';
	    }, 1700);

  		

  		if (document.querySelector('.m-c-work-pic').classList.contains('slick-initialized')) {
	  		$('.m-c-work-pic').slick('unslick');
	  		$('.m-c-work-slider').slick('unslick');
  		}

  		document.querySelector('.m-c-work-pic').innerHTML = '';
  		document.querySelector('.m-c-work-slider').innerHTML = '';


  		for (var i = 0; i < currPics.length; i++) {
  			var tmpDOM = document.createElement('img');
  			tmpDOM.src = currPics[i];
  			document.querySelector('.m-c-work-pic').appendChild(tmpDOM);
  			tmpDOM = document.createElement('img');
  			tmpDOM.src = currPics[i];
  			document.querySelector('.m-c-work-slider').appendChild(tmpDOM);
  		}
  		
  		document.querySelector('.m-c-work-description span').innerHTML = projArr[id][2];
  		
	    setTimeout(() => {
	    	$('.m-c-work-pic').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        fade: true,
        asNavFor: '.m-c-work-slider'
    });
	    $('.m-c-work-slider').slick({
	        slidesToShow: 5,
	        slidesToScroll: 1,
	        autoplay: true,
	        asNavFor: '.m-c-work-pic',
	        dots: true,
	        focusOnSelect: true,
	        responsive: [
	        {
	            breakpoint: 1200,
	            settings: {
	              slidesToShow: 3
	            }
	          },
	          {
	            breakpoint: 992,
	            settings: {
	              dots: false,
	              slidesToShow: 2
	            }
	          },
	          {
	            breakpoint: 500,
	            settings: {
	              dots: false,
	              slidesToShow: 1
	            }
	          }
	        ]
	    });
	    document.querySelector('.m-c-work-slider .slick-next').click();
	  	}, 800);


  	}


  	function servLabel(el, id) {
  		var servModalLabel = document.getElementById('servModalLabel');
  		servModalLabel.innerHTML = servArr[id][1];
  		document.getElementById('m-s-b-title').innerHTML = servArr[id][1];
  		document.getElementById('m-s-b-price').innerHTML = servArr[id][2];
  		document.getElementById('m-s-b-desc').innerHTML = servArr[id][3];
  		document.getElementById('m-s-img').src = servArr[id][4];


  		
  		
  		document.querySelector('.m-c-serv-description span').innerHTML = servArr[id][2];

  		// From php (JSON var) create DOM elements


  	}



  </script>
</body>
</html>