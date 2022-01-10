
<!-- colorbox html part start -->
<div class="container">
    <form id='skuCommissionUploadForm' method="POST">
        @csrf
        <div class="row">
            <h3>Upload SKU Commission</h3>
            <hr class="my-1 w-100">

            <div class="col-12 form-group mb-2 mt-2">
                <label class="form-control-label _fz-1 d-inline mr-4" for="client_code">
                    Client Code <span class="text-red">*<span>
                </label>
                <input class="form-control _fz-1 d-inline w-50" name="client_code" id="client_code" 
                    type="text" value="">
            </div>

            <div class="col-12 form-group mb-2">
                <label class="form-control-label _fz-1 d-inline mr-4" for="file">
                    File Input <span class="text-red">*<span>
                </label>
                <input class="form-control d-inline w-50" type="file" name="file" id="file">
            </div>
        </div>

        {{-- Button --}}
        <div class="d-flex justify-content-center mt-4">
            <button class="btn btn-primary _fz-1 mr-2" type="submit">Create</button>
            <button class="btn btn-danger _fz-1" type="button" id="cancelBtn">Close</button>
        </div>
    </form>

</div>

<script type="text/javascript">
    $(document).ready(function() {
    });

    
</script>