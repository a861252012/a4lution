<!-- colorbox html part start -->
<div class="container">
    {{--                                            {{ dd(get_defined_vars()) }}--}}

    @if(count($errors) > 0 )
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul class="p-0 m-0" style="list-style: none;">
                @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="step-app" id="steps-nav">
        <ul class="step-steps">
            <li data-step-target="step1" class="text-center">Step 1</li>
            <li data-step-target="step2" class="text-center">Step 2</li>
        </ul>

        {{--        <div class="step-footer">--}}
        {{--            <button data-step-action="prev" class="step-btn">Previous</button>--}}
        {{--            <button data-step-action="next" class="step-btn">Next</button>--}}
        {{--            <button data-step-action="finish" class="step-btn">Finish</button>--}}
        {{--        </div>--}}


        {{--test start--}}
        {{--    <div id="smartwizard">--}}

        {{--        <div class="tab-content">--}}
        {{--            <div id="step-1" class="tab-pane" role="tabpanel">--}}
        {{--                Step content--}}
        {{--            </div>--}}
        {{--            <div id="step-2" class="tab-pane" role="tabpanel">--}}
        {{--                Step content--}}
        {{--            </div>--}}
        {{--            <div id="step-3" class="tab-pane" role="tabpanel">--}}
        {{--                Step content--}}
        {{--            </div>--}}
        {{--            <div id="step-4" class="tab-pane" role="tabpanel">--}}
        {{--                Step content--}}
        {{--            </div>--}}
        {{--        </div>--}}

        {{-- test end   --}}

        {{--  DATE --}}
        <div class="step-content">
            <div class="step-tab-panel" data-step="step1">
                <div class="row">
                    <div class="col">
                        <div>Monthly Sales & OPEX Summary in HKD (for the period of {{$formattedStartDate ?? ''}} to
                            {{$formattedEndDate ?? ''}})
                        </div>
                        <strong>
                            {{$clientCode ?? 'S53A'}}
                        </strong>
                    </div>
                </div>

                <hr>
                <div class="row">
                    {{--  Sales OverView --}}
                    <div class="col-6 form-group" style="font-size: 1.1rem;">
                        Sales OverView
                    </div>

                    {{--  Summary --}}
                    <div class="col-6 form-group" style="font-size: 1.1rem;">
                        Summary
                    </div>
                </div>

                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label">Total Sales Orders</label>
                    </div>

                    {{--                    <div class="col-2 form-group text-left">--}}
                    {{--                        {{'$'}}--}}
                    {{--                    </div>--}}

                    <div class="col-3 form-group text-right">
                        {{number_format($lists['total_sales_orders'], 3, '.', ',') ?? ''}}
                    </div>

                    <div class="col-3 form-group">
                        <label class="form-control-label">Avolution Commission</label>
                    </div>
                    <div class="col-3 form-group text-right">
                        {{number_format($lists['avolution_commission'], 3, '.', ',') ?? ''}}
                    </div>
                </div>
                <div class="row">

                    <div class="col-3 form-group">
                        <label class="form-control-label">Total Sales Amount</label>
                    </div>
                    <div class="col-3 form-group text-right">
                        {{number_format($lists['total_sales_amount'], 3, '.', ',') ?? ''}}
                    </div>

                    <div class="col-3 form-group">
                        <label class="form-control-label">Sales Tax Handling</label>
                    </div>
                    <div class="col-3 form-group text-right">
                        {{number_format($lists['sales_tax_handling'], 3, '.', ',') ?? ''}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label">Total Expenses</label>
                    </div>
                    <div class="col-3 form-group text-right">
                        {{number_format($lists['total_expenses'], 3, '.', ',') ?? ''}}
                    </div>

                    <div class="col-3 form-group">
                        <label class="form-control-label">Sales Credit</label>
                    </div>
                    <div class="col-3 form-group text-right">
                        {{number_format($lists['sales_credit'], 3, '.', ',') ?? ''}}
                    </div>
                </div>

                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label">Sales GP</label>
                    </div>
                    <div class="col-3 form-group text-right">
                        {{number_format($lists['sales_gp'], 3, '.', ',') ?? ''}}
                    </div>

                    <div class="col-3 form-group">
                        <label class="form-control-label">OPEX Invoice</label>
                    </div>
                    <div class="col-3 form-group text-right">
                        {{number_format($lists['opex_invoice'], 3, '.', ',') ?? ''}}
                    </div>

                </div>


                {{--  Summary --}}
                {{--                <div class="row">--}}
                {{--                    <div class="col form-group display-4">--}}
                {{--                        Summary--}}
                {{--                    </div>--}}
                {{--                </div>--}}

                {{--                <div class="row">--}}
                {{--                    <div class="col-3 form-group">--}}
                {{--                        <label class="form-control-label">Avolution Commission</label>--}}
                {{--                    </div>--}}
                {{--                    <div class="col-1 form-group">--}}
                {{--                        {{'$11433'}}--}}
                {{--                    </div>--}}
                {{--                </div>--}}
                {{--                <div class="row">--}}
                {{--                    <div class="col-3 form-group">--}}
                {{--                        <label class="form-control-label">Sales Tax Handling</label>--}}
                {{--                    </div>--}}
                {{--                    <div class="col-1 form-group">--}}
                {{--                        {{'$-'}}--}}
                {{--                    </div>--}}
                {{--                </div>--}}
                {{--                <div class="row">--}}
                {{--                    <div class="col-3 form-group">--}}
                {{--                        <label class="form-control-label">Sales Credit</label>--}}
                {{--                    </div>--}}
                {{--                    <div class="col-1 form-group">--}}
                {{--                        {{'$4588'}}--}}
                {{--                    </div>--}}
                {{--                </div>--}}

                {{--                <div class="row">--}}
                {{--                    <div class="col-3 form-group">--}}
                {{--                        <label class="form-control-label">OPEX Invoice</label>--}}
                {{--                    </div>--}}
                {{--                    <div class="col-1 form-group">--}}
                {{--                        {{'$4588'}}--}}
                {{--                    </div>--}}
                {{--                </div>--}}

                <div class="row">
                    <div class="offset-6 col-3 form-group">
                        <label class="form-control-label">FBA & Storage Fee Invoice</label>
                    </div>
                    <div class="col-3 form-group text-right">
                        {{number_format($lists['fba_storage_fee_invoice'], 3, '.', ',') ?? ''}}
                    </div>
                </div>

                <div class="row">
                    <div class="offset-6 col-3 form-group">
                        <label class="form-control-label">Final Credit</label>
                    </div>
                    <div class="col-3 form-group text-right">
                        {{number_format($lists['final_credit'], 3, '.', ',') ?? ''}}
                    </div>
                </div>

                {{-- table --}}
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">A4LUTION ACCOUNT</th>
                        <th scope="col">CLIENT ACCOUNT</th>
                    </tr>
                    </thead>

                    <tbody>

                    <tr>
                        <th scope="row" style="font-size: 1rem;"><strong>Expenses Breakdown</strong></th>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <th scope="row"><strong>-Logistics Fee</strong></th>
                        <td class="text-right">
                            {{number_format($lists['a4_account_logistics_fee'], 3, '.', ',') ?? ''}}
                        </td>
                        <td class="text-right">
                            {{number_format($lists['client_account_logistics_fee'], 3, '.', ',') ?? ''}}
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><strong>-FBA Fee</strong></th>
                        <td class="text-right">
                            {{number_format($lists['a4_account_fba_fee'], 3, '.', ',') ?? ''}}
                        </td>
                        <td class="text-right">
                            {{number_format($lists['client_account_fba_fee'], 3, '.', ',') ?? ''}}
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><strong>-FBA Storage Fee</strong></th>
                        <td class="text-right">
                            {{number_format($lists['a4_account_fba_storage_fee'], 3, '.', ',') ?? ''}}
                        </td>
                        <td class="text-right">
                            {{number_format($lists['client_account_fba_storage_fee'], 3, '.', ',') ?? ''}}
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><strong>-Platform Fee</strong></th>
                        <td class="text-right">
                            {{number_format($lists['a4_account_platform_fee'], 3, '.', ',') ?? ''}}
                        </td>
                        <td class="text-right">
                            {{number_format($lists['client_account_platform_fee'], 3, '.', ',') ?? ''}}
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><strong>-Refund and Resend</strong></th>
                        <td class="text-right">
                            {{number_format($lists['a4_account_refund_and_resend'], 3, '.', ',') ?? ''}}
                        </td>
                        <td class="text-right">
                            {{number_format($lists['client_account_refund_and_resend'], 3, '.', ',') ?? ''}}
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><strong>-Miscellaneous</strong></th>
                        <td class="text-right">
                            {{number_format($lists['a4_account_miscellaneous'], 3, '.', ',') ?? ''}}
                        </td>
                        <td class="text-right">
                            {{number_format($lists['client_account_miscellaneous'], 3, '.', ',') ?? ''}}
                        </td>
                    </tr>

                    {{-- MARKETING FEE --}}
                    <tr>
                        <th scope="row" style="font-size: 1rem;"><strong>MARKETING FEE</strong></th>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="">
                        <th scope="row">-ADVERTISEMENT</th>
                        <td class="text-right">
                            {{number_format($lists['a4_account_advertisement'], 3, '.', ',') ?? ''}}
                        </td>
                        <td class="text-right">
                            {{number_format($lists['client_account_advertisement'], 3, '.', ',') ?? ''}}
                        </td>
                    </tr>

                    <tr class="">
                        <th scope="row"><strong>-MARKETING AND PROMOTION</strong></th>
                        <td class="text-right">
                            {{number_format($lists['a4_account_marketing_and_promotion'], 3, '.', ',') ?? ''}}
                        </td>
                        <td class="text-right">
                            {{number_format($lists['client_account_marketing_and_promotion'], 3, '.', ',') ?? ''}}
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>

            {{-- step2 --}}
            <div class="step-tab-panel" data-step="step2">
                {{--                <form id="step_form" method="POST" action="/invoice/runReport" role="form" class="form">--}}
                <form id="step_form" role="form" class="form">
                    <input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="billing_statement_id" value="{{ $lists->id }}">

                    <div class="row">
                        <div class="col-3 form-group">
                            <label class="form-control-label" for="step_report_date">REPORT
                                DATE</label>
                            <input class="form-control" name="step_report_date" id="step_report_date"
                                   placeholder="step_report_date"
                                   type="text" value="{{$formattedReportDate}}" readonly>
                        </div>

                        <div class="col-3 form-group">
                            <label class="form-control-label" for="issue_date">ISSUE
                                DATE</label>
                            <input class="form-control" name="issue_date" id="issue_date" placeholder="issue_date"
                                   type="text" value="{{$currentDate}}">
                        </div>

                        <div class="col-4 form-group">
                            <label class="form-control-label" for="client_contact">CLIENT
                                CONTACT</label>
                            <input class="form-control" name="client_contact" id="client_contact"
                                   placeholder="client_contact"
                                   type="text" value="{{$customerInfo['contact_person']}}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-3 form-group">
                            <label class="form-control-label" for="client_code">CLIENT
                                CODE</label>
                            <input class="form-control" name="client_code" id="client_code" placeholder="client_code"
                                   type="text" value="{{$clientCode ?? ''}}" readonly>
                        </div>

                        <div class="col-3 form-group">
                            <label class="form-control-label" for="due_date">DUE
                                DATE</label>
                            <input class="form-control" name="due_date" id="due_date" placeholder="due_date"
                                   type="text" value="{{$nextMonthDate}}">
                        </div>

                        <div class="col-4 form-group">
                            <label class="form-control-label" for="client_company">COMPANY
                                CONTACT</label>
                            <input class="form-control" name="client_company" id="client_company"
                                   placeholder="client_company" type="text"
                                   value="{{$customerInfo['company_name']}}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-3 form-group">
                            <label class="form-control-label" for="supplier_name">SUPPLIER
                                NAME</label>
                            <input class="form-control" name="supplier_name" id="supplier_name"
                                   placeholder="supplier_name"
                                   type="text" value="{{$supplierName ?? ''}}" readonly>
                        </div>

                        <div class="col-3 form-group">
                            <label class="form-control-label" for="payment_terms">PAYMENT
                                TERMS (#
                                days net)</label>
                            <input class="form-control" name="payment_terms" id="payment_terms"
                                   placeholder="payment_terms"
                                   type="text" value="10">
                        </div>

                        <div class="col-3 form-group">
                            <label class="form-control-label" for="client_address1">STREET 1
                                CONTACT</label>
                            <input class="form-control" name="client_address1" id="client_address1"
                                   placeholder="client_address1" type="text"
                                   value="{{$customerInfo['address1']}}">
                        </div>

                        <div class="col-3 form-group">
                            <label class="form-control-label" for="client_address2">STREET 2
                                CONTACT</label>
                            <input class="form-control" name="client_address2" id="client_address2"
                                   placeholder="client_address2" type="text"
                                   value="{{$customerInfo['address2']}}">
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-3 offset-6 form-group">
                            <label class="form-control-label" for="client_city">CITY</label>
                            <input class="form-control" name="client_city" id="client_city" placeholder="client_city"
                                   type="text" value="{{$customerInfo['city']}}">
                        </div>

                        <div class="col-2 form-group">
                            <label class="form-control-label"
                                   for="client_district">DISTRICT</label>
                            <input class="form-control" name="client_district" id="client_district"
                                   placeholder="client_district" type="text" value="{{$customerInfo['district']}}">
                        </div>

                        <div class="col-1 form-group">
                            <label class="form-control-label" for="client_zip">ZIP</label>
                            <input class="form-control" name="client_zip" id="client_zip" placeholder="client_zip"
                                   type="text" value="{{$customerInfo['zip']}}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-3 offset-6 form-group">
                            <label class="form-control-label"
                                   for="client_country">COUNTRY</label>
                            <input class="form-control" name="client_country" id="client_country"
                                   placeholder="client_country" type="text" value="{{$customerInfo['country']}}">
                        </div>
                    </div>

                    {{-- Button --}}
                    <div class="row justify-content-center align-items-center">
                        <div class="col-3">
                            <button class="btn btn-primary" type="submit" id="inline_submit">RUN REPORT</button>
                        </div>
                        <div class="col-3">
                            <button class="btn btn-primary" type="button" id="cancel_btn">Cancel</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
    {{-- Button --}}
    {{--            <div class="row justify-content-center align-items-center">--}}
    {{--                <div class="col-3">--}}
    {{--                    <button class="btn btn-primary" type="button" id="inline_submit">Submit</button>--}}
    {{--                </div>--}}
    {{--                <div class="col-3">--}}
    {{--                    <button class="btn btn-primary" type="button" id="cancel_btn">Cancel</button>--}}
    {{--                </div>--}}
    {{--            </div>--}}

</div>
</div>

{{--</div>--}}


{{--        </div>--}}
{{--    </div>--}}
<!-- colorbox html part end -->

<!-- sweetalert JS -->
<script src="{{ asset('js') }}/sweetalert.min.js"></script>

<script src="{{ asset('argon') }}/vendor/jquery/dist/jquery-3.1.0.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn"
        crossorigin="anonymous"></script>
{{--<script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>--}}
{{--<script src="{{ asset('argon') }}/vendor/js-cookie/js.cookie.js"></script>--}}
{{--<script src="{{ asset('argon') }}/vendor/jquery.scrollbar/jquery.scrollbar.min.js"></script>--}}
{{--<script src="{{ asset('argon') }}/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js"></script>--}}
{{--<script src="{{ asset('argon') }}/vendor/lavalamp/js/jquery.lavalamp.min.js"></script>--}}

<!-- Optional JS -->
{{--<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>--}}
{{--<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>--}}

<!-- jquery colorbox JS -->
<script src="{{ asset('argon') }}/vendor/colorbox/js/jquery.colorbox.js"></script>。

{{--<script src="https://code.jquery.com/jquery-3.1.0.js"></script>--}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
        crossorigin="anonymous"></script>
<script src="{{ asset('argon') }}/vendor/js-cookie/js.cookie.js"></script>
<!-- Optional JS -->
<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>

{{-- TODO need to download plugin file --}}
<script src="https://cdn.jsdelivr.net/npm/smartwizard@5/dist/js/jquery.smartWizard.min.js"
        type="text/javascript"></script>

{{--<script src="{{ base_path() }}/vendor/techlab/smartwizard/dist/js/jquery.smartWizard.min.js"--}}
{{--        type="text/javascript"></script>--}}

@stack('js')

<!-- Argon CSS -->
<link type="text/css" href="{{ asset('argon') }}/vendor/colorbox/css/colorbox.css" rel="stylesheet">

<!-- smart_wizard_all CSS  TODO NEED TO BE DOWNLOAD-->
{{--<link href="https://cdn.jsdelivr.net/npm/smartwizard@5/dist/css/smart_wizard_all.min.css" rel="stylesheet"--}}
{{--      type="text/css">--}}


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery.steps@1.1.2/dist/jquery-steps.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery.steps@1.1.2/dist/jquery-steps.min.js"></script>

@stack('css')

@push('js')
    <script type="text/javascript">
        // $(function () {
            // $('#smartwizard').smartWizard({
            //     selected: 2, // Initial selected step, 0 = first step
            //     theme: 'default', // theme for the wizard, related css need to include for other than default theme
            //     justified: true, // Nav menu justification. true/false
            //     darkMode: false, // Enable/disable Dark Mode if the theme supports. true/false
            //     autoAdjustHeight: true, // Automatically adjust content height
            //     cycleSteps: false, // Allows to cycle the navigation of steps
            //     backButtonSupport: true, // Enable the back button support
            //     enableURLhash: true, // Enable selection of the step based on url hash
            //     transition: {
            //         animation: 'none', // Effect on navigation, none/fade/slide-horizontal/slide-vertical/slide-swing
            //         speed: '400', // Transion animation speed
            //         easing: '' // Transition animation easing. Not supported without a jQuery easing plugin
            //     },
            //     toolbarSettings: {
            //         toolbarPosition: 'bottom', // none, top, bottom, both
            //         toolbarButtonPosition: 'right', // left, right, center
            //         showNextButton: true, // show/hide a Next button
            //         showPreviousButton: true, // show/hide a Previous button
            //         toolbarExtraButtons: [] // Extra buttons to show on toolbar, array of jQuery input/buttons elements
            //     },
            //     anchorSettings: {
            //         anchorClickable: true, // Enable/Disable anchor navigation
            //         enableAllAnchors: false, // Activates all anchors clickable all times
            //         markDoneStep: true, // Add done state on navigation
            //         markAllPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
            //         removeDoneStepOnNavigateBack: false, // While navigate back done step after active step will be cleared
            //         enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
            //     },
            //     keyboardSettings: {
            //         keyNavigation: true, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
            //         keyLeft: [37], // Left key code
            //         keyRight: [39] // Right key code
            //     },
            //     lang: { // Language variables for button
            //         next: 'Next',
            //         previous: 'Previous'
            //     },
            //     disabledSteps: [], // Array Steps disabled
            //     errorSteps: [], // Highlight step with errors
            //     hiddenSteps: [] // Hidden steps
            // });

            // $('#smartwizard').smartWizard();


            // $(document).on("click", "button#cancel_btn", function () {
            //     $.colorbox.close();
            // return false;
            // });
        // });

        // $(document).bind('cbox_complete', function () {
        //     $('button#inline_submit').hide();
        //
        //     $(document).on("click", "button#cancel_btn", function () {
        //         // $.colorbox.close();
        //         // parent.$.colorbox.close();
        //
        //         $('#cboxOverlay').remove();
        //         $('#colorbox').remove();
        //
        //         // return false;
        //     });
        //
        //     //edit function
        //     $(document).on("click", "button#edit_btn", function () {
        //         let shipped_date = $("div .inline_shipped_date").attr("data-attr");
        //         let _token = $('meta[name="csrf-token"]').attr('content');
        //         let supplier = $('div#supplier').attr('data-label');
        //
        //         $.ajax({
        //             url: origin + '/orders/checkEditQualification',
        //             data: {shipped_date: shipped_date, _token: _token, supplier: supplier},
        //             type: 'post',
        //             success: function (res) {
        //                 if (res.status !== 'failed') {
        //                     swal({
        //                         icon: 'success',
        //                         text: 'editable now'
        //                     });
        //                     $('input[type=text]').attr('readonly', false);
        //                     $('button#inline_submit').show();
        //                 } else {
        //                     swal({
        //                         icon: 'error',
        //                         text: res.msg
        //                     });
        //                     $('button#inline_submit').hide();
        //                 }
        //             }
        //         });
        //     });
        //
        //     //submit function
        //     $(document).on("click", "button#inline_submit", function () {
        //         let data = {};
        //
        //         data.first_mile_shipping_fee = $("input[name='first_mile_shipping_fee']").val();
        //         data.first_mile_tariff = $("input[name='first_mile_tariff']").val();
        //         data.last_mile_shipping_fee = $("input[name='last_mile_shipping_fee']").val();
        //         data.paypal_fee = $("input[name='paypal_fee']").val();
        //         data.transaction_fee = $("input[name='transaction_fee']").val();
        //         data.fba_fee = $("input[name='fba_fee']").val();
        //         data.other_fee = $("input[name='other_fee']").val();
        //         data.marketplace_tax = $("input[name='marketplace_tax']").val();
        //         data.cost_of_point = $("input[name='cost_of_point']").val();
        //         data.exclusives_referral_fee = $("input[name='exclusives_referral_fee']").val();
        //         data.product_id = $("button#edit_btn").attr('data-attr');
        //
        //         $.ajaxSetup({
        //             headers: {
        //                 'X-CSRF-TOKEN': $('#csrf_token').val()
        //             }
        //         });
        //
        //         $.ajax({
        //             url: origin + '/orders/edit/orderDetail',
        //             data: data,
        //             type: 'post',
        //             success: function (res) {
        //                 if (res.status === 'failed') {
        //                     swal({
        //                         icon: 'error',
        //                         text: 'update failed'
        //                     });
        //                 } else {
        //                     swal({
        //                         icon: res.status,
        //                         text: res.msg
        //                     });
        //                     $('input[type=text]').attr('readonly', false);
        //                 }
        //             }, error: function (error) {
        //                 swal({
        //                     icon: 'error',
        //                     text: error
        //                 });
        //             }
        //         });
        //     });
        // });
    </script>
@endpush
