<?php include('header.php'); ?>
<div class="mainContainer my_account" ng-controller="myAccountController" ng-init="getAccountInfo(true);getSetting()"
    ng-cloak>
    <div class="common_bg t-burger">
        <div class="container  ">
            <h1 class="text-center pb-3"> My Wallet </h1>
            <div class="row">
                <div class="col-md-12 res_account">
                    <div class="site_box">
                        <div class="accountContent">
                            <div class="accountHolder col-md-12">
                                <div class="accountHolder col-md-6">
                                    <div class="col-md-3">
                                        <img ng-src="{{profileDetails.ProfilePic}}"
                                            on-error-src="assets/img/profile.svg" class="">
                                    </div>
                                    <div class="col-md-3">
                                        <h5 class="themeClr">{{profileDetails.FirstName}}</h5>
                                        <span class="ng-binding">{{profileDetails.Email}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="clreafix"></div>
                            <ul class="account_grid">
                                <li class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <img src="assets/img/totalcash.svg" alt="" title="Deposit Cash" width="50px">
                                    </div>
                                    <div>
                                        <strong> Wallet Amount </strong>
                                        <span>{{moneyFormat(profileDetails.WalletAmount)}}</span>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center" ng-if="profileDetails.WinningAmount > 0">
                                    <div class="mr-3">
                                        <img src="assets/img/winning-cash.svg" alt="" title="Winning Cash" width="50px">
                                    </div>
                                    <div>
                                        <strong> Winning Cash </strong>
                                        <span>{{moneyFormat(profileDetails.WinningAmount)}}</span>
                                    </div>
                                </li>
                            </ul>

                            <div class="addAndWithdrawCash d-flex justify-content-center border-top py-3 border-bottom">
                                <div class="addCash col-lg-6 col-md-6 px-0 pr-md-1 px-lg-3">
                                    <a href="javascript:void(0)" ng-click="openPopup('add_money')"
                                        class="btn_primary px-4  w-100"> <i class="fa fa-money fa-1x mr-2"
                                            aria-hidden="true"></i> Add Cash </a>
                                </div>
                                <div class="col-lg-6 col-md-6 px-0 pr-md-1 px-lg-3">
                                    <a href="javascript:void(0)" ng-click="openPopup('withdrawPopup')"
                                        class="btn_primary btn_green px-4  w-100"> <i
                                            class="fa fa-credit-card fa-1x mr-2" aria-hidden="true"></i> Withdraw Cash
                                    </a>
                                </div>
                                <!-- <div class="col-lg-5 col-md-6 px-0 pl-md-1 px-lg-3">
                                    <a href="javascript:void(0)"
                                        ng-click="getEntryList();getUserBalance(SelectedWeekGUID);openPopup('entryPopup')"
                                        class="btn_gray px-4 w-100"> <i class="fa fa-credit-card fa-1x mr-2"
                                            aria-hidden="true"></i> Purchase Entry/Double ups </a>
                                </div> -->
                            </div>
                            <div class="transictionOption my_account">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link {{(activeTab == 'transaction')?'active':''}}"
                                            data-toggle="tab" href="javascript:void(0)"
                                            ng-click="ChangeTab('transaction');">Cash Transaction</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{(activeTab == 'withdrawal')?'active':''}}"
                                            data-toggle="tab" href="javascript:void(0)"
                                            ng-click="ChangeTab('withdrawal');">Cash Withdrawal</a>
                                    </li>
                                    <!-- <li class="nav-item">
                                        <a class="nav-link {{(activeTab == 'assignEntries')?'active':''}}" 
                                            data-toggle="tab" href="javascript:void(0)"
                                            ng-click="getEntryList();getUserBalance(SelectedWeekGUID);openPopup('assignPopup')">Assign Entries</a>
                                    </li> -->
                                </ul>
                                <div class="tab-content">
                                    <div id="withdrawal"
                                        class="tab-pane {{(activeTab == 'withdrawal')?'show active':''}}">
                                        <h5 class="pull-left p-2">Withdrawal</h5>
                                        <div class="table-responsive table-striped">
                                            <table class="mt-2 table table-borderless common_table text-white"
                                                style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>Payment Source</th>
                                                        <th>Amount</th>
                                                        <th>Date &amp; Time</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody scrolly>
                                                    <tr ng-repeat="transactionDetails in WithdrawTransactions | filter:{Status: withdraw_status}"
                                                        ng-if="TotalWithdrawTransactionCount > 0">
                                                        <td>{{transactionDetails.PaymentGateway}}</td>
                                                        <td>{{moneyFormat(transactionDetails.Amount)}}</td>
                                                        <td>{{transactionDetails.EntryDate| myDateFormat}}</td>
                                                        <td>{{transactionDetails.Status}}</td>
                                                    </tr>
                                                    <tr ng-if="TotalWithdrawTransactionCount == 0">
                                                        <td colspan="4" class="text-center">No transactions found.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="transaction"
                                        class="tab-pane {{(activeTab == 'transaction')?'show active':''}}">
                                        <div class="row">
                                            <div class="col">
                                                <h5 class="pull-left p-2">Transaction Details</h5>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="mt-2 table table-borderless common_table text-white">
                                                <thead>
                                                    <tr>
                                                        <th>Transaction ID</th>
                                                        <th>Details</th>
                                                        <th>Status</th>
                                                        <th>Opening Balance</th>
                                                        <th>Cr.</th>
                                                        <th>Dr.</th>
                                                        <th>Available Balance</th>
                                                        <th>Date &amp; Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody scrolly>
                                                    <tr ng-repeat="transactionDetails in transactions"
                                                        ng-if="TotalTransactionCount > 0">
                                                        <td style="word-break: break-all;">
                                                            {{transactionDetails.TransactionID}}</td>
                                                        <td>{{(transactionDetails.Narration == 'Join Contest Winning')?'Contest Winnings':transactionDetails.Narration}}
                                                        </td>
                                                        <td>{{transactionDetails.Status}}</td>
                                                        <td>
                                                            {{ moneyFormat(transactionDetails.OpeningWalletAmount)}}
                                                        </td>
                                                        <td>
                                                            {{ transactionDetails.TransactionType == 'Cr' ? moneyFormat(transactionDetails.WalletAmount) : moneyFormat(0.00)}}
                                                        </td>
                                                        <td>
                                                            {{ transactionDetails.TransactionType == 'Dr' ? moneyFormat(transactionDetails.WalletAmount) : moneyFormat(0.00)}}
                                                        </td>
                                                        <td>
                                                            {{ moneyFormat(transactionDetails.ClosingWalletAmount)}}
                                                        </td>
                                                        <td>{{transactionDetails.EntryDate | myDateFormat}}</td>
                                                    </tr>
                                                    <tr ng-if="TotalTransactionCount == 0">
                                                        <td colspan="8" class="text-center">No transactions found.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('footerHome.php'); ?>