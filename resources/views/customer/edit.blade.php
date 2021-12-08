
<!-- colorbox html part start -->
<div class="container">
    
    <div class="row">
        {{-- Basic Information --}}
        <div class="col-md-7">
            <h3>Basic Information</h3>
            <hr class="my-2">
            <div class="row">
                <div class="col-6 form-group mb-2">
                    <label class="form-control-label _fz-1" for="client_contact">Client Contact</label>
                    <input class="form-control _fz-1" name="client_contact" id="client_contact" 
                        placeholder="client_contact" type="text" value="">
                </div>
                <div class="col-6">
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group mb-2">
                    <label class="form-control-label _fz-1" for="company_contact">Company Contact</label>
                    <input class="form-control _fz-1" name="company_contact" id="company_contact" 
                        placeholder="company_contact" type="text" value="">
                </div>
                <div class="col-6">
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group mb-2">
                    <label class="form-control-label _fz-1" for="street1">Street1</label>
                    <input class="form-control _fz-1" name="street1" id="street1" 
                    placeholder="street1" type="text" value="">
                </div>
                <div class="col-6 form-group mb-2">
                    <label class="form-control-label _fz-1" for="street2">Street2</label>
                    <input class="form-control _fz-1" name="street2" id="street2" 
                    placeholder="street2" type="text" value="">
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group mb-2">
                    <label class="form-control-label _fz-1" for="city">City</label>
                    <input class="form-control _fz-1" name="city" id="city" 
                        placeholder="city" type="text" value="">
                </div>
                <div class="col-3 form-group mb-2">
                    <label class="form-control-label _fz-1" for="district">District</label>
                    <input class="form-control _fz-1" name="district" id="district" 
                        placeholder="district" type="text" value="">
                </div>
                <div class="col-3 form-group mb-2">
                    <label class="form-control-label _fz-1" for="zip">Zip</label>
                    <input class="form-control _fz-1" name="zip" id="zip" 
                        placeholder="zip" type="text" value="">
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group mb-2">
                    <label class="form-control-label _fz-1" for="country">Country</label>
                    <input class="form-control _fz-1" name="country" id="country" 
                        placeholder="country" type="text" value="">
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
                    {{-- <option value="hk" @if($sales_region == 'hk') {{ 'selected' }} @endif> --}}
                    <option value="hk">
                        HK
                    </option>
                    {{-- <option value="tw" @if($sales_region == 'tw') {{ 'selected' }} @endif> --}}
                    <option value="tw">
                        TW
                    </option>
                </select>
            </div>
            <div class="form-group mb-2">
                <label class="form-control-label _fz-1" for="contract_date">Contract Date</label>
                <input class="form-control _fz-1" name="contract_date" id="contract_date" 
                    placeholder="Contract Date" type="text" value="">
            </div>
            <div class="form-group mb-2">
                <label class="form-control-label _fz-1" for="status">Status</label>
                <input class="form-control _fz-1" name="status" id="status" 
                    placeholder="status" type="text" value="">
            </div>
            <div class="form-group mb-2">
                <label class="form-control-label _fz-1" for="">Sales Rep</label>
                <button class="_btn-sales-rep btn m-l-xs" type="button">modal</button>
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
            <input class="custom-control-input" id="sku" type="checkbox">
            <label class="custom-control-label" style="font-size: 0.65rem;" for="sku">SKU</label>
        </div>
        <div class="custom-control custom-checkbox mb-2">
            <input class="custom-control-input" id="basic_rate" type="checkbox">
            <label class="custom-control-label" style="font-size: 0.65rem;" for="basic_rate">Basic Rate</label>
            <input class="form-control w-25 d-inline ml-2" name="basic_rate" id="" type="text" value="">
        </div>
        <div class="custom-control custom-checkbox mb-2">
            <input class="custom-control-input" id="tier_structure" type="checkbox">
            <label class="custom-control-label" style="font-size: 0.65rem;" for="tier_structure">Tier Structure</label>
        </div>
        <hr class="my-2 w-100">
        <table class="_table table-hover table-sm">
            <thead>
                <tr>
                    <th>Tier</th>
                    <th>Amount Threshold</th>
                    <th>Commission Amount</th>
                    <th>Commission Rate(Percent of Sale)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <th>Maximum Amount</th>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>


    


    <!-- modal test -->
    <div class="modal fade" id="SetupCountry" tabindex="-1" role="dialog" aria-labelledby="SetupCountryLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" style="width:750px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                    </button>
                    <span class="sr-only">Close</span>
                    <h3 class="modal-tittle" id="SetupCountryLabel">Service Country Setup</h3>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form method="POST" action="" id="service_type_country" role="form" class="form create_white_form">
                                <div class="row">
                                    <div class="form-group col-lg-12">
                                        <select class="dual-listbox-body" multiple="multiple" name="countries[]">
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-primary"> Save</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#contract_date').datepicker({
            format: 'yyyy-mm-dd',//日期時間格式
            ignoreReadonly: false,  //禁止使用者輸入 啟用唯讀
            autoclose: true
        });

        $('._btn-sales-rep').click(function() {
            $('#SetupCountry').modal('show')
        }) ;

    });

    
</script>