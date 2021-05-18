<div class="mainContainer" ng-controller="PageController">


   <!-- Left menu -->
   <?php include("menu.php"); ?>

   
   <div class="appContent panel setup_new dev-plan">
      <div class="panel-heading">
         <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
      </div>
      <div class="reviewIdea panel-body">
         <div class="customListTable">
            <table class="table">
               <thead>
                        <!-- <tr>
                           <th>Sub-menu Item</th>
                           <th>Links to this Setup in Sentrifugo</th>
                           <th></th>
                           
                        </tr> -->
                     </thead>
                     <tbody>
                        <tr>
                           <td>
                              <a class="open-link" href="{{hrm_base_url}}managemenus" target="_blank">
                                 Core-HR Module Selections 
                              </a>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <a class="open-link" href="{{hrm_base_url}}sitepreference/" target="_blank">
                                 Company Preferences (Site Config) 
                              </a>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <a class="open-link" href="{{hrm_base_url}}empconfiguration/" target="_blank">
                                 Employee Records Setup 
                              </a>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <a class="open-link" href="{{hrm_base_url}}leavemanagement/" target="_blank">
                                 Leave Management Options
                              </a>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <a class="open-link" href="{{hrm_base_url}}empleavesummary/" target="_blank">
                                 Employee Leave Summary
                              </a>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <a class="open-link" href="{{hrm_base_url}}addemployeeleaves/" target="_blank">
                                 Add Employee Leave
                              </a>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <a class="open-link" href="{{hrm_base_url}}holidaygroups/" target="_blank">
                                 Define Time-Off Categories 
                              </a>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <a class="open-link" href="{{hrm_base_url}}holidaydates/" target="_blank">
                                 Manage Company Holidays 
                              </a>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <a class="open-link" href="{{hrm_base_url}}" target="_blank">
                                 Company Assets Categories 
                              </a>
                           </td>
                        </tr>
                        <!-- <tr>
                           <td>
                             <a class="open-link" href="{{hrm_base_url}}expenses/expensecategories/" target="_blank">
                                Expense Categories 
                                </a> 
                           </td>
                           
                           </tr>
                             <tr>
                                 <td>
                                    <a class="open-link" href="{{hrm_base_url}}expenses/paymentmode/" target="_blank">
                                     Expense Payment Mode
                                   </a>
                                 </td>
                                 
                              </tr> -->
                              <tr>
                                 <td>
                                    <a class="open-link" href="{{hrm_base_url}}bgscreeningtype/" target="_blank">
                                       Background Check—Screening Types
                                    </a>
                                 </td>
                              </tr>
                              <tr>
                                 <td>  <a class="open-link" href="{{hrm_base_url}}agencylist/" target="_blank">
                                    Background Check—Agencies  
                                 </a>
                              </td>
                           </tr>
                           <tr>
                              <td>
                                 <a class="open-link" href="{{hrm_base_url}}disciplinaryviolation/" target="_blank">
                                    Disciplinary Violation Types  
                                 </a>
                              </td>
                           </tr>
                           <tr>
                              <td>
                                 <a class="open-link" href="{{hrm_base_url}}exit/exittypes/" target="_blank">
                                    Exit Procedure Types  
                                 </a> 
                              </td>
                           </tr>
                           <tr>
                              <td>
                                 <a class="open-link" href="{{hrm_base_url}}exit/configureexitqs/" target="_blank">
                                    Exit Interview Questions 
                                 </a>
                              </td>
                           </tr>
                           <tr>
                              <td>
                                 <a class="open-link" href="{{hrm_base_url}}exit/exitprocsettings/" target="_blank">
                                    Exit Procedure Settings 
                                 </a>
                              </td>
                           </tr>
                           <tr>
                              <td>
                                 <a class="open-link" href="{{hrm_base_url}}roles/" target="_blank">
                                    Roles & Privileges
                                 </a>
                              </td>
                           </tr>
                           <tr>
                              <td>
                                 <a class="open-link" href="{{hrm_base_url}}empscreening/" target="_blank">
                                    Roles & Privileges
                                 </a>
                              </td>
                           </tr>
                           <li class="dropdown policy-doc configuration pb-3">
                              <button class="btn btn-primary dropdown-toggle btn-policy" type="button" data-toggle="dropdown">Configuration
                                 <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu">
                                 <li><a href="{{hrm_base_url}}bgscreeningtype/" target="_blank">
                                 Screening types</a>
                              </li>
                              <li><a href="{{hrm_base_url}}agencylist/" target="_blank">
                              Agencies</a>
                           </li>
                        </ul>
                     </li>
                     <li class="dropdown policy-doc configuration pb-3">
                        <button class="btn btn-primary dropdown-toggle btn-policy" type="button" data-toggle="dropdown">Expense Tracking Setup
                           <span class="caret"></span></button>
                           <ul class="dropdown-menu">
                              <li><a  href="{{hrm_base_url}}expenses/expensecategories/" target="_blank">
                                 Expense Categories 
                              </a> 
                           </li>
                           <li><a  href="{{hrm_base_url}}expenses/paymentmode/" target="_blank">
                              Expense Payment Mode
                           </a>
                        </li>
                     </ul>
                  </li>
               </tbody>
            </table>
         </div>
      </div>


      <div class="appFooter">
         <b>Powered by</b> <img src="asset/images/logo.png" alt="">
      </div>
   </div>
   <!--appContent-->
</div>
<!--review-modal-->
<div class="modal" id="addreview">
   <div class="custompopup modal-md  modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header">
            <h4 class="modal-title">Review Ideas</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <!-- Modal body -->
         <div class="modal-body clearfix">
            <div class="reviewDetails">
               <ul>
                  <li><strong>Idea type :</strong> Create</li>
                  <li><strong>Submitted By :</strong> Crish Anderson </li>
                  <li><strong>Collaborators :</strong> Employee1  </li>
               </ul>
               <div class="reviewDes border p-2 mb-2">
                  <strong>Idea : </strong>
                  Generate Lorem Ipsum placeholder text for use in your graphic, print and web layouts, and discover plugins for your favorite writing, design and blogging tools.
               </div>
               <div class="reviewDes border p-2 mb-2">
                  <strong>Benefit : </strong>
                  Generate Lorem Ipsum placeholder text for use in your graphic, print and web layouts, and discover plugins for your favorite writing, design and blogging tools.
               </div>
               <div class="reviewDes border p-2 mb-2">
                  <strong>Suggested Measurement : </strong>
                  Generate Lorem Ipsum placeholder text for use in your graphic, print and web layouts, and discover plugins for your favorite writing, design and blogging tools.
               </div>
               <div class="row">
                  <div class="col-sm-5">
                     <div class="reviewAction">
                        <div class="dropdown">
                           <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                              Status
                           </button>
                           <div class="dropdown-menu">
                              <a class="dropdown-item" href="#">Pending</a>
                              <a class="dropdown-item" href="#">Reject</a>
                              <a class="dropdown-item" href="#">Approved</a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm-7">
                     <div class="reviewOption">
                        <div class="customCheckbox">
                           <input type="checkbox">
                           <label>Reward</label>
                        </div>
                     </div>
                     <div class="reviewOption">
                        <div class="customCheckbox">
                           <input type="checkbox">
                           <label>Save Summary to perform and develop</label>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <textarea class="form-control" placeholder="Comment:"></textarea>
               </div>
               <div class="form-group">
                  <button class="btn btn-primary">Save </button>
               </div>
            </div>
         </div>
         <!-- Modal footer -->
      </div>
   </div>
</div>
<!--review-modal-->
<script>
   function valueChanged(){
      if($('.coupon_question').is(":checked"))   
         $("#addreview").modal();
      else
         $("#addreview").hide();
   }
</script>
