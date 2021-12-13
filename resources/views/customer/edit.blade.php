
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
                        <label class="form-control-label _fz-1" for="client_contact">Client Contact</label>
                        <input class="form-control _fz-1" name="client_contact" id="client_contact" 
                            placeholder="client_contact" type="text" value="{{ $customer->contact_person }}">
                    </div>
                    <div class="col-6">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label class="form-control-label _fz-1" for="company_contact">Company Contact</label>
                        <input class="form-control _fz-1" name="company_contact" id="company_contact" 
                            placeholder="company_contact" type="text" value="{{ $customer->company_name }}">
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
                    <label class="form-control-label _fz-1" for="sales_region">Sales Region</label>
                    <select class="form-control _fz-1" data-toggle="select" name="sales_region" id="sales_region">
                        <option value="hk" @if($customer->sales_region === 'hk') {{ 'selected' }} @endif>
                            HK
                        </option>
                        <option value="tw" @if($customer->sales_region === 'tw') {{ 'selected' }} @endif>
                            TW
                        </option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="form-control-label _fz-1" for="contract_date">Contract Date</label>
                    <input class="form-control _fz-1" name="contract_date" id="contract_date" 
                        placeholder="Contract Date" type="text" value="{{ optional($customer->contract_date)->format('Y-m-d') }}">
                </div>
                <div class="form-group mb-2">
                    {{-- TODO: 畫面需要再調整 --}}
                    <label class="form-control-label _fz-1" for="status">Status</label>
                    <label class="custom-toggle d-block">
                        <input type="checkbox" name="active" id='status'
                            @if ($customer->active) checked @endif>
                        <span class="custom-toggle-slider rounded-circle" data-label-off="InActive" data-label-on="Active"></span>
                    </label>
                </div>
                <div class="form-group mb-2">
                    <label class="form-control-label _fz-1" for="">Sales Rep</label>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#salesRepModal">
                        Setting
                    </button>
                </div>
            </div>
            {{-- ./ Advanced Setting --}}
        </div>

        {{-- Commission Structure --}}
        <div class="d-flex flex-column mt-4">
            <h3>Commission Structure</h3>
            <hr class="my-2 w-100">
            <h3>Calculate Type</h3>
            <hr class="my-2 w-100">
            <div class="custom-control custom-checkbox mb-2">
                <input class="custom-control-input" id="sku" type="checkbox" name='sku' 
                    @if (optional($customer->commission)->isSku()) checked @endif>
                <label class="custom-control-label" style="font-size: 0.65rem;" for="sku">SKU</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
                <input class="custom-control-input" id="basic_rate" type="checkbox">
                {{-- TODO: 為什麼要 checkbox --}}
                <label class="custom-control-label" style="font-size: 0.65rem;" for="basic_rate">Basic Rate</label>
                <input class="form-control w-25 d-inline ml-2" name="basic_rate" 
                    id="" type="text" value="{{ optional($customer->commission)->basic_rate }}">
            </div>
            <div class="custom-control custom-checkbox mb-2">
                <input class="custom-control-input" id="tier_structure" type="checkbox" name="tier"
                    @if (optional($customer->commission)->isTier()) checked @endif>
                <label class="custom-control-label" style="font-size: 0.65rem;" for="tier_structure">Tier Structure</label>
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
            <button class="btn btn-primary _fz-1 mr-2" type="submit" id="save">Save</button>
            <button class="btn btn-danger _fz-1" type="button" id="cancel">Cancel</button>
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
                    <form method="POST" action="" id="service_type_country" role="form" class="form create_white_form">
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
                    </form>
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

        var dualListContainer = $("select[name='sales_rep']").bootstrapDualListbox('getContainer');
        dualListContainer.find('.btn-group').css('display', 'none');

        // alert(dualListbox.val());

        // sales_rep_helper1 名稱為套件自行命名的規則: 主select的name加上suffix「_helper1」
        $("select[name='sales_rep_helper1']").change(function(e) {
            $("select[name='sales_rep'] option:selected").each(function() {
                var $this = $(this);
                if ($this.length) {
                    var selText = $this.text();
                    alert(selText);
                }
            });
        });
        $("select[name='sales_rep_helper2']").change(function(e) {
            $("select[name='sales_rep'] option:selected").each(function() {
                var $this = $(this);
                if ($this.length) {
                    var selText = $this.text();
                    alert(selText);
                }
            });
        });

        
    });

    
</script>