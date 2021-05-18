   <div class="canvasMenu menuFixed">
      <div class="menueHead text-center text-uppercase">
         <h3>Culture </h3>
         <h4>Setup</h4>
      </div>
      <ul class="nav navbar-nav scrollbar">

<?php if(CheckUserAccess($this->SessionData['PermittedModules'],'setup/setupmodule')){ ?> 
         <li class=" <?php if($this->uri->uri_string=="setup/setupmodule"){echo "active"; } ?>"><a href="setup/setupmodule" class="text-black-50">Module Selection</a></li>
<?php } ?>

<?php if(CheckUserAccess($this->SessionData['PermittedModules'],'setup/group')){ ?> 
         <li class=" <?php if($this->uri->uri_string=="setup/group"){echo "active"; } ?>"><a href="setup/group">Manage user roles</a></li>
<?php } ?>

<?php if(CheckUserAccess($this->SessionData['PermittedModules'],'setup/corehrsetup')){ ?> 
         <li class=" <?php if($this->uri->uri_string=="setup/corehrsetup"){echo "active"; } ?>"><a href="setup/corehrsetup">Setup the Core HR Module </a></li>
<?php } ?>

         <li class=" <?php if($this->uri->uri_string==""){echo "active"; } ?>"><a href="javascript:void(0);" class="text-black-50">Setup The Inform & Review Module </a></li>


         <li class=" <?php if($this->uri->uri_string==""){echo "active"; } ?>"><a href="javascript:void(0);" class="text-black-50">Setup the Engage Module  </a></li>


         <li class=" <?php if($this->uri->uri_string==""){echo "active"; } ?>"><a href="javascript:void(0);" class="text-black-50">Setup the Perform & Develop Module  </a></li>


         <li class=" <?php if($this->uri->uri_string==""){echo "active"; } ?>"><a href="javascript:void(0);" class="text-black-50">Setup the Comply Module </a></li>


         <li class=" <?php if($this->uri->uri_string==""){echo "active"; } ?>"><a href="javascript:void(0);" class="text-black-50">Setup the Innovate Module </a></li>


         <li class="dropdown policy-doc">
            <button class="btn btn-primary dropdown-toggle btn-policy" type="button" data-toggle="dropdown">Policy Documents
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
               <li><a href="{{hrm_base_url}}categories/" target="_blank">
                  Manage Categories</a>
               </li>
            </ul>
         </li>
      </ul>
   </div>
   <!--canvasMenu-->


  