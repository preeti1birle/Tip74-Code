<?php include 'header.php';?>

<div class="mainContainer" ng-init="getStaticPageContent('aboutus');getBannerList();">
    <div style="margin-top:75px;">
        <div class="top-header-title">
            <div class="container">
                <div class="d-flex justify-content-center align-items-center">
                    <h1> {{Title}}  </h1>
                </div>
                <div class="banner home_banner">
			      <div id="banner-slider" class="carousel slide" data-ride="carousel">
				    <ul class="carousel-indicators">
				      <li data-target="#banner-slider" data-slide-to="{{$index}}" class="{{($index==0)?'active':''}}" ng-repeat="banner in AboutUsBanner"></li>
				    </ul>
			  
				    <div class="carousel-inner">
				      <div class="carousel-item {{($index==0)?'active':''}}" ng-repeat="banner in AboutUsBanner">
					    <img src="{{banner.MediaURL}}" alt="">
				    	  <div class="carousel-caption">
				    		<img src="assets/img/logo-img.png" alt="">
					        <h3>More Ways To Win</h3>
					        <a href="javascript:void(0)" class="playNow">Play Now</a>
				    	  </div>
				      </div>
				    </div>
			      </div>
		        </div>
                <!-- <div class="d-flex justify-content-center align-items-center">
                    <img ng-src="{{MediaThumbURL}}" class="img-fluid" alt="about">
                </div> -->
            </div>
        </div>    
        <div class="about_us_content">
            <div class="container mt-lg-4">
                <div class="row">
                <div class="col-lg-12 col-md-12" ng-bind-html="ContestInfo">
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<!--Main container sec end-->
<?php include 'footerHome.php';?>