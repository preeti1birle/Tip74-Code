<?php include 'header.php';?>

<div class="mainContainer" ng-controller="headerController" ng-init="getStaticPageContent()">
    <div style="margin-top:75px;">
        <div class="top-header-title">
            <div class="container">
                <div class="d-flex justify-content-center align-items-center">
                    <h1> {{Title}}  </h1>
                </div>
                <div class="dropdown dropdown-btn-align-right">
                    <button class="btn dropdown-btn dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{GamesType}}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" ng-click="gameTypeSelection('Soccer')" href="SoccerRules">Soccer</a>
                        <a class="dropdown-item" ng-click="gameTypeSelection('Horse Racing')" href="HorseRacingRules">Horse Racing</a>
                    </div>
                </div>
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