
<!DOCTYPE html>
<html lang="zxx">

<head>
	<title>{{opt('site_title')}}</title>
	<!-- Meta tag Keywords -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8" />
	<meta name="keywords"
		content="{{opt('site_title')}}" />
	<!-- //Meta tag Keywords -->
	<link rel="icon" href="images/fav.png" type="images/png" sizes="16x16">
	<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;700&display=swap" rel="stylesheet">
	<!-- //google fonts -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all" /> <!-- //Style-CSS -->

	<link href="css/font-awesome.css" rel="stylesheet"><!-- //font-awesome-icons -->

</head>

<body>
	<!-- coming soon -->
	<section class="w3l-coming-soon-page">
		<div class="coming-page-info">
			<div class="wrapper">
				<div class="logo-center">
				 <a class="logo" href="#index.html">
            <img src="images/EvolutionRx_BlueLogo.png" alt="Your logo" title="Your logo" style="height:55px;" />
					</a>
				</div>




				<div class="coming-block">


					<p>We are running a routine maintenance and upgradation on our portal and we will be back soon.</p>

					<!-- countdown -->
					<div class="countdown">
						<div class="countdown__days">
							<div class="number"></div>
							<span class>Days</span>
						</div>

						<div class="countdown__hours">
							<div class="number"></div>
							<span class>Hours</span>
						</div>

						<div class="countdown__minutes">
							<div class="number"></div>
							<span class>Minutes</span>
						</div>

						<div class="countdown__seconds">
							<div class="number"></div>
							<span class>Seconds</span>
						</div>
					</div>
					<!-- countdown -->

			        <div class="text-center center-block">
            <p class="txt-railway">Stay Connected</p>
            <br />



				<!-- copyright -->
				<div class="copyright-footer">
					<div class="w3l-copy-right">
						<p>© 2022 {{opt('site_title')}}. All rights reserved
					</div>
				</div>
				<!-- //copyright -->
			</div>
		</div>

		<!-- js -->
		<script src="js/jquery-3.3.1.min.js"></script>

		<!-- Script for counter -->
		<script>
			(() => {
				// Specify the deadline date
				const deadlineDate = new Date('June 20, 2022 23:59:59').getTime();

				// Cache all countdown boxes into consts
				const countdownDays = document.querySelector('.countdown__days .number');
				const countdownHours = document.querySelector('.countdown__hours .number');
				const countdownMinutes = document.querySelector('.countdown__minutes .number');
				const countdownSeconds = document.querySelector('.countdown__seconds .number');

				// Update the count down every 1 second (1000 milliseconds)
				setInterval(() => {
					// Get current date and time
					const currentDate = new Date().getTime();

					// Calculate the distance between current date and time and the deadline date and time
					const distance = deadlineDate - currentDate;

					// Calculations the data for remaining days, hours, minutes and seconds
					const days = Math.floor(distance / (1000 * 60 * 60 * 24));
					const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
					const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
					const seconds = Math.floor((distance % (1000 * 60)) / 1000);

					// Insert the result data into individual countdown boxes
					countdownDays.innerHTML = days;
					countdownHours.innerHTML = hours;
					countdownMinutes.innerHTML = minutes;
					countdownSeconds.innerHTML = seconds;
				}, 1000);
			})();
		</script>
		<!-- //Script for counter -->

	</section>
	<!-- //coming soon -->
</body>

</html>
