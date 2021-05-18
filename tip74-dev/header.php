
<!DOCTYPE html>
<html lang="en" data-ng-app="TIP74" >
<head>
	<?php include('MetaData.php') ?>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/custom.css">
	<link rel="stylesheet" href="assets/css/responsive.css">
	<link rel="stylesheet" href="assets/css/slick.css">
	<link rel="stylesheet" href="assets/css/slick-theme.css">
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/angular-datetimepicker.css"/>
	<link rel="stylesheet" href="assets/css/select2.css"/>
	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
	<link rel="icon" href="assets/img/fevicon_icon.png" type="image/gif" sizes="32x32">
</head>
<body ng-controller="MainController" ng-cloak>
	<header class="header" ng-controller="headerController" ng-cloak>
		<nav class="navbar navbar-expand-xl navbar-light fixed-top">
			<div class="container">
				<a class="navbar-brand order-1 order-xl-1" ng-click="gameTypeSelection('SPORTS')"  href="<?= $base_url; ?>"><img src="assets/img/logo.png" class="img-fluid"></a>

				<div class="right_menu d-flex align-items-center order-2 order-xl-3">
					<div class="login" ng-if="!isLoggedIn">
						<a href="javascript:void(0)" data-toggle="modal" data-target="#LoginModal"><i class="fa fa-key mr-2"></i>LOGIN</a>
					</div>
					<div class="user-details" ng-if="isLoggedIn">
		            	<div class="dropdown notification" ng-init="getNotifications()">
		                	<a href="javascript:void(0)" class="" id="notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		                		<i class="fa fa-bell"></i>
		                		<span class="badge" ng-if="notificationCount > 0">{{notificationCount}}</span>
		                	</a>
		                	<div class="dropdown-menu" aria-labelledby="notifications">
		                		<div class="massages">
		                    		<h5> Notifications </h5>
		                    		<div class="clear_all text-right" ng-if="notificationList.length > 0">
										<a href="javascript:void(0);"  ng-click="checkNotificationDeletionCount()">
	                                    	<i class="fa fa-trash" style="color:#000"></i>Clear All
	                                    </a>
                                    </div>
		                			<ul class="list-unstyled notification_list comman_scroll">
		                    			<li ng-repeat="notification in notificationList" ng-click="readNotification(notification.NotificationID, notification.RefrenceID)">
					                        <p>
					                        	<span>{{notification.NotificationMessage}}</span> <br>
					                        	<span>{{notification.EntryDate | myDateFormat}}</span>
					                        </p>
		                				</li>
		                    			<li ng-if="notificationList.length == 0"> No unread notification. </li>
		                    		</ul>
		                  		</div>
		            		</div>
		            	</div>
		            	<div class="dropdown account_dropdown">
		                	<a href="javascript:;" class="" id="account_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span>{{getUserNameFirstLetter(profileDetails.FirstName,'First')}}</span>{{getUserNameFirstLetter(profileDetails.FirstName,'Short')}}<i class="fa fa-sort-desc ml-2"></i></a>
			                <div class="dropdown-menu">
			                	<a href="profile" class="dropdown-item">My Profile</a>
								<a href="myAccount" class="dropdown-item">My Wallet</a>
								<a href="myEntries" class="dropdown-item">My Entries</a>
								<a href="javascript:void(0)" ng-click="openPopup('changePassword');" class="dropdown-item">Change Password</a>
								<a href="javascript:void(0)" ng-click="openPopup('bankDetails');" class="dropdown-item">Setup Bank Info</a>
			                	<a href="javascript:void(0);" ng-click="logout()" class="dropdown-item">Logout</a>
			                </div>
		            	</div>
		            </div>

		            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				    	<span class="sr-only">Toggle navigation</span>
		                <span class="icon-bar top"></span>
		                <span class="icon-bar mid"></span>
		                <span class="icon-bar btm"></span>
					</button>
				</div>
				
				<div class="collapse navbar-collapse justify-content-center order-3 order-xl-2" id="navbarNav">
			    	<ul class="navbar-nav">
				    	<li class="nav-item active dropdown">
					        <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					          {{GamesType}}
					        </a>
					        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
								<a class="dropdown-item" href="<?= $base_url; ?>" ng-click="gameTypeSelection('SPORTS')" style="background: lightgrey;" >SPORTS</a>
					        	<a class="dropdown-item" ng-click="gameTypeSelection('Soccer')"  href="<?= $base_url; ?>{{isLoggedIn?'dashboard':''}}">Soccer</a>
					        	<a class="dropdown-item" ng-click="gameTypeSelection('Horse Racing')" href="<?= $base_url; ?>{{isLoggedIn?'HorseRacing.php':''}}">Horse Racing</a>
				    		</div>
					    </li>
				    	<li class="nav-item" ng-if="isLoggedIn">
				        	<a class="nav-link {{secondLevelLocation == 'myPrediction'?'showActiveTab':''}}" href="myPrediction">My Prediction</a>
				    	</li>
				    	<li class="nav-item" ng-if="isLoggedIn">
				        	<a class="nav-link {{secondLevelLocation == 'leaderboard'?'showActiveTab':''}}" href="leaderboard">Leaderboard</a>
				    	</li>
				    	<li class="nav-item">
				        	<a class="nav-link {{secondLevelLocation == 'HowItWork'?'showActiveTab':''}}" href="HowItWork">How it Works</a>
				    	</li>
				    	<li class="nav-item">
				        	<a class="nav-link {{secondLevelLocation == 'AboutUs'?'showActiveTab':''}}" href="AboutUs">About Us</a>
				    	</li>
				    	<li class="nav-item">
				        	<a class="nav-link {{secondLevelLocation == 'ContactUs'?'showActiveTab':''}}" href="ContactUs">Contact Us</a>
				    	</li>
			    	</ul>
			    	<div class="social-links">
						<a href="javascript:void(0)"><i class="fa fa-facebook"></i></a>
						<a href="javascript:void(0)"><i class="fa fa-twitter"></i></a>
						<a href="javascript:void(0)"><i class="fa fa-instagram"></i></a>
						<a href="javascript:void(0)"><i class="fa fa-google-plus"></i></a>
					</div>
				</div>
			</div>
		</nav>
		<?php if($PathName == ''){ ?>
		<div class="banner home_banner" style="padding-top: 62px;" ng-if="!isLoggedIn">
			<div id="banner-slider" class="carousel slide" data-ride="carousel">
				<ul class="carousel-indicators">
				    <li data-target="#banner-slider" data-slide-to="0" class="active"></li>
				    <li data-target="#banner-slider" data-slide-to="1"></li>
				</ul>
			  
				<div class="carousel-inner">
				    <div class="carousel-item {{($index==0)?'active':''}}" ng-repeat="banner in BannerList">
				    	<img src="{{banner.MediaURL}}" alt="">
				    	<div class="carousel-caption">
				    		<img src="assets/img/logo-img.png" alt="">
					        <h1>More Ways To Win</h1>
					        <a href="javascript:void(0)" class="playNow">Play Now</a>
				    	</div>
				    </div>
				</div>
			  
				<!-- <a class="carousel-control-prev" href="#banner-slider" data-slide="prev">
			    	<span class="carousel-control-prev-icon"></span>
				</a>
				<a class="carousel-control-next" href="#banner-slider" data-slide="next">
			    	<span class="carousel-control-next-icon"></span>
				</a> -->
			</div>
		</div>
		<?php } ?>
	</header>
	<!-- <a href="javascript:void(0)" ng-click="getEntryList();getUserBalance(SelectedWeekGUID);openPopup('entryPopup')" class="btn_primary fixedPurchaseBtn animate"> <i class="fa fa-money fa-1x mr-2 d-none" aria-hidden="true"></i> Purchase Entry </a> -->
	
