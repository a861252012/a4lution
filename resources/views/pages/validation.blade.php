@extends('layouts.app', [
    'parentSection' => 'forms',
    'elementName' => 'validations'
])

@section('content') 
    @component('layouts.headers.auth') 
        @component('layouts.headers.breadcrumbs')
            @slot('title') 
                {{ __('Validations') }} 
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'validation') }}">{{ __('Forms') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Validations') }}</li>
        @endcomponent 
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col">
                <div class="card-wrapper">
                    <!-- Custom form validation -->
                    <div class="card">
                        <!-- Card header -->
                        <div class="card-header">
                            <h3 class="mb-0">Custom styles</h3>
                        </div>
                        <!-- Card body -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <p class="mb-0">
                                        For custom form validation messages, you’ll need to add the novalidate boolean attribute to your <code>&lt;form&gt;</code>.
                                        This disables the browser default feedback tooltips, but still provides access to the
                                        form validation APIs in JavaScript.
                                        <br /><br /> When attempting to submit, you’ll see the<code>:invalid</code> and <code>:valid</code>                                    styles applied to your form controls.
                                    </p>
                                </div>
                            </div>
                            <hr />
                            <form class="needs-validation" novalidate>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-control-label" for="validationCustom01">First name</label>
                                        <input type="text" class="form-control" id="validationCustom01" placeholder="First name" value="Mark" required>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-control-label" for="validationCustom02">Last name</label>
                                        <input type="text" class="form-control" id="validationCustom02" placeholder="Last name" value="Otto" required>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-control-label" for="validationCustomUsername">Username</label>
                                        <input type="text" class="form-control" id="validationCustomUsername" placeholder="Username" aria-describedby="inputGroupPrepend"
                                            required>
                                        <div class="invalid-feedback">
                                            Please choose a username.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-control-label" for="validationCustom03">City</label>
                                        <input type="text" class="form-control" id="validationCustom03" placeholder="City" required>
                                        <div class="invalid-feedback">
                                            Please provide a valid city.
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-control-label" for="validationCustom04">State</label>
                                        <input type="text" class="form-control" id="validationCustom04" placeholder="State" required>
                                        <div class="invalid-feedback">
                                            Please provide a valid state.
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-control-label" for="validationCustom05">Zip</label>
                                        <input type="text" class="form-control" id="validationCustom05" placeholder="Zip" required>
                                        <div class="invalid-feedback">
                                            Please provide a valid zip.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input class="custom-control-input" id="invalidCheck" type="checkbox" value="" required>
                                        <label class="custom-control-label" for="invalidCheck">Agree to terms and conditions</label>
                                        <div class="invalid-feedback">
                                            You must agree before submitting.
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary" type="submit">Submit form</button>
                            </form>
                        </div>
                    </div>
                    <!-- Default browser form validation -->
                    <div class="card">
                        <!-- Card header -->
                        <div class="card-header">
                            <h3 class="mb-0">Browser defaults</h3>
                        </div>
                        <!-- Card body -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <p class="mb-0">
                                        Not interested in custom validation feedback messages or writing JavaScript to change form behaviors? All good, you can use
                                        the browser defaults. Try submitting the form below. Depending on your browser and OS,
                                        you’ll see a slightly different style of feedback.
                                        <br /><br /> While these feedback styles cannot be styled with CSS, you can still customize
                                        the feedback text through JavaScript.
                                    </p>
                                </div>
                            </div>
                            <hr />
                            <form>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="validationDefault01">First name</label>
                                            <input type="text" class="form-control" id="validationDefault01" placeholder="First name" value="Mark" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="validationDefault02">Last name</label>
                                            <input type="text" class="form-control" id="validationDefault02" placeholder="Last name" value="Otto" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="validationDefaultUsername">Username</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend2">@</span>
                                                </div>
                                                <input type="text" class="form-control" id="validationDefaultUsername" placeholder="Username" aria-describedby="inputGroupPrepend2"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="validationDefault03">City</label>
                                            <input type="text" class="form-control" id="validationDefault03" placeholder="City" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="validationDefault04">State</label>
                                            <input type="text" class="form-control" id="validationDefault04" placeholder="State" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="validationDefault05">Zip</label>
                                            <input type="text" class="form-control" id="validationDefault05" placeholder="Zip" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input class="custom-control-input" id="invalidCheck2" type="checkbox" value="" required>
                                        <label class="custom-control-label" for="invalidCheck2">Agree to terms and conditions</label>
                                        <div class="invalid-feedback">
                                            You must agree before submitting.
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary" type="submit">Submit form</button>
                            </form>
                        </div>
                    </div>
                    <!-- Default browser form validation -->
                    <div class="card">
                        <!-- Card header -->
                        <div class="card-header">
                            <h3 class="mb-0">Server side</h3>
                        </div>
                        <!-- Card body -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <p class="mb-0">
                                        We recommend using client side validation, but in case you require server side, you can indicate invalid and valid form fields
                                        with <code>.is-invalid</code> and <code>.is-valid</code>.
                                        Note that <code>.invalid-feedback</code> is also supported with these classes.
                                    </p>
                                </div>
                            </div>
                            <hr />
                            <form>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group has-success">
                                            <label class="form-control-label" for="validationServer01">First name</label>
                                            <input type="text" class="form-control is-valid" id="validationServer01" placeholder="First name" value="Mark" required>
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group has-success">
                                            <label class="form-control-label" for="validationServer02">Last name</label>
                                            <input type="text" class="form-control is-valid" id="validationServer02" placeholder="Last name" value="Otto" required>
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group has-danger">
                                            <label class="form-control-label" for="validationServerUsername">Username</label>
                                            <input type="text" class="form-control is-invalid" id="validationServerUsername" placeholder="Username" aria-describedby="inputGroupPrepend3"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group has-danger">
                                            <label class="form-control-label" for="validationServer03">City</label>
                                            <input type="text" class="form-control is-invalid" id="validationServer03" placeholder="City" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid city.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group has-danger">
                                            <label class="form-control-label" for="validationServer04">State</label>
                                            <input type="text" class="form-control is-invalid" id="validationServer04" placeholder="State" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid state.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group has-danger">
                                            <label class="form-control-label" for="validationServer05">Zip</label>
                                            <input type="text" class="form-control is-invalid" id="validationServer05" placeholder="Zip" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid zip.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group has-danger">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input class="custom-control-input is-invalid" id="invalidCheck3" type="checkbox" value="" required>
                                        <label class="custom-control-label" for="invalidCheck3">Agree to terms and conditions</label>
                                        <div class="invalid-feedback">
                                            You must agree before submitting.
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary" type="submit">Submit form</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('layouts.footers.auth')
    </div>
@endsection