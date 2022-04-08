<style>
    .table td, .table th {
        padding: 0.5rem;
    }
</style>

<!-- colorbox html part start -->
<div class="container">

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

        {{--  DATE --}}
        <div class="step-content _fz-1">
            <div class="step-tab-panel" data-step="step1">
                <div class="row">
                    <div class="col">
                        <div>Monthly Sales & OPEX Summary in HKD (for the period of {{ $formattedStartDate }} to
                            {{ $formattedEndDate }})
                        </div>
                        <strong>
                            {{ $clientCode }}
                        </strong>
                    </div>
                </div>

                <hr class="my-2">

                <div class="row">
                    <div class="col-4">
                        <strong>Sales OverView</strong>
                        <table class="ml-4">
                            <tr>
                                <td class='w-75'>Total Sales Orders</td>
                                <td class='w-25'>{{ number_format($billingStatement->total_sales_orders) ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class='w-75'>Total Sales Amount</td>
                                <td class='w-25'>${{ number_format($billingStatement->total_sales_amount) ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class='w-75'>Total Expenses</td>
                                <td class='w-25'>${{ number_format($billingStatement->total_expenses) ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class='w-75'>Sales GP</td>
                                <td class='w-25'>${{ number_format($billingStatement->sales_gp) ?: '-' }}</td>
                            </tr>
                        </table>

                        <strong>Summary</strong>
                        <table class="ml-4">
                            <tr>
                                <td class='w-75'>Avolution Commission</td>
                                <td class='w-25'>
                                    ${{ number_format($billingStatement->avolution_commission) ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class='w-75'>Sales Tax Handling</td>
                                <td class='w-25'>${{ number_format($billingStatement->sales_tax_handling) ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class='w-75'>Sales Credit</td>
                                <td class='w-25'>${{ number_format($billingStatement->sales_credit) ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class='w-75'>OPEX Invoice</td>
                                <td class='w-25'>${{ number_format($billingStatement->opex_invoice) ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class='w-75'>FBA & Storage Fee Invoice</td>
                                <td class='w-25'>
                                    ${{ number_format($billingStatement->fba_storage_fee_invoice) ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class='w-75'>Final Credit</td>
                                <td class='w-25'>${{ number_format($billingStatement->final_credit) ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-8">
                        <table class="table _table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">A4LUTION ACCOUNT</th>
                                <th scope="col">CLIENT ACCOUNT</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr>
                                <th scope="row"><strong>Expenses Breakdown</strong></th>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">-Logistics Fee</th>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->a4_account_logistics_fee) ?: '-' }}
                                </td>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->client_account_logistics_fee) ?: '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">-FBA Fee</th>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->a4_account_fba_fee) ?: '-' }}
                                </td>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->client_account_fba_fee) ?: '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">-FBA Storage Fee</th>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->a4_account_fba_storage_fee) ?: '-' }}
                                </td>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->client_account_fba_storage_fee) ?: '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">-Platform Fee</th>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->a4_account_platform_fee) ?: '-' }}
                                </td>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->client_account_platform_fee) ?: '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">-Refund and Resend</th>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->a4_account_refund_and_resend) ?: '-' }}
                                </td>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->client_account_refund_and_resend) ?: '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">-Miscellaneous</th>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->a4_account_miscellaneous) ?: '-' }}
                                </td>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->client_account_miscellaneous) ?: '-' }}
                                </td>
                            </tr>
                            {{-- MARKETING FEE --}}
                            <tr>
                                <th scope="row"><strong>Marketing Fee</strong></th>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr class="">
                                <th scope="row">-Advertisement</th>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->a4_account_advertisement) ?: '-' }}
                                </td>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->client_account_advertisement) ?: '-' }}
                                </td>
                            </tr>
                            <tr class="">
                                <th scope="row">-Marketing And Promotion</th>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->a4_account_marketing_and_promotion) ?: '-' }}
                                </td>
                                <td class="text-right">
                                    ${{ number_format($billingStatement->client_account_marketing_and_promotion) ?: '-' }}
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- ./ row --}}

            </div>

            {{-- step2 --}}
            <div class="step-tab-panel" data-step="step2">
                <form id="step_form" role="form" class="form">
                    @csrf
                    <input type="hidden" name="billing_statement_id" value="{{ $billingStatement->id }}">

                    <div class="row">
                        <div class="col-3 form-group">
                            <label class="form-control-label _fz-1" for="step_report_date">Report Date</label>
                            <input class="form-control _fz-1" name="step_report_date" id="step_report_date"
                                   placeholder="step_report_date"
                                   type="text" value="{{ $formattedReportDate ?? '' }}" readonly>
                        </div>

                        <div class="col-3 form-group">
                            <label class="form-control-label _fz-1" for="issue_date">Issue Date</label>
                            <input class="form-control _fz-1" name="issue_date" id="issue_date" placeholder="issue_date"
                                   type="text" value="{{ $currentDate ?? '' }}">
                        </div>

                        <div class="col-4 form-group">
                            <label class="form-control-label _fz-1" for="client_contact">Client Contact</label>
                            <input class="form-control _fz-1" name="client_contact" id="client_contact"
                                   placeholder="client_contact"
                                   type="text" value="{{ $customerInfo['contact_person'] ?? '' }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-3 form-group">
                            <label class="form-control-label _fz-1" for="client_code">Client Code</label>
                            <input class="form-control _fz-1" name="client_code" id="client_code"
                                   placeholder="client_code"
                                   type="text" value="{{ $clientCode ?? '' }}" readonly>
                        </div>

                        <div class="col-3 form-group">
                            <label class="form-control-label _fz-1" for="payment_terms">
                                Payment Terms (# days net)
                            </label>
                            <input class="form-control _fz-1" name="payment_terms" id="payment_terms"
                                   placeholder="payment_terms" type="text" value="10">
                        </div>

                        <div class="col-4 form-group">
                            <label class="form-control-label _fz-1" for="client_company">Company Contact</label>
                            <input class="form-control _fz-1" name="client_company" id="client_company"
                                   placeholder="client_company" type="text"
                                   value="{{ $customerInfo['company_name'] ?? '' }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-3 form-group">
                            <label class="form-control-label _fz-1" for="supplier_name">Supplier Name</label>
                            <input class="form-control _fz-1" name="supplier_name" id="supplier_name"
                                   placeholder="supplier_name"
                                   type="text" value="{{ $supplierName ?? '' }}" readonly>
                        </div>

                        <div class="col-3 form-group">
                        </div>

                        <div class="col-3 form-group">
                            <label class="form-control-label _fz-1" for="client_address1">Street 1</label>
                            <input class="form-control _fz-1" name="client_address1" id="client_address1"
                                   placeholder="client_address1" type="text"
                                   value="{{ $customerInfo['address1'] ?? '' }}">
                        </div>

                        <div class="col-3 form-group">
                            <label class="form-control-label _fz-1" for="client_address2">Street 2</label>
                            <input class="form-control _fz-1" name="client_address2" id="client_address2"
                                   placeholder="client_address2" type="text"
                                   value="{{ $customerInfo['address2'] ?? '' }}">
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-3 offset-6 form-group">
                            <label class="form-control-label _fz-1" for="client_city">City</label>
                            <input class="form-control _fz-1" name="client_city" id="client_city"
                                   placeholder="client_city"
                                   type="text" value="{{ $customerInfo['city'] ?? '' }}">
                        </div>

                        <div class="col-2 form-group">
                            <label class="form-control-label _fz-1"
                                   for="client_district">District</label>
                            <input class="form-control _fz-1" name="client_district" id="client_district"
                                   placeholder="client_district"
                                   type="text" value="{{ $customerInfo['district'] ?? '' }}">
                        </div>

                        <div class="col-1 form-group">
                            <label class="form-control-label _fz-1" for="client_zip">Zip</label>
                            <input class="form-control _fz-1" name="client_zip" id="client_zip" placeholder="client_zip"
                                   type="text" value="{{ $customerInfo['zip'] ?? '' }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-3 offset-6 form-group">
                            <label class="form-control-label _fz-1"
                                   for="client_country">Country</label>
                            <input class="form-control _fz-1" name="client_country" id="client_country" type="text"
                                   placeholder="client_country" value="{{ $customerInfo['country'] ?? '' }}">
                        </div>
                    </div>

                    {{-- Button --}}
                    <div class="row justify-content-center align-items-center">
                        <div class="col-3">
                            <button class="btn btn-primary _fz-1" type="submit" id="inline_submit">Run Report</button>
                        </div>
                        <div class="col-3">
                            <button class="btn btn-primary _fz-1" type="button" id="cancel_btn">Cancel</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
<!-- colorbox html part end -->

<!-- sweetalert JS -->
<script src="{{ asset('js') }}/sweetalert.min.js"></script>

<script src="{{ asset('argon') }}/vendor/jquery/dist/jquery-3.1.0.js"></script>

<!-- jquery.form JS -->
<script src="{{ asset('argon') }}/js/jquery.form_4.3.0.js"></script>

<!-- jquery colorbox JS -->
<script src="{{ asset('argon') }}/vendor/colorbox/js/jquery.colorbox.js"></script>

<!-- bootstrap.bundle JS -->
<script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
@stack('js')

<!-- Argon CSS -->
<link type="text/css" href="{{ asset('argon') }}/vendor/colorbox/css/colorbox.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery.steps@1.1.2/dist/jquery-steps.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery.steps@1.1.2/dist/jquery-steps.min.js"></script>
@stack('css')
