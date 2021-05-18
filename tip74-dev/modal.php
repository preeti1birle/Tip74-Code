        <!-- Login Modal -->
        <div class="modal fade loginModal" id="LoginModal" popup-handler tabindex="-1" role="dialog"
            aria-labelledby="LoginModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document" ng-init="getCountryList()">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="loginModalLabel">Log In</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form name="signin" ng-submit="signIn(signin)" novalidate="" autocomplete="off">
                            <div class="form-group">
                                <i class="fa fa-envelope-o mr-2"></i>
                                <div class="input-field">
                                    <label for="exampleInputEmailID">Email ID</label>
                                    <input type="email" name="Keyword" ng-model="loginData.Keyword" class="form-control"
                                        id="exampleInputEmailID" aria-describedby="emailHelp" placeholder="Email"
                                        ng-required="true">
                                    <div ng-show="LoginSubmitted && signin.Keyword.$error.required"
                                        class="text-danger form-error">
                                        *Email is required.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <i class="fa fa-lock mr-2"></i>
                                <div class="input-field">
                                    <label for="exampleInputPassword1">Password</label>
                                    <input type="password" name="Password" ng-model="loginData.Password"
                                        ng-required="true" class="form-control" id="exampleInputPassword1"
                                        placeholder="Password">
                                    <div ng-show="LoginSubmitted && signin.Password.$error.required"
                                        class=" text-danger form-error">
                                        *Password is required.
                                    </div>
                                </div>
                            </div>
                            <div class="text-right forgotPswd"><a href="javascript:void(0)" data-dismiss="modal"
                                    aria-label="Close" data-toggle="modal" data-target="#forgotPasswordModal">Forgot
                                    Password ?</a></div>
                            <div class="submit">
                                <button class="btn bg-gradient">Login</button>
                            </div>
                            <div class="loginWithSocial">
                                <a href="javascript:void(0)"><img src="assets/img/facebook.png" class="mr-1"
                                        width="20"></i>Facebook</a>
                                <a href="javascript:void(0)"><img src="assets/img/google.png" class="mr-2"
                                        width="20">Google</a>
                            </div>
                            <div class="register">
                                <p>Don't have an account ? <a data-dismiss="modal" aria-label="Close"
                                        href="javascript:void(0)" class="register-btn"
                                        ng-click="openSignupModal()">Register</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- SignUp Modal -->
        <div class="modal fade signUpModal" id="SignUpModal" popup-handler tabindex="-1" role="dialog"
            aria-labelledby="SignUpModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="signUpModalLabel">Sign Up</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form name="signup" ng-submit="signUp(signup)" novalidate="" autocomplete="off">
                            <div class="form-group">
                                <i class="fa fa-user-o mr-2"></i>
                                <div class="input-field">
                                    <label for="exampleInputName">Name</label>
                                    <input type="text" class="form-control" name="fullName"
                                        ng-model="formData.FirstName" ng-required="true" id="exampleInputName"
                                        aria-describedby="emailHelp" placeholder="Name">
                                    <div ng-show="signupSubmitted && signup.fullName.$error.required"
                                        class="text-danger form-error">
                                        *Name is required.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <i class="fa fa-envelope-o mr-2"></i>
                                <div class="input-field">
                                    <label for="exampleInputEmail">Email</label>
                                    <input type="email" class="form-control	" name="email" ng-model="formData.Email"
                                        placeholder="Email Address" class="form-control"
                                        ng-pattern="/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/" ng-required="true"
                                        id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Email">
                                    <div ng-show="signupSubmitted && signup.email.$error.required"
                                        class="text-danger form-error">
                                        *Email is required.
                                    </div>
                                    <div ng-show="signup.email.$error.pattern" class="text-danger form-error">
                                        *Please enter valid email.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <i class="fa fa-user-o mr-2"></i>
                                <div class="input-field">
                                    <label for="exampleInputUserName">User Name</label>
                                    <input type="text" name="UserName" ng-model="formData.Username" class="form-control"
                                        id="exampleInputUserName" placeholder="User name" ng-required="true">
                                    <div ng-show="signupSubmitted && signup.UserName.$error.required"
                                        class="text-danger form-error">
                                        *User name is required.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <i class="fa fa-mobile mr-2"></i>
                                <div class="input-field">
                                    <label for="exampleInputMobile">Contact No.</label>
                                    <div class="input-group">
                                        <select class="drop-down" name="countryCode" ng-model="formData.PhoneCode" ng-required="true" ng-change="" style="border-color: var(--primaryClr);margin-top: 5px;">
                                            <option ng-repeat="code in countryList" value="{{code.phonecode}}">
                                            +{{code.phonecode}}
                                            </option>
                                        </select>
                                        <input type="text" name="Mobile" ng-model="formData.PhoneNumber"
                                        class="form-control" id="exampleInputMobile" placeholder="Contact No."
                                        ng-required="true" numbers-only maxlength="10">
                                    </div>
                                    
                                    <div ng-show="signupSubmitted && signup.countryCode.$error.required"
                                        class="text-danger form-error">
                                        *Phone code is required.
                                    </div>
                                    <div ng-show="signupSubmitted && signup.Mobile.$error.required"
                                        class="text-danger form-error">
                                        <br>
                                        *Contact no. is required.
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <i class="fa fa-lock mr-2"></i>
                                <div class="input-field">
                                    <label for="exampleInputPassword">Password</label>
                                    <input type="password" name="password" ng-model="formData.Password"
                                        ng-pattern="/^(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z])([a-zA-Z0-9@_%]+)$/"
                                        ng-minlength="6" ng-required="true" ng-change="formData.confrim_password=''"
                                        class="form-control" id="exampleInputPassword" placeholder="Password">
                                    <div ng-show="signupSubmitted && signup.password.$error.required"
                                        class="form-error text-danger">
                                        *Password is required.
                                    </div>
                                    <div ng-show="signup.password.$error.pattern || signup.password.$error.minlength"
                                        class="text-danger form-error">
                                        *Password must have one capital, one number and 6 character long.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <i class="fa fa-lock mr-2"></i>
                                <div class="input-field">
                                    <label for="exampleInputConfirmPassword">Confirm Password</label>
                                    <input type="password" name="confrim_password" ng-model="formData.confrim_password"
                                        ng-required="true" compare-to="formData.Password" class="form-control"
                                        id="exampleInputConfirmPassword" placeholder="Confirm Password">
                                    <div ng-show="signupSubmitted && signup.confrim_password.$error.required"
                                        class="text-danger form-error">
                                        *Confirm Password is required.
                                    </div>
                                    <div class="text-danger"
                                        ng-show="!signup.confrim_password.$error.required && signup.confrim_password.$error.compareTo">
                                        Your passwords must match.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <i class="fa fa-calendar mr-2"></i>
                                <div class="input-field">
                                    <label for="exampleInputDOB">Date Of Birth</label>
                                    <div class="dropdown  dropdown-start-parent">
                                        <span class="dateTime_field">
                                            <input type="text" name="DOB" id="dropdownStart" ng-required="true"
                                                placeholder="DOB" role="button" data-toggle="dropdown"
                                                data-target=".dropdown-start-parent" class="form-control"
                                                value="{{formData.BirthDate | date:'yyyy-MM-dd'}}"></span>
                                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                            <datetimepicker data-ng-model="formData.BirthDate"
                                                data-datetimepicker-config="{ dropdownSelector: '#dropdownStart', renderOn: 'end-date-changed',startView:'day', minView:'day' }"
                                                data-on-set-time="startDateOnSetTime()"
                                                data-before-render="startDateBeforeRender($dates)"></datetimepicker>
                                        </ul>
                                    </div>
                                    <div ng-show="signupSubmitted && signup.DOB.$error.required"
                                        class="text-danger form-error">
                                        *DOB is required.
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button type="submit" class="btn bg-gradient">Sign Up</button>
                            </div>
                            <div class="register">
                                <p class="mt-0">Already have an account ? <a href="javascript:void(0);"
                                        data-dismiss="modal" aria-label="Close" data-toggle="modal"
                                        data-target="#LoginModal">Login</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- forget password Modal -->
        <div class="modal fade loginModal forgotPasswordModal" id="forgotPasswordModal" popup-handler tabindex="-1"
            role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form name="forgotPasswordForm" ng-submit="sendEmailForgotPassword(forgotPasswordForm)"
                            novalidate="">
                            <div class="form-group">
                                <i class="fa fa-envelope-o mr-2"></i>
                                <div class="input-field">
                                    <label for="exampleInputEmail1">Email ID</label>
                                    <input type="email" name="Keyword" class="form-control" id="exampleInputEmail1"
                                        aria-describedby="emailHelp" placeholder="Email"
                                        ng-model="forgotPasswordData.Keyword" ng-required="true">
                                    <div ng-show="forgotEmailSubmitted && forgotPasswordForm.Keyword.$error.required"
                                        class="text-danger form-error">
                                        *Email is required.
                                    </div>
                                </div>
                            </div>
                            <div class="submit">
                                <button type="submit" class="btn bg-gradient">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- reset password Modal -->
        <div class="modal fade loginModal resetPassModal forgotPasswordModal" id="verifyForgotPassword" popup-handler>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="forgotPasswordModalLabel">Reset Password</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form name="verifyforgotPassword" ng-submit="verifyForgotPassword(verifyforgotPassword)"
                            novalidate="" autocomplete="off">
                            <div class="form-group">
                                <i class="fa fa-envelope-o mr-2"></i>
                                <div class="input-field">
                                    <input placeholder="OTP" name="opt" ng-model="forgotPassword.OTP" numbers-only
                                        class="form-control" type="text" ng-required="true">
                                    <div ng-show="forgotPasswordSubmitted && verifyforgotPassword.opt.$error.required"
                                        class="error form-error text-danger">
                                        *OTP is required.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <i class="fa fa-lock mr-2"></i>
                                <div class="input-field">
                                    <input type="password" name="password" ng-model="forgotPassword.Password"
                                        placeholder="New Password" class="form-control" ng-change="removeMassage()"
                                        ng-pattern="/^(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z])([a-zA-Z0-9@_%]+)$/"
                                        ng-minlength="6" ng-required="true">
                                    <div ng-show="forgotPasswordSubmitted && verifyforgotPassword.password.$error.required"
                                        class="error form-error text-danger">
                                        *Password is required.
                                    </div>
                                    <div ng-show="verifyforgotPassword.password.$error.pattern || verifyforgotPassword.password.$error.minlength"
                                        class="form-error error text-danger">*Password must have one capital, one number
                                        and 6 character long.
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <i class="fa fa-lock mr-2"></i>
                                <div class="input-field">
                                    <input type="password" ng-model="forgotPassword.confirmPass"
                                        compare-to="forgotPassword.Password" name="confirmPass"
                                        placeholder="Confirm New Password" class="form-control" ng-required="true"
                                        ng-change="removeMassage()">
                                    <div ng-show="forgotPasswordSubmitted && verifyforgotPassword.confirmPass.$error.required"
                                        class="error form-error text-danger">
                                        *Confirm password is required.
                                    </div>
                                    <div class="error text-danger"
                                        ng-show="!verifyforgotPassword.confirmPass.$error.required && verifyforgotPassword.confirmPass.$error.compareTo">
                                        Your passwords must match.</div>
                                </div>
                            </div>
                            <div class="submit">
                                <button type="submit" class="btn bg-gradient">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- change password Modal -->
        <div class="modal fade loginModal resetPassModal forgotPasswordModal" id="changePassword" popup-handler>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="forgotPasswordModalLabel">Change Password</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form name="changePasswordForm" ng-submit="changePassword(changePasswordForm)" novalidate=""
                            autocomplete="off">
                            <div class="form-group">
                                <i class="fa fa-lock mr-2"></i>
                                <div class="input-field">
                                    <input placeholder="Current Password" name="currentPass" ng-model="CurrentPassword"
                                        class="form-control" type="password" ng-required="true">
                                    <div ng-show="isFormSubmitted && changePasswordForm.currentPass.$error.required"
                                        class="text-danger form-error">
                                        *Current Password is required.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <i class="fa fa-lock mr-2"></i>
                                <div class="input-field">
                                    <input type="password" name="password" ng-model="Password"
                                        placeholder="New Password" class="form-control"
                                        ng-pattern="/^(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z])([a-zA-Z0-9@_%]+)$/"
                                        ng-minlength="6" ng-required="true">
                                    <div ng-show="isFormSubmitted && changePasswordForm.password.$error.required"
                                        class="text-danger form-error">
                                        *New Password is required.
                                    </div>
                                    <div ng-show="changePasswordForm.password.$error.pattern || changePasswordForm.password.$error.minlength"
                                        class="text-danger form-error">
                                        *Password must have one capital, one number and 6 character long.
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <i class="fa fa-lock mr-2"></i>
                                <div class="input-field">
                                    <input type="password" ng-model="confirmPass" compare-to="Password"
                                        name="confirmPass" placeholder="Confirm New Password" class="form-control"
                                        ng-required="true">
                                    <div ng-show="isFormSubmitted && changePasswordForm.confirmPass.$error.required"
                                        class="text-danger form-error">
                                        *Confirm password is required.
                                    </div>
                                    <div class="text-danger"
                                        ng-show="!changePasswordForm.confirmPass.$error.required && changePasswordForm.confirmPass.$error.compareTo">
                                        Your passwords must match.
                                    </div>
                                </div>
                            </div>
                            <div class="submit">
                                <button type="submit" class="btn bg-gradient">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Modal  -->
        <div class="modal fade site_modal team_modal" id="TeamModal" popup-handler tabindex="-1" role="dialog"
            aria-labelledby="TeamModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="TeamModalLabel" ng-if="ShowingTeam !=''">
                            {{(ShowingTeam == 'local')?TeamMatchInfo.TeamNameLocal:TeamMatchInfo.TeamNameVisitor}}</h5>
                        <h5 class="modal-title" id="TeamModalLabel" ng-if="ShowingTeam ==''">{{TeamMatchInfo.TeamName}}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mb-4">
                        <div class="text-center">
                            <img ng-if="ShowingTeam !=''"
                                ng-src="{{(ShowingTeam == 'local')?TeamMatchInfo.TeamFlagLocal:TeamMatchInfo.TeamFlagVisitor}}"
                                class="img-fluid team_profile"
                                alt="{{(ShowingTeam == 'local')?TeamMatchInfo.TeamNameShortLocal:TeamMatchInfo.TeamNameShortVisitor}}">
                            <img ng-if="ShowingTeam ==''" ng-src="{{TeamMatchInfo.TeamFlag}}"
                                class="img-fluid team_profile" alt="{{TeamMatchInfo.TeamNameShort}}">
                        </div>
                        <ul class="list-unstyled team_results">
                            <li class="bg-success">
                                <p>Won</p>
                                <h4 ng-if="ShowingTeam !=''">
                                    {{(ShowingTeam == 'local')?TeamMatchInfo.TeamStandingsLocal.Overall.Won:TeamMatchInfo.TeamStandingsVisitor.Overall.Won}}
                                </h4>
                                <h4 ng-if="ShowingTeam ==''">{{TeamMatchInfo.TeamStandings.Overall.Won}}</h4>
                                <span>{{TeamMatchInfo.WiningPercent}}%</span>
                            </li>
                            <li class="bg-warning">
                                <p>Draw</p>
                                <h4 ng-if="ShowingTeam !=''">
                                    {{(ShowingTeam == 'local')?TeamMatchInfo.TeamStandingsLocal.Overall.Draw:TeamMatchInfo.TeamStandingsVisitor.Overall.Draw}}
                                </h4>
                                <h4 ng-if="ShowingTeam ==''">{{TeamMatchInfo.TeamStandings.Overall.Draw}}</h4>
                                <span>{{TeamMatchInfo.DrawPercent}}%</span>
                            </li>
                            <li class="bg-danger">
                                <p>Lost</p>
                                <h4 ng-if="ShowingTeam !=''">
                                    {{(ShowingTeam == 'local')?TeamMatchInfo.TeamStandingsLocal.Overall.Lost:TeamMatchInfo.TeamStandingsVisitor.Overall.Lost}}
                                </h4>
                                <h4 ng-if="ShowingTeam ==''">{{TeamMatchInfo.TeamStandings.Overall.Lost}}</h4>
                                <span>{{TeamMatchInfo.LostPercent}}%</span>
                            </li>
                        </ul>
                        <ul class="list-unstyled areavice_win">
                            <li>
                                <span><i class="fa fa-home" aria-hidden="true"></i></span>
                                <p>Home Wins</p>
                                <h4 class="mb-0" ng-if="ShowingTeam !=''">
                                    {{(ShowingTeam == 'local')?TeamMatchInfo.TeamStandingsLocal.Home.Won:TeamMatchInfo.TeamStandingsVisitor.Home.Won}}/{{(ShowingTeam == 'local')?TeamMatchInfo.TeamStandingsLocal.Home.GamePlayed:TeamMatchInfo.TeamStandingsVisitor.Home.GamePlayed}}
                                </h4>
                                <h4 class="mb-0" ng-if="ShowingTeam ==''">
                                    {{TeamMatchInfo.TeamStandings.Home.Won}}/{{TeamMatchInfo.TeamStandings.Home.GamePlayed}}
                                </h4>
                            </li>
                            <li>
                                <span><i class="fa fa-bus" aria-hidden="true"></i></span>
                                <p>Away Wins</p>
                                <h4 class="mb-0" ng-if="ShowingTeam !=''">
                                    {{(ShowingTeam == 'local')?TeamMatchInfo.TeamStandingsLocal.Away.Won:TeamMatchInfo.TeamStandingsVisitor.Away.Won}}/{{(ShowingTeam == 'local')?TeamMatchInfo.TeamStandingsLocal.Away.GamePlayed:TeamMatchInfo.TeamStandingsVisitor.Away.GamePlayed}}
                                </h4>
                                <h4 class="mb-0" ng-if="ShowingTeam ==''">
                                    {{TeamMatchInfo.TeamStandings.Away.Won}}/{{TeamMatchInfo.TeamStandings.Away.GamePlayed}}
                                </h4>
                            </li>
                        </ul>
                        <div class="table-responsive">
                            <table class="table mt-4 team_table ">
                                <tbody>
                                    <tr class="{{match.ResultStatus == 'Won'?'bg-success':(match.ResultStatus == 'Lost')?'bg-danger':'bg-warning'}}"
                                        ng-repeat="match in TeamMatchList">
                                        <td><span><i class="fa fa-{{match.HomeAway == 'Home'?'home':'bus'}}"
                                                    aria-hidden="true"></i></span></td>
                                        <td>{{match.MatchStartDateTime | date :'dd MMM'}}</td>
                                        <td>{{match.OpponentTeamName}}</td>
                                        <td>{{match.MatchScoreDetails.FullTimeScore}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- <div class="modal-footer justify-content-center">
					<button type="button" class="btn btn-light m-0 px-4" data-dismiss="modal">Close</button>
				</div> -->
                </div>
            </div>
        </div>
        <!-- Team Modal End  -->

        <!-- Bank Details Modal -->
        <div class="modal fade  signUpModal" id="bankDetails" popup-handler>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="forgotPasswordModalLabel">Bank Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form name="updateBankInfo" ng-submit="updateBankDetails(updateBankInfo)" novalidate=""
                            autocomplete="off">
                            <div class="form-group">
                                <i class="fa fa-map-marker mr-2"></i>
                                <div class="input-field">
                                    <input placeholder="Address 1" name="Address" ng-model="profileDetails.Address"
                                        class="form-control" type="text" ng-required="true">
                                    <div ng-show="isBankFormSubmitted && updateBankInfo.Address.$error.required"
                                        class="text-danger form-error">
                                        *Address is required.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <i class="fa fa-map-marker mr-2" data-toggle="tooltip"
                                    title="Address must be physical address of recipient. PO Box not accepted."
                                    data-placement="right"></i>
                                <div class="input-field">
                                    <input type="text" name="Address1" ng-model="profileDetails.Address1"
                                        placeholder="Address 2" class="form-control" ng-required="true">
                                    <div ng-show="isBankFormSubmitted && updateBankInfo.Address1.$error.required"
                                        class="text-danger form-error">
                                        *Address 1 is required.
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <i class=" fa fa-university mr-2" data-placement="right" data-toggle="tooltip"
                                    title="International Bank Account Numbers (IBAN) are used globally and consist of up to 34 alphanumeric characters. They start with a 2 digit ISO country code (e.g. DE for Germany) and are mandatory for EURO transfers in Europe."></i>
                                <div class="input-field">
                                    <input type="text" ng-model="profileDetails.IBAN" name="IBAN"
                                        placeholder="International Bank Account Number" class="form-control"
                                        ng-required="true">
                                    <div ng-show="isBankFormSubmitted && updateBankInfo.IBAN.$error.required"
                                        class="text-danger form-error">
                                        *IBAN is required.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <i class="fa fa-lock mr-2" data-placement="right" data-toggle="tooltip"
                                    title="For Australia & South Africa, the Clearing or Branch code is mandatory. For India, the IFS (11 alpha-numeric characters) or 6-digit PIN code is mandatory. If you are sending money to UK & do not have the IBAN of the recipient, you will have to provide a Clearing code."></i>
                                <div class="input-field">
                                    <input type="text" ng-model="profileDetails.RoutingCode" name="RoutingCode"
                                        placeholder="Routing Code" class="form-control" ng-required="true">
                                    <div ng-show="isBankFormSubmitted && updateBankInfo.RoutingCode.$error.required"
                                        class="text-danger form-error">
                                        *RoutingCode is required.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <i class="fa fa-lock mr-2" data-placement="right" data-toggle="tooltip"
                                    title=" A SWIFT code is an international bank code that identifies particular banks worldwide. It is also known as a Bank Identifier Code(BIC)."></i>
                                <div class="input-field">
                                    <input type="text" ng-model="profileDetails.SwiftCode" name="SwiftCode"
                                        placeholder="Bank Identifier Code" class="form-control" ng-required="true">
                                    <div ng-show="isBankFormSubmitted && updateBankInfo.SwiftCode.$error.required"
                                        class="text-danger form-error">
                                        *SwiftCode is required.
                                    </div>
                                </div>
                            </div>
                            <div class="submit">
                                <button type="submit" class="btn bg-gradient">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lock Modal End  -->
        <!-- Team lineups Modal  -->
        <div class="modal fade site_modal teamLineup_modal" popup-handler id="LineUpModal" tabindex="-1" role="dialog"
            aria-labelledby="LineUpModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="LineUpModalLabel">Team Lineups</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row teams_flags">
                            <div class="col">
                                <img ng-src="{{LineUpMatchInfo.TeamFlagLocal}}"
                                    alt="{{LineUpMatchInfo.TeamNameShortLocal}}">
                                <span>{{LineUpMatchInfo.TeamNameLocal}}</span>
                            </div>
                            <div class="col">
                                <img ng-src="{{LineUpMatchInfo.TeamFlagVisitor}}"
                                    alt="{{LineUpMatchInfo.TeamNameShortVisitor}}">
                                <span>{{LineUpMatchInfo.TeamNameVisitor}}</span>
                            </div>
                        </div>
                        <div class="card" ng-if="TeamLineup.Goalkeepers.length > 0">
                            <h5 class="card-header text-center mb-0">Goalkeepers</h5>
                            <div class="row">
                                <div class="col-sm">
                                    <ul class="list-unstyled">
                                        <li ng-repeat="team in TeamLineup.Goalkeepers"
                                            ng-if="team.TeamGUID == LineUpMatchInfo.TeamGUIDLocal">{{team.PlayerName}}
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm">
                                    <ul class="list-unstyled">
                                        <li ng-repeat="team in TeamLineup.Goalkeepers"
                                            ng-if="team.TeamGUID == LineUpMatchInfo.TeamGUIDVisitor">{{team.PlayerName}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card" ng-if="TeamLineup.Defenders.length > 0">
                            <h5 class="card-header text-center mb-0">Defenders</h5>
                            <div class="row">
                                <div class="col-sm">
                                    <ul class="list-unstyled">
                                        <li ng-repeat="team in TeamLineup.Defenders"
                                            ng-if="team.TeamGUID == LineUpMatchInfo.TeamGUIDLocal">{{team.PlayerName}}
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm">
                                    <ul class="list-unstyled">
                                        <li ng-repeat="team in TeamLineup.Defenders"
                                            ng-if="team.TeamGUID == LineUpMatchInfo.TeamGUIDVisitor">{{team.PlayerName}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card" ng-if="TeamLineup.Midfielders.length > 0">
                            <h5 class="card-header text-center mb-0">Midfielders</h5>
                            <div class="row">
                                <div class="col-sm">
                                    <ul class="list-unstyled">
                                        <li ng-repeat="team in TeamLineup.Midfielders"
                                            ng-if="team.TeamGUID == LineUpMatchInfo.TeamGUIDLocal">{{team.PlayerName}}
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm">
                                    <ul class="list-unstyled">
                                        <li ng-repeat="team in TeamLineup.Midfielders"
                                            ng-if="team.TeamGUID == LineUpMatchInfo.TeamGUIDVisitor">{{team.PlayerName}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card" ng-if="TeamLineup.Forwards.length > 0">
                            <h5 class="card-header text-center mb-0">Forwards</h5>
                            <div class="row">
                                <div class="col-sm">
                                    <ul class="list-unstyled">
                                        <li ng-repeat="team in TeamLineup.Forwards"
                                            ng-if="team.TeamGUID == LineUpMatchInfo.TeamGUIDLocal">{{team.PlayerName}}
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm">
                                    <ul class="list-unstyled">
                                        <li ng-repeat="team in TeamLineup.Forwards"
                                            ng-if="team.TeamGUID == LineUpMatchInfo.TeamGUIDVisitor">{{team.PlayerName}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <h5 ng-if="TeamLineup.Goalkeepers.length == 0" class="card-header text-center mt-2 mb-0">
                            Line-ups is not available yet!</h5>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-light m-0 px-4" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Team lineups Modal End  -->

        <!-- Match Prediction Lock Modal End  -->
        <div class="modal fade site_modal team_modal" id="matchPredictionPopup" popup-handler tabindex="-1"
            role="dialog" aria-labelledby="PredictionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
						<strong>Match Prediction Lock Message</strong>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mb-4">
						<strong>{{predictionMessage}}</strong>
					</div>
					<div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-light m-0 px-4" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
		</div>
        <!-- Match Prediction Lock Modal End  -->
        
        <!--  doubleUp Modal -->
        <div class="modal fade site_modal team_modal doubleUp" id="RaceDubbleup" popup-handler tabindex="-1"
            role="dialog" aria-labelledby="PredictionLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
						<table class=" ">
                            <tr>
                                <th class="txt_gradient">Race 1</th>
                                <td>THE TRUMP - SOLIDARITE MARYE PIKE CUP</td>
                                <td>
                                    <div class="custom-control custom-checkbox ">
                                        <input class="custom-control-input" type="checkbox" id="race1check">
                                        <label for="race1check" class="custom-control-label">Double Up</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="txt_gradient">Race 2</th>
                                <td>THE ICE AX CUP</td>
                                <td>
                                    <div class="custom-control custom-checkbox ">
                                        <input class="custom-control-input" type="checkbox" id="race2check">
                                        <label for="race2check" class="custom-control-label">Double Up</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="txt_gradient">Race 3</th>
                                <td>THE VILLERS CUP</td>
                                <td>
                                    <div class="custom-control custom-checkbox ">
                                        <input class="custom-control-input" type="checkbox" id="race3check">
                                        <label for="race3check" class="custom-control-label">Double Up</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="txt_gradient">Race 4</th>
                                <td>THE SIR RAYMOND HEIN QC CUP</td>
                                <td>
                                    <div class="custom-control custom-checkbox ">
                                        <input class="custom-control-input" type="checkbox" id="race4check">
                                        <label for="race4check" class="custom-control-label">Double Up</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="txt_gradient">Race 5</th>
                                <td>THE SIR GAETAN DUVAL QC CUP</td>
                                <td>
                                    <div class="custom-control custom-checkbox ">
                                        <input class="custom-control-input" type="checkbox" id="race5check">
                                        <label for="race5check" class="custom-control-label">Double Up</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="txt_gradient">Race 6</th>
                                <td>THE NIPPY REGEN CUP</td>
                                <td>
                                    <div class="custom-control custom-checkbox ">
                                        <input class="custom-control-input" type="checkbox" id="race6check">
                                        <label for="race6check" class="custom-control-label">Double Up</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="txt_gradient">Race 7</th>
                                <td>THE SIR GAETAN DUVAL QC CUP</td>
                                <td>
                                    <div class="custom-control custom-checkbox ">
                                        <input class="custom-control-input" type="checkbox" id="race7check">
                                        <label for="race7check" class="custom-control-label">Double Up</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="txt_gradient">Race 8</th>
                                <td>THE EDOUARD DESBLEDS PLATE</td>
                                <td>
                                    <div class="custom-control custom-checkbox ">
                                        <input class="custom-control-input" type="checkbox" id="race8check">
                                        <label for="race8check" class="custom-control-label">Double Up</label>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
					<div class="modal-footer justify-content-center">
                        <button type="button" class="btn_gray" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn_gradient" >Lock</button>
                    </div>
                </div>
            </div>
		</div>
        <!-- doubleUp Modal End  -->


        <!-- Purchase entry Error Modal  -->
        <div class="modal fade site_modal team_modal" id="PurchaseEntryAlertPopup" popup-handler tabindex="-1"
            role="dialog" aria-labelledby="PredictionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
						<strong>Alert</strong>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mb-4">
						<strong>{{purchaseMessage}}</strong>
					</div>
					<div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-light m-0 px-4" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
		</div>
        <!-- Purchase entry Error Modal  End  -->

        <!-- Purchase entry Error Modal  -->
        <div class="modal fade site_modal team_modal" id="LockPredictionErrorModal" popup-handler tabindex="-1"
            role="dialog" aria-labelledby="PredictionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
						<strong>Alert</strong>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mb-4">
						<strong>Remaining prediction balance is 0, purchase new entry for further predictions</strong>
					</div>
					<div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-light m-0 px-4" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
		</div>
        <!-- Purchase entry Error Modal  End  -->
