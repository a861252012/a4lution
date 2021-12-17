
<link href="{{ asset('css/plugins/bootstrap-duallistbox/bootstrap-duallistbox.css') }}" rel="stylesheet">
<style>
    .table-sm td, .table-sm th {
        padding: .1rem .5rem;
    }
</style>

<!-- colorbox html part start -->
<div class="container">
    <form id='customer_form' method="POST">
        @csrf
        @method('PATCH')

        <div class="row">
            {{-- Basic Information --}}
            <div class="col-md-7">
                <h3>Basic Information</h3>
                <hr class="my-2">
                
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="client_code">
                            Client Code <span class="text-red">*<span>
                        </label>
                        <input class="form-control _fz-1" name="client_code" id="client_code" 
                            placeholder="client_code" type="text" value="{{ $customer->client_code }}">
                    </div>
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="company_name">
                            Company Name
                        </label>
                        <input class="form-control _fz-1" name="company_name" id="company_name" 
                            placeholder="company_name" type="text" value="{{ $customer->company_name }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="company_contact">Company Contact</label>
                        <input class="form-control _fz-1" name="company_contact" id="company_contact" 
                            placeholder="company_contact" type="text" value="{{ $customer->contact_person }}">
                    </div>
                    <div class="col-6">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="street1">Street1</label>
                        <input class="form-control _fz-1" name="street1" id="street1" 
                        placeholder="street1" type="text" value="{{ $customer->address1 }}">
                    </div>
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="street2">Street2</label>
                        <input class="form-control _fz-1" name="street2" id="street2" 
                        placeholder="street2" type="text" value="{{ $customer->address2 }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="city">City</label>
                        <input class="form-control _fz-1" name="city" id="city" 
                            placeholder="city" type="text" value="{{ $customer->city }}">
                    </div>
                    <div class="col-3 form-group mb-2">
                        <label class="form-control-label _fz-1" for="district">District</label>
                        <input class="form-control _fz-1" name="district" id="district" 
                            placeholder="district" type="text" value="{{ $customer->district }}">
                    </div>
                    <div class="col-3 form-group mb-2">
                        <label class="form-control-label _fz-1" for="zip">Zip</label>
                        <input class="form-control _fz-1" name="zip" id="zip" 
                            placeholder="zip" type="text" value="{{ $customer->zip }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="country">Country</label>
                        <input class="form-control _fz-1" name="country" id="country" 
                            placeholder="country" type="text" value="{{ $customer->country }}">
                    </div>
                    <div class="col-6">
                    </div>
                </div>
            </div>
            {{-- ./ Basic Information --}}

            {{-- Advanced Setting --}}
            <div class="col-md-5">
                <h3>Advanced Setting</h3>
                <hr class="my-2">
                <div class="form-group mb-2">
                    <label class="form-control-label _fz-1" for="sales_region">
                        Sales Region <span class="text-red">*<span>
                    </label>
                    <select class="form-control _fz-1" data-toggle="select" name="sales_region" id="sales_region">
                        <option value="HK" @if($customer->sales_region === 'HK') {{ 'selected' }} @endif>
                            HK
                        </option>
                        <option value="TW" @if($customer->sales_region === 'TW') {{ 'selected' }} @endif>
                            TW
                        </option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="form-control-label _fz-1" for="contract_date">
                        Contract Date <span class="text-red">*<span>
                    </label>
                    <input class="form-control _fz-1" name="contract_date" id="contract_date" 
                        placeholder="Contract Date" type="text" value="{{ optional($customer->contract_date)->format('Y-m-d') }}">
                </div>
                <div class="form-group mb-2">
                    {{-- TODO: 畫面可能需要再調整 --}}
                    <label class="form-control-label _fz-1">Status</label>
                    <div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="active" name="active" class="custom-control-input" value="1"
                                {{ $customer->isActive() ? 'checked' : '' }}>
                            <label class="custom-control-label" for="active">Active</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="inActive" name="active" class="custom-control-input" value="0" 
                                {{ $customer->isInActive() ? 'checked' : '' }}>
                            <label class="custom-control-label" for="inActive">InActive</label>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label class="form-control-label _fz-1" for="">Sales Rep</label>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#salesRepModal">
                        Setting
                    </button>
                    
                    <input type="hidden" name="sales_reps" value="{{ implode('|', array_keys($selectedSalesReps)) }}">
                    <p class="_fz-1 mt-1 _p-sales-reps">{{ implode('、', $selectedSalesReps) }}</p>
                </div>
            </div>
            {{-- ./ Advanced Setting --}}
        </div>

        {{-- Commission Structure --}}
        <div class="d-flex flex-column mt-4">
            <h3>Commission Structure</h3>
            <hr class="my-2 w-100">
            <h3>Calculate Type <span class="text-red">*<span></h3>
            <hr class="my-2 w-100">
            @inject('commission', 'App\Constants\Commission')
            @php
                $skuChecked = (optional($customer->commission)->calculate_type === $commission::CALCULATE_TYPE_SKU) ? 'checked' : '';
                $promotionChecked = (optional($customer->commission)->calculate_type === $commission::CALCULATE_TYPE_PROMOTION) ? 'checked' : '';
                $tierChecked = (optional($customer->commission)->calculate_type === $commission::CALCULATE_TYPE_TIER) ? 'checked' : '';
                $basicChecked = (optional($customer->commission)->calculate_type === $commission::CALCULATE_TYPE_BASIC_RATE) ? 'checked' : '';
            @endphp
            <div class="custom-control custom-radio mb-2">
                <input type="radio" id="is_sku" name="calculate_type" class="custom-control-input" value="{{ $commission::CALCULATE_TYPE_SKU }}" {{ $skuChecked }}>
                <label class="custom-control-label" style="font-size: 0.65rem;" for="is_sku">SKU</label>
            </div>
            <div class="custom-control custom-radio mb-2">
                <input type="radio" id="promotion" name="calculate_type" class="custom-control-input" value="{{ $commission::CALCULATE_TYPE_BASIC_RATE }}" {{ $promotionChecked }}>
                <label class="custom-control-label" style="font-size: 0.65rem;" for="promotion">Promotion: </label>
                <div class="d-inline ml-2">
                    {{-- <p class="d-inline _fz-1">Promotion Threshold:</p> --}}
                    <label class="d-inline form-control-label _fz-1" for="promotion_threshold">Promotion Threshold</label>
                    <input class="form-control w-25 d-inline ml-2" name="promotion_threshold" id="promotion_threshold"
                        type="text" value="{{ optional($customer->commission)->promotion_threshold }}">
                    <label class="d-inline form-control-label _fz-1" for="tier_promotion">Tier Promotion</label>
                    <input class="form-control w-25 d-inline ml-2" name="tier_promotion" id="tier_promotion"
                        type="text" value="{{ optional($customer->commission)->tier_promotion }}">
                </div>
                
            </div>
            <div class="custom-control custom-radio mb-2">
                <input type="radio" id="basic_rate" name="calculate_type" class="custom-control-input" value="{{ $commission::CALCULATE_TYPE_PROMOTION }}" {{ $basicChecked }}>
                <label class="custom-control-label" style="font-size: 0.65rem;" for="basic_rate">Basic Rate</label>
                <input class="form-control w-25 d-inline ml-2" name="basic_rate" 
                    id="" type="text" value="{{ optional($customer->commission)->basic_rate }}">
            </div>
            <div class="custom-control custom-radio mb-2">
                <input type="radio" id="tier" name="calculate_type" class="custom-control-input" value="{{ $commission::CALCULATE_TYPE_TIER }}" {{ $tierChecked }}>
                <label class="custom-control-label" style="font-size: 0.65rem;" for="tier">Tier</label>
            </div>
            <hr class="my-2 w-100">
            <table class="_table table-hover table-sm">
                <thead>
                    <tr>
                        <th>Tier</th>
                        <th class="text-center">Amount Threshold</th>
                        <th class="text-center">Commission Amount</th>
                        <th class="text-center">Commission Rate(Percent of Sale)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>1</th>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_1_threshold' 
                                value='{{ optional($customer->commission)->tier_1_threshold }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_1_amount' 
                                value='{{ optional($customer->commission)->tier_1_amount }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_1_rate' 
                                value='{{ optional($customer->commission)->tier_1_rate }}'>
                        </td>
                    </tr>
                    <tr>
                        <th>2</th>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_2_threshold' 
                                value='{{ optional($customer->commission)->tier_2_threshold }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_2_amount' 
                                value='{{ optional($customer->commission)->tier_2_amount }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_2_rate' 
                                value='{{ optional($customer->commission)->tier_2_rate }}'>
                        </td>
                    </tr>
                    <tr>
                        <th>3</th>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_3_threshold' 
                                value='{{ optional($customer->commission)->tier_3_threshold }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_3_amount' 
                                value='{{ optional($customer->commission)->tier_3_amount }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_3_rate' 
                                value='{{ optional($customer->commission)->tier_3_rate }}'>
                        </td>
                    </tr>
                    <tr>
                        <th>4</th>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_4_threshold' 
                                value='{{ optional($customer->commission)->tier_4_threshold }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_4_amount' 
                                value='{{ optional($customer->commission)->tier_4_amount }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_4_rate' 
                                value='{{ optional($customer->commission)->tier_4_rate }}'>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="text-center">Maximum Amount</th>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_top_amount' 
                                value='{{ optional($customer->commission)->tier_top_amount }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_top_rate' 
                                value='{{ optional($customer->commission)->tier_top_rate }}'>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Button --}}
        <div class="d-flex justify-content-center my-2">
            <button class="btn btn-primary _fz-1 mr-2" type="submit">Save</button>
            <button class="btn btn-danger _fz-1" type="button" id="cancelBtn">Cancel</button>
        </div>
    </form>

    <!-- Modal -->
    <div class="modal fade" id="salesRepModal" tabindex="-1" role="dialog" aria-labelledby="salesRepModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesRepModalLabel">Sales Rep Setup</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <select class="_select-sales_rep" multiple="multiple" name="sales_rep">
                                @foreach ($selectedSalesReps as $id => $user_name)
                                    <option value="{{ $id }}" selected>{{ $user_name }}</option>
                                @endforeach
                                @foreach ($unSelectedSalesReps as $id => $user_name)
                                    <option value="{{ $id }}">{{ $user_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('argon/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('js/plugins/bootstrap-duallistbox/jquery.bootstrap-duallistbox.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#contract_date').datepicker({
            format: 'yyyy-mm-dd',//日期時間格式
            ignoreReadonly: false,  //禁止使用者輸入 啟用唯讀
            autoclose: true
        });

        $('._btn-sales-rep').click(function() {
            $('#salesRepModal').modal('show')
        }) ;

        var dualListbox = $("select[name='sales_rep']").bootstrapDualListbox({
            nonSelectedListLabel: 'Available',
            selectedListLabel: 'Selected',
        });

        // 箭頭 button 隱藏
        var dualListContainer = $("select[name='sales_rep']").bootstrapDualListbox('getContainer');
        dualListContainer.find('.btn-group').css('display', 'none');


        function showSalesReps() {
            let sales_reps = [];
            $("select[name='sales_rep'] option:selected").each(function() {
                let $this = $(this);
                if ($this.length) {
                    sales_reps.push($this.text());
                }
            });

            $('._p-sales-reps').html(sales_reps.join('、'));
            $("input[name='sales_reps']").val(dualListbox.val().join('|'))
        }

        // sales_rep_helper1 名稱為 dualListbox 套件自行命名的規則: 主 select 的 name 加上suffix「_helper1」
        $("select[name='sales_rep_helper1']").change(function(e) {
            showSalesReps();
        });

        $("select[name='sales_rep_helper2']").change(function(e) {
            showSalesReps();
        });
        
    });

    
</script>