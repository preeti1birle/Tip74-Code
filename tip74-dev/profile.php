<?php include('header.php') ?>

	<div class="content profile burger mt-5" ng-controller="profileController" ng-init="getProfileInfo();getCountryList();" ng-cloak>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h3 class="heading">My Profile</h3>
				</div>
				<div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
					<div class="frofile-head">
						<div class="profile-img">
							<img ng-src="{{profileDetails.ProfilePic}}" on-error-src="assets/img/profile.svg">
                            <div class="profile_upload_btn">
                                <form name="fileUpload" id="fileUpload" enctype="multipart/form-data" novalidate="">
                                    <input ngf-select ngf-accept="'image/*'" onchange="angular.element(this).scope().updateProfilePic(this.files)" ng-model="picFile" name="file" type="file">
                                </form>
                            </div>
                        </div>
						<div class="account-holder">
							<h4>{{profileDetails.FirstName}}</h4>
							<p>{{profileDetails.Email}}</p>	
						</div>
					</div>
					<form class="profileContent" name="userform" ng-submit="updateProfile(userform)" novalidate="" autocomplete="off">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
                                    <label>Full Name</label>
                                    <input class="form-control customReadOnlyField" type="text" name="FirstName" ng-model="profileDetails.FirstName" ng-required="true" placeholder="Full Name" >
                                    <div ng-show="submitted && userform.FirstName.$error.required" class="text-danger form-error">
                                        *Full name is required.
                                    </div>
								</div>
							</div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <div class="dropdown  dropdown-start-parent">
										<span  class="dateTime_field">
										<input type="text" name="BirthDate" id="dropdownStart"  ng-required="true" placeholder="BirthDate" role="button" data-toggle="dropdown" data-target=".dropdown-start-parent" class="form-control" value="{{profileDetails.BirthDate | date:'yyyy-MM-dd'}}"></span>
										<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
											<datetimepicker data-ng-model="profileDetails.BirthDate" data-datetimepicker-config="{ dropdownSelector: '#dropdownStart', renderOn: 'end-date-changed',startView:'day', minView:'day' }" data-on-set-time="startDateOnSetTime()" data-before-render="startDateBeforeRender($dates)"></datetimepicker>
										</ul>
									</div>
                                    <div  ng-show="submitted && userform.BirthDate.$error.required" class="text-danger form-error">
                                        *Birth date is required.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select class="form-control customReadOnlyField" ng-model="profileDetails.Gender" name="Gender" ng-required="true">
                                        <option value="">Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                    <div ng-show="submitted && userform.Gender.$error.required" class="text-danger form-error">
                                        *Gender is required.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Country </label>
                                    <select class="form-control customReadOnlyField" ng-model="profileDetails.CountryCode" name="Country" ng-required="true" >
                                        <option value="">Select Country</option>
                                        <option ng-repeat="countries in countryList" value="{{countries.CountryCode}}" >{{countries.CountryName}}</option>
                                    </select>
                                    <div  ng-show="submitted && userform.Country.$error.required" class="text-danger form-error">
                                        *Country is required.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" class="form-control customReadOnlyField" placeholder="City" ng-model="profileDetails.CityName" name="City" ng-required="true">
                                    <div  ng-show="submitted && userform.City.$error.required" class="text-danger form-error">
                                        *City is required.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input class="form-control customReadOnlyField" placeholder="Address" type="text" ng-model="profileDetails.Address" name="Address" ng-required="true">
                                    <div  ng-show="submitted && userform.Address.$error.required" class="text-danger form-error">
                                        *Address is required.
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 submit">
								<button type="submit" class="btn bg-gradient">Save</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php include('footerHome.php') ?>