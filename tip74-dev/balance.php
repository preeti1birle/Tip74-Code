<!--addmoney-->
<div class="modal fade centerPopup" popup-handler id="add_more_money" tabindex="-1" role="dialog" aria-labelledby="modalLabelSmall" aria-hidden="true" >
    <div class="modal-dialog custom_popup small_popup">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Low Balance</h4>
            </div>
            <div class="modal-body clearfix comon_body ammount_popup">
                
                    <table class="table text-center">
                        <thead>
                        <tr>
                            <th> Current Balance </th>
                            <th> Joining Amount </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="ng-binding"> {{moneyFormat(profileDetails.TotalCash)}} </p></td>
                            <td><p class="ng-binding">{{moneyFormat(ContestInfo.EntryFee)}}</p></td>
                        </tr>
                        </tbody>
                   </table>
                
                <form ng-submit="selectPaymentMode(amount,addCashForm)" name="addCashForm" novalidate="true">
                    <div class="form-group">
                        <label>Add cash to your account</label>
                        <input placeholder="Enter amount." class="form-control numeric" name="amount" type="text" ng-model="amount" numbers-only  ng-required="true" >
                        <div style="color:red" ng-show="cashSubmitted && addCashForm.amount.$error.required" class="form-error">
                            *Amount is Required
                        </div>
                        <div class="text-danger" ng-if="errorAmount">{{errorAmountMsg}}</div>
                    </div>
                    <div class="add_money">
                        <h4>ADD MORE CASH</h4>
                        <ul>
                            <li><button type="button" class="btn theme_bgclr" ng-click="addMoreCash(250)"  >₹ 250</button></li>
                            <li><button type="button" class="btn theme_bgclr" ng-click="addMoreCash(500)"  >₹ 500</button></li>
                            <li><button type="button" class="btn theme_bgclr" ng-click="addMoreCash(1000)" >₹ 1000</button></li>
                        </ul>
                    </div>
                    <div class="button_right text-center">
                        <button class="btn btn-submit bluebg" > ADD CASH </button>
                        
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>
<!--addmoney