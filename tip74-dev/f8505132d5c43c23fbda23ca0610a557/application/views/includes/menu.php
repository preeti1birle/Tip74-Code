
<div class="top_header d-flex p-2 navbar-dark fixed-top">
   
    <div class="navbar-header col-lg-3 col-sm-9">  
        <button class="menu-toggel navbar-toggler navbar-toggler-right text-white collapsed" type="button" data-toggle="collapse"> 
          <span class="navbar-toggler-icon"> </span> 
        </button>
    </div>
    
    <div class="col-lg-6 text-center">
        <a class="navbar-brand" href="#"><img src="<?php echo API_URL;?>asset/img/emailer/logo.png" height="30"></a>
    </div>

    <div class="col-lg-3 d-flex justify-content-around col-sm-3">
<!--          <div class="notification dropdown"  ng-init="getNotifications()">-->
<!--          <div class="notification dropdown">
              <ul class="navbar-nav">
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle text-white" href="#" id="navbardrop" data-toggle="dropdown">  <i class="fa fa-bell" aria-hidden="true"></i><span class="badge ng-binding ng-scope" ng-if="data.notificationCount > 0">{{data.notificationCount}}</span></a>
                  
                  <div class="dropdown-menu dropdown-menu-right notify-list">
                      <ul  class="list-unstyled">
                        <li><div class="dropdown-title"> <h6>You have new notification</h6><a href="javascript:void(0)" ng-click="readAllNotification()">Mark All Read</a> | <a href="javascript:void(0)" ng-click="deleteAllNotification()">Delete All</a></div></li>
                    
                          <div class="notif-center p-3">
                            <a href="javascript:void(0)" ng-repeat="rows in notificationList" ng-click="readNotification(rows.NotificationID)">
                              <div class="notif-icon bg-primary"> <i class="fa fa-user-plus" ng-if="rows.NotificationPatternGUID != 'verify'"></i><i class="fa fa-file-text" ng-if="rows.NotificationPatternGUID == 'verify'"></i> </div>
                              <div class="notif-content pl-3">
                                <p class="mb-0"> {{rows.NotificationText}} </p>
                                <p class="mb-0"> {{rows.NotificationMessage}} </p>
                                <span class="time">{{rows.EntryDate | myDateFormat}}</span>

                              </div>
                            </a>
                            <p class="no-records text-center">
                                <span ng-if="notificationList">No more notification found.</span>
                                <span ng-if="!notificationList">No notification found.</span>
                            </p>
                          </div>
                        <li><div class="dropdown-title"><a href="NotificationList/">All Notifications</a></div></li>
                      </ul>
                  </div>
                </li>
              </ul>
          </div>-->
        <div class="dropdown action_toggle">
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" id="navbardrop" data-toggle="dropdown"> Welcome Admin </a>
              <div class="dropdown-menu dropdown-menu-right" data-display="static">
                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#changePassword_modal"  >Change Password</a>
                <a class="dropdown-item logout-btn" href="<?php echo base_url().'dashboard/signout/'.$this->SessionData['SessionKey'];?>">Sign Out</a>
              </div>
            </li>
          </ul>
        </div> 
    </div>
</div> 
<div class="main-navbar">
<nav class="navbar navbar-expand-sm fixed-top navigation">
      <div class="container-fluid">
    <!-- navigation -->
      <div class="navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav">
        <?php foreach($this->Menu as $Value){?>
          <?php if(empty($Value['ChildMenu'])){ ?>
            <li class="nav-item">        
            <a class="nav-link  <?php if($Value['ModuleName']==$this->ModuleData['ModuleName']){echo "active";} ?>" href="<?php echo base_url().$Value['ModuleName'];?>"><i class="fa fa-home"></i><?php echo $Value['ControlName'];?></a>
            </li>       
          <?php }else{ ?>
            <li class="nav-item dropdown <?php if($Value['ModuleName']==$this->ModuleData['ModuleName']){echo "active";} ?>">
              <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
              <i class="<?php echo $Value['ModuleIcon'];?>"></i><?php echo $Value['ControlName'];?>
            </a>

            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
              <?php foreach($Value['ChildMenu'] as $Value0){ ?>
                <li><a class="dropdown-item <?php if($Value0['ModuleName']==$this->ModuleData['ModuleName']){echo "active";} ?>" href="<?php echo base_url().$Value0['ModuleName'];?>"><?php echo $Value0['ControlName'];?></a></li>
              <?php } ?>
            </ul>
          </li>
        <?php } ?>

      <?php } ?>
    </ul>
    </div>


    <!-- /navigation -->

    <div class="attr-nav w-100"> 
      <!-- Right nav -->

    <!--   <div class="dropdown">
        <div class="nav-item">

          <a class="nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-click="getNotifications();">
            <i class="fa fa-bell-o fa-lg text-white" ></i>
            <span class="badge" ng-show="data.notificationCount>0">{{data.notificationCount}}</span>
          </a>

          <ul class="dropdown-menu notification_menu">
            <li class="text-light bg-dark px-2 py-2">Notifications</li>

            <li class="notification-box text-center" ng-if="!notificationList">
              <a class="dropdown-item" href="javascript:void(0)">
              <div class="row">
                <div class="not-txt">
                  No new notifications.
                </div>
              </div>
            </a>
          </li>

          <li class="notification-box" ng-repeat="(key, row) in notificationList">
            <a class="dropdown-item" href="./order" target="_self">
            <div class="row">
              <div class="not-txt">
                {{row.NotificationText}}
              </div>
              <div class="not-date"><small class="text-warning">{{row.EntryDate}}</small></div>
            </div>
          </a>
        </li>

      </ul>
    </div>
    </div> -->
    </div>
    </div>
    </nav>

    <div id="mainFrame">
      <div class="container-fluid">
        <section class="block">


