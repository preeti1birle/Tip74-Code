<?php include 'header.php';?>

<div class="mainContainer" ng-init="getStaticPageContent('howitworks')">
    <div style="margin-top:75px;">
        <div class="top-header-title">
            <div class="container">
                <div class="d-flex justify-content-center align-items-center">
                    <h1> {{Title}}  </h1>
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