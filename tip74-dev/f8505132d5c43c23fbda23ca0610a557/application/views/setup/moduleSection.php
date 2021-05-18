<div class="mainContainer" ng-controller="PageController">


   <!-- Left menu -->
   <?php include("menu.php"); ?>


   <div class="appContent panel workflow">
      <div class="panel-heading">
         <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
      </div>
      <div class="panel-body">
         <p class="pl-3"> You can Manage your Culture system by selecting or de-selecting the below modules .</p>
         <div class="color-active-box d-flex">
            <ul>
               <li>
                  <a href="javascript:void(0);" class="clr-box1"><span ></span>Active</a>
               </li>
               <li>
                  <a href="javascript:void(0);" class="clr-box2"><span ></span>Inactive</a>
               </li>
               <li class="disabled">
                  <a href="javascript:void(0);" class="clr-box3 "><span ></span>Not Available</a>
               </li>
            </ul>
         </div>
         <div class="culture-box pt-4 pb-4">
            <ul class="first-box-active">
               <li class="active"><a href="javascript:void(0);" class="btn"> <img src="asset/admin/images/icon.png" width="50"> 
                  <input type="checkbox">
                  <label>Surveys</label>
               </a>
            </li>
            <li>
               <a href="javascript:void(0);" class="btn">
                  <img src="asset/images/icon1.png" width="40">
                  <input type="checkbox">
                  <label>Rewards</label>
                  <li><a href="javascript:void(0);" class="btn"><img src="asset/images/icon2.png" width="50"> 
                     <input type="checkbox">
                     <label>Inovate</label>
                  </a></li>
               </ul>
               <ul class="second-box-inactive">
                  <li><a href="javascript:void(0);" class="btn"> <img src="asset/images/icon6.png" width="50">
                     <input type="checkbox">
                     <label>Perform & Developnment</label>
                  </a>
               </li>
               <li><a href="javascript:void(0);" class="btn"> <img src="asset/images/icon4.png" width="50">
                  <input type="checkbox">
                  <label>Comply</label>
               </a>
            </li>
         </ul>
         <ul class="third-box-disable ">
            <li class=""><a href="javascript:void(0);" class="btn disabled "><img src="asset/images/icon3.png" width="50">
               <input type="checkbox">
               <label>Discipline</label></a>
            </li>
            <li><a href="javascript:void(0);" class="btn disabled"><img src="asset/images/icon5.png" width="50">  
               <input type="checkbox">
               <label>Care Book</label></a>
            </li>
            <li><a href="javascript:void(0);" class="btn disabled"><img src="asset/images/icon7.png" width="50">
               <input type="checkbox">
               <label>Engage</label></a>
            </li>
         </ul>
      </div>
      <!-- </div> -->
      <div class="form-group text-center pull-left">
         <button class="btn btn-primary">Save  </button>
         <button class="btn btn-default">Cancel </button>
      </div>
   </div>
</div>
<div class="appFooter">
   <b>Powered by</b> <img src="asset/images/logo.png" alt="">
</div>
</div>
