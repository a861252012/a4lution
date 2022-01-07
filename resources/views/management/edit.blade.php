<!-- colorbox html part start -->
<div class="container">
    <form id='monthly_fee_setting'>
        @csrf
        <div class="row my-4">
            <div class="col-md-9">
                <h2>Client Code: {{ $clientCode }}</h2>
            </div>
            <input value="{{ $clientCode }}" id="client_code_val" type="hidden">

            <div class="col-md-3">
                <button class="form-control _fz-1 btn _btn btn-primary" id="submit_btn" type="submit">
                    Save Commission
                </button>
            </div>
        </div>
        <div class="my-4"></div>
        {{-- Sales setting start--}}

        {{-- Sales Header --}}
        <div class="row">
            <div class="col">
                <h3>Sales</h3>
                <h5 class="text-muted">Select The Type Of Commission(s) to Calculate</h5>
            </div>
        </div>

        <div class="row" id="sales_area">
            <div class="col-md-5">
                <hr class="my-2">

                {{-- Sales setting base radio btn --}}
                <div class="row">
                    <div class="col-6 form-group mb-2 custom-control custom-radio custom-control-inline">
                        <input name="is_tiered_rate[{{$role['sales']}}]" id="sales_base" type="radio"
                               class="custom-control-input" value="F" data-id="{{$role['sales']}}" checked>
                        <label class="form-control-label _fz-1 custom-control-label" for="sales_base">Base</label>
                    </div>
                </div>

                {{-- Sales setting rate input --}}
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="sales_first_year_rate">Rate (First Year)</label>
                        <input class="form-control _fz-1" name="rate_base[{{$role['sales']}}]"
                               id="sales_first_year_rate" type="number" min="0" max="100" maxlength="3" required>
                    </div>
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="sales_renewal_rate">Rate (Renewal)</label>
                        <input class="form-control _fz-1" name="rate[{{$role['sales']}}]" id="sales_renewal_rate"
                               type="number" min="0" max="100" maxlength="3" required>
                    </div>
                </div>
            </div>

            {{-- Sales setting Commission Tier --}}
            <div class="col-md-7">
                <hr class="my-2">

                {{-- Sales setting Commission Tier radio btn --}}
                <div class="row">
                    <div class="col-6 form-group mb-2 custom-control custom-radio custom-control-inline">
                        <input name="is_tiered_rate[{{$role['sales']}}]" id="sales_tier" type="radio"
                               class="custom-control-input" data-id="{{$role['sales']}}" value="T">
                        <label class="form-control-label _fz-1 custom-control-label" for="sales_tier">
                            Commission Tier
                        </label>
                    </div>
                </div>

                {{-- Sales setting Commission Tier input --}}
                <div class="row">
                    <div class="col-2 form-group mb-2 align-self-center">
                        <div class="form-control-label _fz-1">Threshold</div>
                    </div>
                    <div class="col-5 form-group mb-2">
                        <input class="form-control _fz-1" name="threshold[{{$role['sales']}}]" type="number" min="1"
                               max="1000000" maxlength="7" id="sales_threshold">
                    </div>
                    <div class="col-2 form-group mb-2 _fz-1 align-self-center">
                        <div class="form-control-label _fz-1">(HKD)</div>
                    </div>
                </div>

                <div class="row">
                    {{-- Sales setting Commission Tier 1 --}}
                    <div class="col-2 form-group mb-2 align-self-center">
                        <div class="form-control-label _fz-1">Tier 1</div>
                    </div>
                    <div class="col-3 form-group mb-2">
                        <label class="form-control-label _fz-1" for="sales_tier_first_year_rate">Rate (First
                            Year)</label>
                        <input class="form-control _fz-1" name="tier_1_first_year[{{$role['sales']}}]" min="1" max="100"
                               maxlength="3" type="number" id="sales_tier_first_year_rate">
                    </div>
                    <div class="col-3 form-group mb-2">
                        <label class="form-control-label _fz-1" for="sales_tier_renewal_year_rate">Rate
                            (Renewal)</label>
                        <input class="form-control _fz-1" name="tier_1_over_a_year[{{$role['sales']}}]"
                               id="sales_tier_renewal_year_rate" type="number" min="1" max="100" maxlength="3">
                    </div>
                    <div class="col-4 form-group mb-2">
                        <label class="form-control-label _fz-1">Commission Payable</label>
                        <div class="form-control-label _fz-1" style="margin-top: 1rem;">1 Up to The Threshold</div>
                    </div>

                    {{-- Sales setting Commission Tier 2 --}}
                    <div class="col-2 form-group mb-2 align-self-center">
                        <div class="form-control-label _fz-1">Tier 2</div>
                    </div>
                    <div class="col-3 form-group mb-2">
                        <input class="form-control _fz-1" name="tier_2_first_year[{{$role['sales']}}]"
                               id="sales_tier_2_first_year" type="number" min="1" max="100">
                    </div>
                    <div class="col-3 form-group mb-2">
                        <input class="form-control _fz-1" name="tier_2_over_a_year[{{$role['sales']}}]"
                               id="sales_tier_2_over_a_year" type="number" min="1" max="100">
                    </div>
                    <div class="col-4 form-group mb-2 align-self-center">
                        <div class="form-control-label _fz-1">Upper Threshold</div>
                    </div>
                </div>

            </div>
            {{-- Sales setting Commission Tier End --}}

        </div>
        {{-- Sales setting end --}}

        <hr class="my-2 w-100">

        {{-- Account Service setting--}}

        {{-- Account Service Header --}}
        <div class="row">
            <div class="col">
                <h3>Account Service</h3>
                <h5 class="text-muted">Select The Type Of Commission(s) to Calculate</h5>
            </div>
        </div>

        <div class="row" id="as_area">
            <div class="col-md-5">
                <hr class="my-2">

                {{-- Account Service base radio btn --}}
                <div class="row">
                    <div class="col-6 form-group mb-2 custom-control custom-radio custom-control-inline">
                        <input name="is_tiered_rate[{{$role['account_service']}}]" id="account_service_base"
                               type="radio" class="custom-control-input" value="F"
                               data-id="{{$role['account_service']}}" checked>
                        <label class="form-control-label _fz-1 custom-control-label" for="account_service_base">
                            Base
                        </label>
                    </div>
                </div>

                {{-- Account Service rate input --}}
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="as_first_year_rate">Rate (First Year)</label>
                        <input class="form-control _fz-1" name="rate_base[{{$role['account_service']}}]"
                               id="as_first_year_rate" type="number" min="0" max="100" required>
                    </div>
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="as_renewal_rate">Rate (Renewal)</label>
                        <input class="form-control _fz-1" name="rate[{{$role['account_service']}}]"
                               id="as_renewal_rate" type="number" min="0" max="100" required>
                    </div>
                </div>
            </div>

            {{-- Account Service Commission Tier --}}
            <div class="col-md-7">
                <hr class="my-2">

                {{-- Account Service Commission Tier radio btn --}}
                <div class="row">
                    <div class="col-6 form-group mb-2 custom-control custom-radio custom-control-inline">
                        <input name="is_tiered_rate[{{$role['account_service']}}]" id="as_tier" type="radio"
                               class="custom-control-input" data-id="{{$role['account_service']}}" value="T">
                        <label class="form-control-label _fz-1 custom-control-label" for="as_tier">
                            Commission Tier
                        </label>
                    </div>
                </div>

                {{-- Account Service Commission Tier input --}}
                <div class="row">
                    <div class="col-2 form-group mb-2 align-self-center">
                        <div class="form-control-label _fz-1">Threshold</div>
                    </div>
                    <div class="col-5 form-group mb-2">
                        <input class="form-control _fz-1" name="threshold[{{$role['account_service']}}]"
                               id="as_threshold" type="number" min="1" max="1000000" maxlength="7">
                    </div>
                    <div class="col-2 form-group mb-2 _fz-1 align-self-center">
                        <div class="form-control-label _fz-1">(HKD)</div>
                    </div>
                </div>

                {{-- Account Service Commission Tier 1 --}}
                <div class="row">
                    <div class="col-2 form-group mb-2 align-self-center">
                        <div class="form-control-label _fz-1">Tier 1</div>
                    </div>
                    <div class="col-3 form-group mb-2">
                        <label class="form-control-label _fz-1" for="as_t1_first_year_rate">Rate (First Year)</label>
                        <input class="form-control _fz-1" name="tier_1_first_year[{{$role['account_service']}}]"
                               id="as_t1_first_year_rate" type="number" min="1" max="100">
                    </div>
                    <div class="col-3 form-group mb-2">
                        <label class="form-control-label _fz-1" for="as_t1_renewal_rate">Rate (Renewal)</label>
                        <input class="form-control _fz-1" name="tier_1_over_a_year[{{$role['account_service']}}]"
                               id="as_t1_renewal_rate" type="number" min="1" max="100">
                    </div>
                    <div class="col-4 form-group mb-2">
                        <label class="form-control-label _fz-1">Commission Payable</label>
                        <div class="form-control-label _fz-1" style="margin-top: 1rem;">1 Up to The Threshold</div>
                    </div>

                    {{-- Account Service Commission Tier 2 --}}
                    <div class="col-2 form-group mb-2 align-self-center">
                        <div class="form-control-label _fz-1">Tier 2</div>
                    </div>
                    <div class="col-3 form-group mb-2">
                        <input class="form-control _fz-1" name="tier_2_first_year[{{$role['account_service']}}]"
                               id="as_t2_first_year_rate" type="number" min="1" max="100">
                    </div>
                    <div class="col-3 form-group mb-2">
                        <input class="form-control _fz-1" name="tier_2_over_a_year[{{$role['account_service']}}]"
                               id="as_t2_over_a_year" type="number" min="1" max="100">
                    </div>
                    <div class="col-4 form-group mb-2 align-self-center">
                        <div class="form-control-label _fz-1">Upper Threshold</div>
                    </div>
                </div>

            </div>
            {{-- Account Service Commission Tier End --}}

        </div>
        {{-- Account Service Account Service end --}}

        <hr class="my-2 w-100">

        {{-- Operation setting --}}

        {{-- Operation setting Sales Header --}}
        <div class="row">
            <div class="col">
                <h3>Operation</h3>
                <h5 class="text-muted">Select The Type Of Commission(s) to Calculate</h5>
            </div>
        </div>

        <div class="row" id="op_area">
            <div class="col-md-5">
                <hr class="my-2">

                {{-- Operation setting base radio btn --}}
                <div class="row">
                    <div class="col-6 form-group mb-2 custom-control custom-radio custom-control-inline">
                        <input name="is_tiered_rate[{{$role['operation']}}]" id="operation_base" type="radio"
                               class="custom-control-input" value="F" data-id="{{$role['operation']}}" checked>
                        <label class="form-control-label _fz-1 custom-control-label" for="operation_base">
                            Base
                        </label>
                    </div>
                </div>

                {{-- Operation setting rate input --}}
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="op_first_year_rate">Rate (First Year)</label>
                        <input class="form-control _fz-1" name="rate_base[{{$role['operation']}}]" type="number"
                               id="op_first_year_rate" min="0" max="100" required>
                    </div>
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="op_renewal_rate">Rate (Renewal)</label>
                        <input class="form-control _fz-1" name="rate[{{$role['operation']}}]" id="op_renewal_rate"
                               type="number" min="0" max="100" required>
                    </div>
                </div>

            </div>

            {{-- Operation setting Commission Tier --}}
            <div class="col-md-7">
                <hr class="my-2">

                {{-- Operation setting Commission Tier radio btn --}}
                <div class="row">
                    <div class="col-6 form-group mb-2 custom-control custom-radio custom-control-inline">
                        <input name="is_tiered_rate[{{$role['operation']}}]" id="operation_tier" type="radio"
                               class="custom-control-input" data-id="{{$role['operation']}}" value="T">
                        <label class="form-control-label _fz-1 custom-control-label" for="operation_tier">
                            Commission Tier
                        </label>
                    </div>
                </div>

                {{-- Operation setting Commission Tier input --}}
                <div class="row">
                    <div class="col-2 form-group mb-2 align-self-center">
                        <div class="form-control-label _fz-1">Threshold</div>
                    </div>
                    <div class="col-5 form-group mb-2">
                        <input class="form-control _fz-1" name="threshold[{{$role['operation']}}]"
                               id="op_threshold" type="number" min="1" max="1000000" maxlength="7">
                    </div>
                    <div class="col-2 form-group mb-2 _fz-1 align-self-center">
                        <div class="form-control-label _fz-1">(HKD)</div>
                    </div>
                </div>

                {{-- Operation setting Commission Tier 1 --}}
                <div class="row">
                    <div class="col-2 form-group mb-2 align-self-center">
                        <div class="form-control-label _fz-1">Tier 1</div>
                    </div>
                    <div class="col-3 form-group mb-2">
                        <label class="form-control-label _fz-1" for="op_t1_first_year_rate">Rate (First Year)</label>
                        <input class="form-control _fz-1" name="tier_1_first_year[{{$role['operation']}}]"
                               id="op_t1_first_year_rate" type="number" min="1" max="100">
                    </div>
                    <div class="col-3 form-group mb-2">
                        <label class="form-control-label _fz-1" for="op_t1_renewal_rate">Rate (Renewal)</label>
                        <input class="form-control _fz-1" name="tier_1_over_a_year[{{$role['operation']}}]"
                               id="op_t1_renewal_rate" type="number" min="1" max="100">
                    </div>
                    <div class="col-4 form-group mb-2">
                        <label class="form-control-label _fz-1">Commission Payable</label>
                        <div class="form-control-label _fz-1" style="margin-top: 1rem;">1 Up to The Threshold</div>
                    </div>

                    {{-- Operation setting Commission Tier 2 --}}
                    <div class="col-2 form-group mb-2 align-self-center">
                        <div class="form-control-label _fz-1">Tier 2</div>
                    </div>
                    <div class="col-3 form-group mb-2">
                        <input class="form-control _fz-1" name="tier_2_first_year[{{$role['operation']}}]"
                               id="op_t2_first_year_rate" type="number" min="1" max="100">
                    </div>
                    <div class="col-3 form-group mb-2">
                        <input class="form-control _fz-1" name="tier_2_over_a_year[{{$role['operation']}}]"
                               id="op_t2_over_a_year_rate" type="number" min="1" max="100">
                    </div>
                    <div class="col-4 form-group mb-2 align-self-center">
                        <div class="form-control-label _fz-1">Upper Threshold</div>
                    </div>
                </div>

            </div>
            {{-- Operation setting Commission Tier End --}}

        </div>
        {{-- Operation setting Operation end --}}

    </form>
</div>
<script type="text/javascript">
    $(function () {
        $('#cancelBtn').click(function () {
            $.colorbox.close();
            // parent.jQuery.colorbox.close()
        });

        // prepare Options Object
        let options = {
            url: origin + '/management/monthlyFee/ajax/createSetting/' + $('#client_code_val').val(),
            type: 'POST',
            success: function (res) {
                swal({
                    icon: 'success',
                    text: res.msg,
                }).then(function (isConfirm) {
                    if (isConfirm) {
                        $.colorbox.close();
                    }
                });
            },
            error: function (e) {
                let errors = [];
                $.each(JSON.parse(e.responseText).errors, function (col, msg) {
                    errors.push(msg.toString());
                });

                swal({
                    icon: 'error',
                    text: errors.join("\n")
                });
            }
        };

        // pass options to ajaxForm
        $('#monthly_fee_setting').ajaxForm(options);

        // edit page 的 input 限制只能輸入整數
        $('input[type="number"]').on('input', function () {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        });

        //切換rate/tier選項時,會清空另一選項的值 (操作時似乎較卡頓)
        $('input[type="radio"]').on('change', function () {
            clearInputValue(
                $(this).val() === 'T',
                $(this).data('id')
            );
        });
    });

    //清除指定 selector 的值
    function clearInputValue(isTear, roleID) {
        let selector = getRateSelector(isTear, roleID).selector;
        let antiSelector = getRateSelector(isTear, roleID).anti_selector;

        $(selector).val('');
        $(antiSelector).prop('required', true);
        $(selector).prop('required', false);
    }

    //判斷jquery selector
    function getRateSelector(isTear, roleID) {
        let selector;
        let antiSelector;

        switch (roleID) {
            case 1:
                selector = (isTear) ?
                    '#sales_first_year_rate, #sales_renewal_rate' :
                    '#sales_area :not(#sales_first_year_rate, #sales_renewal_rate)';

                antiSelector = (!isTear) ?
                    '#sales_first_year_rate, #sales_renewal_rate' :
                    '#sales_area :not(#sales_first_year_rate, #sales_renewal_rate)';
                break;
            case 3:
                selector = (isTear) ?
                    '#op_first_year_rate, #op_renewal_rate' :
                    '#op_area :not(#op_first_year_rate, #op_renewal_rate)';

                antiSelector = (!isTear) ?
                    '#op_first_year_rate, #op_renewal_rate' :
                    '#op_area :not(#op_first_year_rate, #op_renewal_rate)';
                break;
            case 4:
                selector = (isTear) ?
                    '#as_first_year_rate, #as_renewal_rate' :
                    '#as_area :not(#as_first_year_rate, #as_renewal_rate)';

                antiSelector = (!isTear) ?
                    '#op_first_year_rate, #op_renewal_rate' :
                    '#op_area :not(#op_first_year_rate, #op_renewal_rate)';
                break;
        }

        return {
            'selector': selector,
            'anti_selector': antiSelector
        };
    }

    // $(function () {
    //     $('#cancelBtn').click(function () {
    //         $.colorbox.close();
    //         // parent.jQuery.colorbox.close()
    //     });
    //
    //     // prepare Options Object
    //     let options = {
    //         url: origin + '/management/monthlyFee/ajax/createSetting/' + clientCode,
    //         // responseType: 'blob', // important
    //         type: 'POST',
    //         success: function (res) {
    //             swal({
    //                 icon: 'success',
    //                 text: res.msg,
    //             }).then(function (isConfirm) {
    //                 if (isConfirm) {
    //                     $.colorbox.close();
    //                 }
    //             });
    //         },
    //         error: function (e) {
    //             let errors = [];
    //             $.each(JSON.parse(e.responseText).errors, function (col, msg) {
    //                 errors.push(msg.toString());
    //             });
    //
    //             swal({
    //                 icon: 'error',
    //                 text: errors.join("\n")
    //             });
    //         }
    //     };
    //
    //     // pass options to ajaxForm
    //     $('#monthly_fee_setting').ajaxForm(options);
    //
    //     // edit page 的 input 限制只能輸入整數
    //     $('input[type="number"]').on('input', function () {
    //         this.value = this.value.replace(/[^0-9\.]/g, '');
    //     });
    //
    //     //切換rate/tier選項時,會清空另一選項的值 (操作時較卡頓)
    //     // $('input[type="radio"]').on('change', function () {
    //     //     clearInputValue(
    //     //         ($(this).val() === 'T') ? 'tier' : 'rate',
    //     //         $(this).data('id')
    //     //     );
    //     // });
    //
    //     //切換rate/tier選項時,會清空另一選項的值
    //     $('input[name="is_tiered_rate[1]"]').on('change', function () {
    //         clearInputValue(
    //             ($(this).val() === 'T') ? 'tier' : 'rate',
    //             1
    //         );
    //     });
    //
    //     $('input[name="is_tiered_rate[3]"]').on('change', function () {
    //         clearInputValue(
    //             ($(this).val() === 'T') ? 'tier' : 'rate',
    //             3
    //         );
    //     });
    //
    //     $('input[name="is_tiered_rate[4]"]').on('change', function () {
    //         clearInputValue(
    //             ($(this).val() === 'T') ? 'tier' : 'rate',
    //             4
    //         );
    //     });
    //
    // });
    //
    // function ajaxGetMonthlyFeeView(clientCode) {
    //     $.colorbox({
    //         iframe: false,
    //         href: origin + '/management/monthlyFee/ajax/editView/' + clientCode,
    //         width: "80%",
    //         height: "90%",
    //         data: {
    //             _token: $('meta[name="csrf-token"]').attr('content')
    //         },
    //         onComplete: function () {
    //             $('#cancelBtn').click(function () {
    //                 $.colorbox.close();
    //                 // parent.jQuery.colorbox.close()
    //             });
    //
    //             // prepare Options Object
    //             let options = {
    //                 url: origin + '/management/monthlyFee/ajax/createSetting/' + clientCode,
    //                 // responseType: 'blob', // important
    //                 type: 'POST',
    //                 success: function (res) {
    //                     swal({
    //                         icon: 'success',
    //                         text: res.msg,
    //                     }).then(function (isConfirm) {
    //                         if (isConfirm) {
    //                             $.colorbox.close();
    //                         }
    //                     });
    //                 },
    //                 error: function (e) {
    //                     let errors = [];
    //                     $.each(JSON.parse(e.responseText).errors, function (col, msg) {
    //                         errors.push(msg.toString());
    //                     });
    //
    //                     swal({
    //                         icon: 'error',
    //                         text: errors.join("\n")
    //                     });
    //                 }
    //             };
    //
    //             // pass options to ajaxForm
    //             $('#monthly_fee_setting').ajaxForm(options);
    //
    //             // edit page 的 input 限制只能輸入整數
    //             $('input[type="number"]').on('input', function () {
    //                 this.value = this.value.replace(/[^0-9\.]/g, '');
    //             });
    //
    //             //切換rate/tier選項時,會清空另一選項的值 (操作時較卡頓)
    //             // $('input[type="radio"]').on('change', function () {
    //             //     clearInputValue(
    //             //         ($(this).val() === 'T') ? 'tier' : 'rate',
    //             //         $(this).data('id')
    //             //     );
    //             // });
    //
    //             //切換rate/tier選項時,會清空另一選項的值
    //             $('input[name="is_tiered_rate[1]"]').on('change', function () {
    //                 clearInputValue(
    //                     ($(this).val() === 'T') ? 'tier' : 'rate',
    //                     1
    //                 );
    //             });
    //
    //             $('input[name="is_tiered_rate[3]"]').on('change', function () {
    //                 clearInputValue(
    //                     ($(this).val() === 'T') ? 'tier' : 'rate',
    //                     3
    //                 );
    //             });
    //
    //             $('input[name="is_tiered_rate[4]"]').on('change', function () {
    //                 clearInputValue(
    //                     ($(this).val() === 'T') ? 'tier' : 'rate',
    //                     4
    //                 );
    //             });
    //
    //         }.bind(this)
    //     });
    // }
    //
    // //清除指定 selector 的值
    // function clearInputValue(type, roleID) {
    //     $(getRateSelector(type, roleID)).val('');
    // }
    //
    // //判斷jquery selector
    // function getRateSelector(type, roleID) {
    //     let selector;
    //     switch (roleID) {
    //         case 1:
    //             selector = (type === 'tier') ?
    //                 '#sales_first_year_rate, #sales_renewal_rate' :
    //                 '#sales_area :not(#sales_first_year_rate, #sales_renewal_rate)';
    //             break;
    //         case 3:
    //             selector = (type === 'tier') ?
    //                 '#op_first_year_rate, #op_renewal_rate' :
    //                 '#op_area :not(#op_first_year_rate, #op_renewal_rate)';
    //             break;
    //         case 4:
    //             selector = (type === 'tier') ?
    //                 '#as_first_year_rate, #as_renewal_rate' :
    //                 '#as_area :not(#as_first_year_rate, #as_renewal_rate)';
    //             break;
    //     }
    //
    //     return selector;
    // }
</script>