
<link href="{{ asset('css/plugins/bootstrap-duallistbox/bootstrap-duallistbox.css') }}" rel="stylesheet">
<style>
    .table-sm td, .table-sm th {
        padding: .1rem .5rem;
    }
</style>

<!-- colorbox html part start -->
<div class="container">
    <form id='customerUpdateForm' method="POST">
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
                            Client Code <span class="text-red">*</span>
                        </label>
                        <input class="form-control _fz-1" name="client_code" id="client_code" 
                            type="text" value="{{ $customer->client_code }}" disabled>
                    </div>
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="company_name">
                            Company Name
                        </label>
                        <input class="form-control _fz-1" name="company_name" id="company_name" 
                            type="text" value="{{ $customer->company_name }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="company_contact">Company Contact</label>
                        <input class="form-control _fz-1" name="company_contact" id="company_contact" 
                            type="text" value="{{ $customer->contact_person }}">
                    </div>
                    <div class="col-6">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="street1">Street1</label>
                        <input class="form-control _fz-1" name="street1" id="street1" 
                            type="text" value="{{ $customer->address1 }}">
                    </div>
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="street2">Street2</label>
                        <input class="form-control _fz-1" name="street2" id="street2" 
                            type="text" value="{{ $customer->address2 }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="city">City</label>
                        <input class="form-control _fz-1" name="city" id="city" 
                            type="text" value="{{ $customer->city }}">
                    </div>
                    <div class="col-3 form-group mb-2">
                        <label class="form-control-label _fz-1" for="district">District</label>
                        <input class="form-control _fz-1" name="district" id="district" 
                            type="text" value="{{ $customer->district }}">
                    </div>
                    <div class="col-3 form-group mb-2">
                        <label class="form-control-label _fz-1" for="zip">Zip</label>
                        <input class="form-control _fz-1" name="zip" id="zip" 
                            type="text" value="{{ $customer->zip }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="country">Country</label>
                        <input class="form-control _fz-1" name="country" id="country" 
                            type="text" value="{{ $customer->country }}">
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
                        Sales Region <span class="text-red">*</span>
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
                        Contract Date <span class="text-red">*</span>
                    </label>
                    <input class="form-control _fz-1" name="contract_date" id="contract_date" 
                        type="text" value="{{ optional($customer->contract_date)->format('Y-m-d') }}">
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
                    <label class="form-control-label _fz-1" for="">Staff Members</label>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#staffMemberModal">
                        Setting
                    </button>
                    
                    <input type="hidden" name="staff_members" value="{{ $selectedUsers->pluck('id')->implode('|') }}">
                    <p class="_fz-1 mt-1 _p-sales-reps">{{ $selectedUsers->pluck('user_name')->implode('、') }}</p>
                </div>
            </div>
            {{-- ./ Advanced Setting --}}
        </div>

        {{-- Commission Structure --}}
        <div class="d-flex flex-column mt-4">
            <h3>Sales Commission Calculator</h3>
            <hr class="my-2 w-100">
            <h3>(Standard) Calculation Type <span class="text-red">*</span></h3>
            <hr class="my-2 w-100">
            @inject('commission', 'App\Constants\CommissionConstant')
            @php
                $skuChecked = (optional($customer->commission)->calculation_type === $commission::CALCULATION_TYPE_SKU) ? 'checked' : '';
                $tierChecked = (optional($customer->commission)->calculation_type === $commission::CALCULATION_TYPE_TIER) ? 'checked' : '';
                $basicChecked = (optional($customer->commission)->calculation_type === $commission::CALCULATION_TYPE_BASIC_RATE) ? 'checked' : '';
            @endphp
            <div class="custom-control custom-radio mb-2">
                <input type="radio" id="is_sku" name="calculation_type" class="custom-control-input" value="{{ $commission::CALCULATION_TYPE_SKU }}" {{ $skuChecked }}>
                <label class="custom-control-label" style="font-size: 0.65rem;" for="is_sku">SKU</label>
            </div>
            <div class="custom-control custom-radio mb-2">
                <input type="radio" id="basic_rate" name="calculation_type" class="custom-control-input" value="{{ $commission::CALCULATION_TYPE_BASIC_RATE }}" {{ $basicChecked }}>
                <label class="custom-control-label" style="font-size: 0.65rem;" for="basic_rate">Basic Rate</label>
                <input class="form-control w-25 d-inline ml-2 _input_limit_integer" name="basic_rate" 
                    type="text" value="{{ optional($customer->commission)->basic_rate_percentage }}"> %
            </div>
            <div class="custom-control custom-radio mb-2">
                <input type="radio" id="tier" name="calculation_type" class="custom-control-input" value="{{ $commission::CALCULATION_TYPE_TIER }}" {{ $tierChecked }}>
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
                        <th>1<span class="text-red">*</span></th>
                        <td>
                            <input class="form-control _fz-1 _input_limit_integer" type="text" name='tier_1_threshold' 
                                value='{{ optional($customer->commission)->tier_1_threshold }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_1_amount' 
                                value='{{ optional($customer->commission)->tier_1_amount }}'>
                        </td>
                        <td>
                            <input class="form-control d-inline w-75 _fz-1 _input_limit_integer" type="text" name='tier_1_rate' 
                                value='{{ optional($customer->commission)->tier_1_rate_percentage }}'>
                            <span>%</span>
                        </td>
                    </tr>
                    <tr>
                        <th>2</th>
                        <td>
                            <input class="form-control _fz-1 _input_limit_integer" type="text" name='tier_2_threshold' 
                                value='{{ optional($customer->commission)->tier_2_threshold }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_2_amount' 
                                value='{{ optional($customer->commission)->tier_2_amount }}'>
                        </td>
                        <td>
                            <input class="form-control d-inline w-75 _fz-1 _input_limit_integer" type="text" name='tier_2_rate' 
                                value='{{ optional($customer->commission)->tier_2_rate_percentage }}'>
                            <span>%</span>
                        </td>
                    </tr>
                    <tr>
                        <th>3</th>
                        <td>
                            <input class="form-control _fz-1 _input_limit_integer" type="text" name='tier_3_threshold' 
                                value='{{ optional($customer->commission)->tier_3_threshold }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_3_amount' 
                                value='{{ optional($customer->commission)->tier_3_amount }}'>
                        </td>
                        <td>
                            <input class="form-control d-inline w-75 _fz-1 _input_limit_integer" type="text" name='tier_3_rate' 
                                value='{{ optional($customer->commission)->tier_3_rate_percentage }}'>
                            <span>%</span>
                        </td>
                    </tr>
                    <tr>
                        <th>4</th>
                        <td>
                            <input class="form-control _fz-1 _input_limit_integer" type="text" name='tier_4_threshold' 
                                value='{{ optional($customer->commission)->tier_4_threshold }}'>
                        </td>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_4_amount' 
                                value='{{ optional($customer->commission)->tier_4_amount }}'>
                        </td>
                        <td>
                            <input class="form-control d-inline w-75 _fz-1 _input_limit_integer" type="text" name='tier_4_rate' 
                                value='{{ optional($customer->commission)->tier_4_rate_percentage }}'>
                            <span>%</span>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="text-center">Maximum Amount<span class="text-red">*</span></th>
                        <td>
                            <input class="form-control _fz-1" type="text" name='tier_top_amount' 
                                value='{{ optional($customer->commission)->tier_top_amount }}'>
                        </td>
                        <td>
                            <input class="form-control d-inline w-75 _fz-1 _input_limit_integer" type="text" name='tier_top_rate' 
                                value='{{ optional($customer->commission)->tier_top_rate_percentage }}'>
                            <span>%</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr class="my-2 w-100">
            <h3>(Optional) Promo Commission <span class="text-red">*</span></h3>
            <hr class="my-2 w-100">
            <div class="form-group mb-2">
                <label class="form-control-label _fz-1" for="percentage_off_promotion">Percentage Off Promotion</label>
                <input class="form-control _fz-1 d-inline w-25 _input_limit_integer" name="percentage_off_promotion" id="percentage_off_promotion" 
                    type="text" value="{{ optional($customer->commission)->percentage_off_promotion }}">
                <span>%</span>
            </div>
            <div class="form-group mb-2">
                <label class="form-control-label _fz-1" for="tier_promotion">Promo Commission Rate</label>
                <input class="form-control _fz-1 d-inline w-25 _input_limit_integer" name="tier_promotion" id="tier_promotion" 
                    type="text" value="{{ optional($customer->commission)->tier_promotion_percentage }}">
                <span>%</span>
            </div>
        </div>

        {{-- Button --}}
        <div class="d-flex justify-content-center my-2">
            <button class="btn btn-primary _fz-1 mr-2" type="submit">Save</button>
            <button class="btn btn-danger _fz-1" type="button" id="cancelBtn">Cancel</button>
        </div>
    </form>

    <!-- Modal -->
    <div class="modal fade" id="staffMemberModal" tabindex="-1" role="dialog" aria-labelledby="staffMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staffMemberModalLabel">Sales Rep Setup</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <select class="_select-sales_rep" multiple="multiple" name="sales_rep">
                                @foreach ($selectedUsers as $user)
                                    <option value="{{ $user['id'] }}" selected>{{ $user['user_name'] }} ({{ $user['role_desc'] }})</option>
                                @endforeach
                                @foreach ($unSelectedUsers as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['user_name'] }} ({{ $user['role_desc'] }})</option>
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

        // ******************
        // Staff Members 處理
        // ******************
        $('._btn-sales-rep').click(function() {
            $('#staffMemberModal').modal('show')
        }) ;

        var dualListbox = $("select[name='sales_rep']").bootstrapDualListbox({
            nonSelectedListLabel: 'Available',
            selectedListLabel: 'Selected',
        });

        // 箭頭 button 隱藏
        var dualListContainer = $("select[name='sales_rep']").bootstrapDualListbox('getContainer');
        dualListContainer.find('.btn-group').css('display', 'none');


        function showSalesReps() {
            let staff_members = [];
            $("select[name='sales_rep'] option:selected").each(function() {
                let $this = $(this);
                if ($this.length) {
                    staff_members.push($this.text());
                }
            });

            $('._p-sales-reps').html(staff_members.join('、'));
            $("input[name='staff_members']").val(dualListbox.val().join('|'))
        }

        // sales_rep_helper1 名稱為 dualListbox 套件自行命名的規則: 主 select 的 name 加上suffix「_helper1」
        $("select[name='sales_rep_helper1']").change(function(e) {
            showSalesReps();
        });

        $("select[name='sales_rep_helper2']").change(function(e) {
            showSalesReps();
        });


        // ****************************
        // 限制 input 只能輸入小數點後兩位
        // ****************************
        function setTwoDecimal(num) {
            if(num.indexOf(".") !== 0){
                num = num.replace(/[^\d.]/g, "");  // 清除'數字'和 '.' 以外的字元  
                num = num.replace(/\.{2,}/g, "."); // 只保留第一個 '.' 清除多餘的  
                num = num.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
                num = num.replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3'); // 只能輸入兩個小數  
                if (num.indexOf(".") < 0 && num != "") { // 以上已經過濾，此處控制的是如果沒有小數點，首位不能為類似於 01、02的金額 
                    num = parseFloat(num);
                }  
            }else{
                num = "";
            }

            return num;
        }

        $('input[name=basic_rate]').on('input', function () {
            $(this).val(
                setTwoDecimal($(this).val())
            );
        });

        $('input[name=tier_1_amount]').on('input', function () {
            $(this).val(
                setTwoDecimal($(this).val())
            );
        });

        $('input[name=tier_2_amount]').on('input', function () {
            $(this).val(
                setTwoDecimal($(this).val())
            );
        });

        $('input[name=tier_3_amount]').on('input', function () {
            $(this).val(
                setTwoDecimal($(this).val())
            );
        });

        $('input[name=tier_4_amount]').on('input', function () {
            $(this).val(
                setTwoDecimal($(this).val())
            );
        });

        $('input[name=tier_top_amount]').on('input', function () {
            $(this).val(
                setTwoDecimal($(this).val())
            );
        });

        // ****************************
        // 限制 input 只能輸入整數
        // ****************************
        $('._input_limit_integer').on('input', function () {
            this.value = Number(this.value.replace(/[^0-9]/g, ''));
            if (this.value == 0) {
                this.value = '';
            }
        });
        
        
    });

    
</script>