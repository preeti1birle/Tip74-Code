<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>

<div class="panel-body" ng-controller="PageController" ng-init="getList();getRolePrivileges();getGroups();">
   <!-- Left menu -->
   <?php //include("menu.php"); ?>

   <!-- Top container -->
   <div class="clearfix mt-2 mb-2 d_flex">
      <span class="records hidden-sm-down" style="flex: 1;">
         <span ng-if="data.dataList.length" class="h5">Total records: {{TotalRecords}}</span>
      </span>

      <div class="custom_btn">      
         <button class="btn btn-success btn-sm ml-1 float-right" onclick='$("#add_group_modal").modal("show")'>Add User Type</button>
      </div>
     
   </div>
   <!-- Top container/ -->

   <div class="appContent panel manage-grp">
         <!-- data table -->
         <table class="table table-striped table-hover all-table-scroll  roles_table coupon_pad" ng-if="data.dataList.length">
            <!-- table heading -->
            <thead>
               <tr>
                  <th> User Type Name </th>
                  <th> Permitted Modules </th>
                  <th class="text-center"> User Count </th>
                  <th class="text-right"> Action </th>
               </tr>
            </thead>
            <!-- table body -->
            <tbody>
               <tr ng-repeat="(key, lists) in data.dataList">
                  <td>{{lists.UserTypeName}}</td>
                  <td>
                     <span ng-repeat="m in lists.PermittedModules">
                        {{m.ModuleTitle}}
                        {{$last ? '' : ($index==lists.PermittedModules.length-2) ? ' and ' : ',&nbsp;'}}
                     </span>
                  </td>
                  <td class="text-center">{{lists.UserCount}}</td>
                  <td class="text-center">
                     <div class="dropdown action_toggle" ng-if="lists.UserTypeID!='1'">
                        <button class="btn btn-secondary  btn-sm action custom_toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-if="data.UserGUID!=row.UserGUID"><i class="fa fa-ellipsis-h"></i></button>
                        <div class="dropdown-menu">
                           <a class="dropdown-item" href="" ng-click="loadFormEdit(key,lists.UserTypeGUID);">Edit</a>
                           <a class="dropdown-item" href="" ng-click="loadFormAddStaff(key,lists.UserTypeGUID);">Add User</a>
                           <a class="dropdown-item" href="" ng-if="lists.UserCount == 0" ng-click="loadFormDelete(key,lists.UserTypeGUID);">Delete</a>
                        </div>
                     </div>
                  </td>
               </tr>
            </tbody>
         </table>

         <!-- no record -->
         <p class="no-records text-center" ng-if="data.noRecords">
            <span ng-if="data.dataList.length">No more records found.</span>
            <span ng-if="!data.dataList.length">No records found.</span>
         </p>
  
   </div>

   <!-- Edit permission-modal -->
   <div class="modal fade" id="edit_permission_modal">
      <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
               <h3 class="modal-title h5">{{formData.UserTypeName}}</h3>        
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body clearfix">
               <form id="editForm" name="editForm" novalidate>
                  <div class="modal-body">
                     <div class="form-area">
                        <div class="row">

                           <div class="col-md-4">
                              <div class="form-group">
                                 <label class="control-label">User Type Name</label>
                                 <input name="GroupName" type="text" class="form-control" value="{{formData.UserTypeName}}" placeholder="User Type Name" maxlength="20">
                              </div>
                           </div>

                           <div class="col-md-4">
                              <div class="form-group">
                                 <h4 class="text-center">Set Permission</h4>
                              </div>
                           </div>

                           <!-- <div class="col-md-4">
                              <div class="form-group">
                                 <label class="control-label">Default Module</label>
                                 <select name="DefaultModule" id="DefaultModule" class="form-control chosen-select" ng-model="ModuleName" ng-change="SelDefaultModule(ModuleName)">
                                    <option value=""></option>
                                    <option ng-repeat="List in formData.PermittedModules" value="{{List.ModuleName}}" ng-selected="List.IsDefault=='Yes'">{{List.ModuleTitle}}</option>
                                 </select>
                              </div>
                           </div> -->
                        </div>
                        <hr>
                        <div class="tabs_wrapr">
                           <ul class="nav nav-tabs site_tabs" id="myTab" role="tablist">
                              <li class="nav-item">
                                 <a class="nav-link active" id="cricket-tab" data-toggle="tab" role="tab" ng-click="check(1)" aria-controls="cricket" aria-selected="true">cricket</a>
                              </li>
                              <li class="nav-item">
                                    <a class="nav-link" id="football-tab" data-toggle="tab" role="tab" ng-click="check(2)" aria-controls="football" aria-selected="false">football</a>
                              </li>
                              <li class="nav-item">
                                    <a class="nav-link" id="setting-tab" data-toggle="tab" role="tab" ng-click="check(3)" aria-controls="setting" aria-selected="false">setting</a>
                              </li>
                              <li class="nav-item">
                                    <a class="nav-link" id="gully-tab" data-toggle="tab" role="tab" ng-click="check(4)" aria-controls="setting" aria-selected="false">Gully</a>
                              </li>
                              <li class="nav-item">
                                    <a class="nav-link" id="auction-tab" data-toggle="tab" role="tab" ng-click="check(5)" aria-controls="setting" aria-selected="false">Auction</a>
                              </li>
                              <li class="nav-item">
                                    <a class="nav-link" id="user-tab" data-toggle="tab" role="tab" ng-click="check(6)" aria-controls="setting" aria-selected="false">User</a>
                              </li>
                              <li class="nav-item">
                                    <a class="nav-link" id="virtual-tab" data-toggle="tab" role="tab" ng-click="check(7)" aria-controls="setting" aria-selected="false">Virtual</a>
                              </li>
                           </ul>
                           <div class="tab-content" id="myTabContent">
                              <div class="tab-pane fade show active" id="cricket" role="tabpanel" aria-labelledby="cricket-tab">
                                 <!-- <h5>Cricket</h5> -->
                                 <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="series" role="tabpanel" aria-labelledby="series-tab">
                                       <div class="checkbox_list">
                                          <div class="customCheckbox checkbox" ng-show="List.ModelType == Type" ng-repeat="List in formData.PermittedModules|orderBy: 'ModelType'" ng-if="List.ModuleTitle!='Dashboard'">
                                             <input name="ModuleName[]" value="{{List.ModuleName}}" ng-show="List.ModelType == Type" class="coupon_question" ng-checked="List.Permission=='Yes'" type="checkbox" id="{{List.ModuleTitle}}">
                                             <label ng-show="List.ModelType == Type" for="{{List.ModuleTitle}}">{{List.ModuleTitle}}</label>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="tab-pane fade" id="football" role="tabpanel" aria-labelledby="football-tab">
                              </div>
                              <div class="tab-pane fade" id="setting" role="tabpanel" aria-labelledby="setting-tab">
                              </div>
                           </div>
                        </div>
                        <!-- <div class="" style="column-width: 170px;">
                           <div class="" ng-repeat="List in formData.PermittedModules" ng-if="List.ModuleTitle!='Dashboard'">
                              <div class="form-group">
                                 <div class="customCheckbox checkbox">
                                    <input name="ModuleName[]" value="{{List.ModuleName}}" class="coupon_question" ng-checked="List.Permission=='Yes'" type="checkbox" id="{{List.ModuleTitle}}">
                                    <label for="{{List.ModuleTitle}}">{{List.ModuleTitle}}</label>
                                 </div>
                              </div>
                           </div>
                        </div> -->
                     </div>
                  </div>

                  <div class="modal-footer">
                     <input type="hidden" name="UserTypeGUID" value="{{formData.UserTypeGUID}}" >   
                     <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                     <button type="submit" class="btn btn-success btn-sm" ng-disabled="editDataLoading" ng-click="editData();">Save</button>
                  </div>

               </form>
            </div>
            <!-- category footer -->
         </div>
      </div>
   </div>

   <!-- Add-User Type-Modal -->
   <div class="modal fade" id="add_group_modal">
      <div class="modal-dialog modal-md" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h3 class="modal-title h5">Add User Type</h3>      
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
               <div class="form-area">
                  <form id="add_form" name="add_form" autocomplete="off" >
                     <div class="row">
                        <div class="col-md-12">
                           <div class="form-group">
                              <label class="control-label">User Type Name</label>
                              <input name="GroupName" type="text" class="form-control" value="" placeholder="User Type Name" maxlength="20">
                           </div>
                        </div>
                     </div> 
                  </form>
               </div>
            </div>

            <div class="modal-footer">
               <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-success btn-sm" ng-disabled="addDataLoading" ng-click="addGroupData()">Save</button>
            </div>
         </div>
      </div>
   </div>

   <!-- add Modal -->
   <div class="modal fade" id="addStaff_model">
      <div class="modal-dialog modal-md" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h3 class="modal-title h5">Add <?php echo $this->ModuleData['ModuleName'];?></h3>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
               <div class="form-area">
                  <form id="addStaff_form" name="addStaff_form" autocomplete="off">

                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">First Name</label>
                              <input name="FirstName" type="text" class="form-control" value="" maxlength="50" placeholder="FIRST NAME">
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Last Name</label>
                              <input name="LastName" type="text" class="form-control" value="" maxlength="50" placeholder="LAST NAME">
                           </div>
                        </div>
                     </div>


                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Email</label>
                              <input name="Email" type="text" class="form-control" value="" maxlength="50" placeholder="EMAIL">
                           </div>
                        </div>

                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Password</label>
                              <input name="Password" type="password" class="form-control" value="" maxlength="12" placeholder="PASSWORD">
                           </div>
                        </div>

                     </div>

                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Contact No.</label>
                              <input name="PhoneNumber" type="text" class="form-control" value="" maxlength="10" placeholder="CONTACT NO">
                           </div>
                        </div>
                     </div>

                     <hr>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">User Type</label>
                              <select name="UserTypeID" id="UserTypeID" class="form-control chosen-select">
                                 <option value="{{formData.UserTypeID}}">{{formData.UserTypeName}}</option>
                              </select>
                           </div>
                        </div>

                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="control-label">Status</label>
                              <select name="Status" id="Status" class="form-control chosen-select">
                                 <option value="">Please Select</option>
                                 <option value="Pending">Pending</option>
                                 <option value="Verified" selected>Verified</option>
                                 <option value="Deleted">Deleted</option>
                                 <option value="Blocked">Blocked</option>
                              </select>
                           </div>
                        </div>

                     </div>

                     <input type="hidden" name="Source" value="Direct">

                  </form>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-success btn-sm" ng-disabled="editDataLoading"
                  ng-click="addStaffData()">Save</button>
            </div>
            
            <div ng-include="templateURLAdd"></div>
         </div>
      </div>
   </div>

   <!-- delete Modal -->
   <div class="modal fade" id="delete_model">
      <div class="modal-dialog modal-md" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h3 class="modal-title h5">Delete <?php echo $this->ModuleData['ModuleName'];?></h3>      
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <!-- form -->
            <form id="edit_form" name="edit_form" autocomplete="off">
               <div class="modal-body">
                  <div class="form-area">
                     <div class="form-group">
                        <p class="mt-2 text-center"><strong ng-bind="formData.UserTypeName"></strong></p>
                     </div>
                     <hr>
                     <div class="form-group">
                        <div class="">
                           <p><strong>Warning!</strong> This action is irreversible, this will delete this user type and all associated records. All data will be scrubbed and irretrievable.</p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                  <button type="button" color="deoco" class="btn btn-danger btn-sm" ng-disabled="deleteDataLoading" ng-click="deleteSelectedRecords(formData.UserTypeGUID)">Delete Permanently</button>
               </div>
            </form>
            <!-- /form -->
         </div>
      </div>
   </div>

</div><!-- Body/ -->

<script type="text/javascript">
      // $scope.SelDefaultModule = function(ModuleTitle) {

      //   $("#"+ModuleTitle).attr("ng-checked",true);      
      // }

   function SelDefaultModule(sel_val) {
      $("."+sel_val).attr("ng-checked",true); 

   }
</script>
