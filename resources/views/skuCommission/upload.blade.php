<style>
    .container h3 {
        font-size: 16px;
        margin-bottom: 10px;
    }
    .container  label,input,button {
        font-size: 12px !important;
    }
    .form-control {
        padding: 10px;
        height: 45px;
        border-radius: 8px;
    }
    .btn {
        padding: 10px 20px;
    }
</style>
<!-- colorbox html part start -->
<div class="container">
    <form id='skuCommissionUploadForm' method="POST">
        @csrf
        <div>
            <h3>Upload SKU Commission</h3>
            <hr style="margin: 5px;" class="w-100">

            <div style="margin-bottom: 10px;">
                <label class="form-control-label d-inline" style="margin-right: 10px;" for="client_code">
                    Client Code <span class="text-red">*<span>
                </label>
                <input class="form-control d-inline w-50" name="client_code" id="client_code" 
                    type="text" value="">
            </div>

            <div style="margin-bottom: 10px;">
                <label class="form-control-label d-inline" style="margin-right: 10px;" for="file">
                    File Input <span class="text-red">*<span>
                </label>
                <input class="form-control d-inline w-50" type="file" name="file" id="file">
            </div>
        </div>

        {{-- Button --}}
        <div class="text-center" style="margin-top: 10px;">
            <button class="btn btn-primary" style="margin-right: 10px;" type="submit">Create</button>
            <button class="btn btn-danger" type="button" id="cancelBtn">Close</button>
        </div>
    </form>

</div>