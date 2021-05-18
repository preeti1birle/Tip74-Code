<?php include('header.php') ?>
<div class="content" ng-controller="HomeController" ng-init="getTestimonials()" ng-cloak>
    <section class="section-01">
        <div class="banner home_banner">
            <div id="banner-slider" class="carousel slide" data-ride="carousel">
                <ul class="carousel-indicators">
                    <li data-target="#banner-slider" data-slide-to="{{$index}}" class="{{($index==0)?'active':''}}"
                        ng-repeat="banner in MediaThumbURL"></li>
                </ul>

                <div class="carousel-inner">
                    <div class="carousel-item {{($index==0)?'active':''}}" ng-repeat="banner in MediaThumbURL">
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
        <div class="cornerBG"></div>
        <div class="d-sm-flex soccer-horseRacing">
            <div class="col-sm-6 col-md-4 order-2 order-md-1 img-box">
                <img src="assets/img/soccer-horseRacing_01.jpg">
                <div class="img-caption">
                    <h4>Lorem ipsum, or lipsum as it is sometimes known Lorem </h4>
                    <p>Lorem ipsum, or lipsum as it is s ometimes known Lorem ipsum, or lipsum as it is s ometimes known
                    </p>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 order-1 order-md-2 img-box">
                <img src="assets/img/soccer-horseRacing_02.jpg">
                <div class="soccer-horseRacing-box d-none d-lg-flex">
                    <ul class="nav nav-tabs btn_tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="soccer-tab" data-toggle="tab" href="#soccer" role="tab"
                                aria-controls="soccer" aria-selected="true">
                                <img src="assets/img/soccer.png">
                                <h5>Soccer</h5>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="horseRacing-tab" data-toggle="tab" href="#horseRacing" role="tab"
                                aria-controls="horseRacing" aria-selected="false">
                                <img src="assets/img/horseRacing.png">
                                <h5>Horse Racing</h5>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="img-caption">
                    <div>
                        <h3>SOCCER</h3>
                        <p>Lorem ipsum, or lipsum as it is s ometimes known Lorem ipsum, or lipsum as it is s ometimes
                            known</p>
                        <a ng-if="isLoggedIn" href="<?= $base_url; ?>{{isLoggedIn?'dashboard':''}}"
                            class="round-btn">Prediction <img src="assets/img/arrow-right.svg"></a>
                        <a ng-if="!isLoggedIn" href="javascript:void(0)" class="round-btn" data-toggle="modal"
                            data-target="#LoginModal">Prediction <img src="assets/img/arrow-right.svg"></a>
                    </div>

                    <div class="cntr-line my-2 my-xl-5"></div>

                    <div>
                        <h3>HORSE RACING</h3>
                        <p>Lorem ipsum, or lipsum as it is s ometimes known Lorem ipsum, or lipsum as it is s ometimes
                            known</p>
                        <a href="javascript:void(0)" class="round-btn">Prediction <img
                                src="assets/img/arrow-right.svg"></a>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4 order-2 order-md-3 img-box">
                <img src="assets/img/soccer-horseRacing_03.jpg">
                <div class="img-caption">
                    <h4>Lorem ipsum, or lipsum as it is sometimes known Lorem </h4>
                    <p>Lorem ipsum, or lipsum as it is s ometimes known Lorem ipsum, or lipsum as it is s ometimes known
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-02">
        <div class="cornerBG"></div>
        <div class="premier-leagues">
            <div class="container">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="soccer" role="tabpanel" aria-labelledby="soccer-tab">
                        <div class="row">
                            <div class="col-sm-6 col-lg-4">
                                <div class="league">
                                    <h6>Premier League</h6>
                                    <div class="league-details">
                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                            Ipsum has been the industry's standard dummy text </p>
                                        <a href="javascript:void(0)" class="readMore mb-2">Read More</a>
                                        <a href="javascript:void(0)" class="round-btn">START</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <div class="league">
                                    <h6>Premier League</h6>
                                    <div class="league-details">
                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                            Ipsum has been the industry's standard dummy text </p>
                                        <a href="javascript:void(0)" class="readMore mb-2">Read More</a>
                                        <a href="javascript:void(0)" class="round-btn">START</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <div class="league">
                                    <h6>Premier League</h6>
                                    <div class="league-details">
                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                            Ipsum has been the industry's standard dummy text </p>
                                        <a href="javascript:void(0)" class="readMore mb-2">Read More</a>
                                        <a href="javascript:void(0)" class="round-btn">START</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-lg-4">
                                <div class="league">
                                    <h6>Premier League</h6>
                                    <div class="league-details">
                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                            Ipsum has been the industry's standard dummy text </p>
                                        <a href="javascript:void(0)" class="readMore mb-2">Read More</a>
                                        <a href="javascript:void(0)" class="round-btn">START</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <div class="league">
                                    <h6>Premier League</h6>
                                    <div class="league-details">
                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                            Ipsum has been the industry's standard dummy text </p>
                                        <a href="javascript:void(0)" class="readMore mb-2">Read More</a>
                                        <a href="javascript:void(0)" class="round-btn">START</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <div class="league">
                                    <h6>Premier League</h6>
                                    <div class="league-details">
                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                            Ipsum has been the industry's standard dummy text </p>
                                        <a href="javascript:void(0)" class="readMore mb-2">Read More</a>
                                        <a href="javascript:void(0)" class="round-btn">START</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="horseRacing" role="tabpanel" aria-labelledby="horseRacing-tab">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h6 class="text-white">Comming Soon</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-03 news burger">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3>News</h3>
                </div>
                <div class="col-lg-10 offset-lg-1 col-md-12">
                    <div class="news-details">
                        <ul class="col-sm-6">
                            <li>New: Super Rugby Restart tournament to launch on Superbru</li>
                            <li>New: Super Rugby Restart tournament to launch on Superbru</li>
                            <li>New: Super Rugby Restart tournament to launch on Superbru</li>
                            <li>New: Super Rugby Restart tournament to launch on Superbru</li>
                        </ul>
                        <ul class="col-sm-6">
                            <li>New: Super Rugby Restart tournament to launch on Superbru</li>
                            <li>New: Super Rugby Restart tournament to launch on Superbru</li>
                            <li>New: Super Rugby Restart tournament to launch on Superbru</li>
                            <li>New: Super Rugby Restart tournament to launch on Superbru</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-04 howToPredict burger">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3>How To Predict</h3>
                </div>
                <div class="col-lg-5">
                    <ul>
                        <li>
                            <h6 class="themeClr">CHOOSE A GAME</h6>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                ut labore et dolore </p>
                        </li>
                        <li>
                            <h6 class="themeClr">Create Dream Team</h6>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                ut labore et dolore </p>
                        </li>
                        <li>
                            <h6 class="themeClr">Watch & Win</h6>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                ut labore et dolore </p>
                        </li>
                    </ul>
                    <a href="javascript:void(0)" class="playNow">PLAY NOW</a>
                </div>
                <div class="col-lg-7 mt-4 mt-lg-0">
                    <div class="video video-img-box">
                        <img src="assets/img/video-bg.png">
                        <a href="javascript:void(0)" class="playVideo" data-toggle="modal"
                            data-target="#videoModal"><img src="assets/img/video-play-icon.png" class="img-fluid"></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-05 testimonial burger" ng-if="Testimonials.length>0">
        <div id="testimonial-slider" class="carousel slide" data-ride="carousel">
            <!-- <ul class="carousel-indicators">
				    <li data-target="#testimonial-slider" data-slide-to="0" class="active"></li>
				    <li data-target="#testimonial-slider" data-slide-to="1"></li>
				    <li data-target="#testimonial-slider" data-slide-to="2"></li>
				</ul> -->

            <div class="container">
                <div class="row">
                    <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
                        <div class="carousel-inner">
                            <div class="carousel-item {{$index == 0?'active':''}}" ng-repeat="test in Testimonials">
                                <img src="{{test.MediaURL}}" alt="">
                                <p><i class="fa fa-quote-left mr-2"></i>{{test.PostContent}} <i
                                        class="fa fa-quote-right ml-2"></i></p>
                                <h6 class="themeClr">{{test.PostCaption}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <a class="carousel-control-prev" href="#testimonial-slider" data-slide="prev">
                <span class="carousel-control-prev-icon"><img src="assets/img/chevron-left.png"></span>
            </a>
            <a class="carousel-control-next" href="#testimonial-slider" data-slide="next">
                <span class="carousel-control-next-icon"><img src="assets/img/chevron-right.png"></span>
            </a>
        </div>
    </section>

    <section class="section-06 downloadSec">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-md-4 mb-4 mb-md-0 mt-lg-5 order-1 order-md-1 text-center text-sm-right">
                    <h6 class="themeClr">ARE YOU ANDROID USER?</h6>
                    <p>Are You Ready to Play?</p>
                    <p>Download Now For Free!</p>
                    <a href="javascript:void(0)" class="download-btn">
                        <span><i class="fa fa-android"></i></span>
                        <div class="text-left">
                            <p>Available On</p>
                            <p>PLAY STORE</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 order-3 order-md-2 px-lg-4">
                    <img src="assets/img/downloadSec-mobile.png" class="img-fluid">
                </div>
                <div class="col-sm-6 col-md-4 mb-4 mb-md-0 mt-lg-5 order-2 order-md-3 text-center text-sm-left">
                    <h6 class="themeClr">ARE YOU iPHONE USER?</h6>
                    <p>Are You Ready to Play?</p>
                    <p>Download Now For Free!</p>
                    <a href="javascript:void(0)" class="download-btn iphone">
                        <span><i class="fa fa-apple"></i></span>
                        <div class="text-left">
                            <p>Available On</p>
                            <p>APP STORE</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Modal -->
    <div class="modal fade HomeVideoModal" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <div class="modal-body p-0">
                    <div class="video video-img-box">
                        <!-- <img src="assets/img/video-bg.png"> -->
                        <iframe width="100%" height="515" src="https://www.youtube.com/embed/rNShUMJ1sKo"
                            frameborder="0"
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                </div>
                <!-- <div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary">Save changes</button>
					</div> -->
            </div>
        </div>
    </div>
</div>
<?php include('footerHome.php') ?>