@extends('layouts.app', [
    'parentSection' => 'forms',
    'elementName' => 'elements'
])

@section('content') 
    @component('layouts.headers.auth') 
        @component('layouts.headers.breadcrumbs')
            @slot('title') 
                {{ __('Elements') }} 
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'elements') }}">{{ __('Forms') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Elements') }}</li>
        @endcomponent 
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="card mb-4">
            <!-- Card header -->
            <div class="card-header">
                <h3 class="mb-0">Form group in grid</h3>
            </div>
            <!-- Card body -->
            <div class="card-body">
                <!-- Form groups used in grid -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label" for="example3cols1Input">One of three cols</label>
                            <input type="text" class="form-control" id="example3cols1Input" placeholder="One of three cols">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label" for="example3cols2Input">One of three cols</label>
                            <input type="text" class="form-control" id="example3cols2Input" placeholder="One of three cols">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label" for="example3cols3Input">One of three cols</label>
                            <input type="text" class="form-control" id="example3cols3Input" placeholder="One of three cols">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label class="form-control-label" for="example4cols1Input">One of four cols</label>
                            <input type="text" class="form-control" id="example4cols1Input" placeholder="One of four cols">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label class="form-control-label" for="example4cols2Input">One of four cols</label>
                            <input type="text" class="form-control" id="example4cols2Input" placeholder="One of four cols">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label class="form-control-label" for="example4cols3Input">One of four cols</label>
                            <input type="text" class="form-control" id="example4cols3Input" placeholder="One of four cols">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label class="form-control-label" for="example4cols3Input">One of four cols</label>
                            <input type="text" class="form-control" id="example4cols3Input" placeholder="One of four cols">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label" for="example2cols1Input">One of two cols</label>
                            <input type="text" class="form-control" id="example2cols1Input" placeholder="One of two cols">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label" for="example2cols2Input">One of two cols</label>
                            <input type="text" class="form-control" id="example2cols2Input" placeholder="One of two cols">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card-wrapper">
                    <!-- Form controls -->
                    <div class="card">
                        <!-- Card header -->
                        <div class="card-header">
                            <h3 class="mb-0">Form controls</h3>
                        </div>
                        <!-- Card body -->
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label class="form-control-label" for="exampleFormControlInput1">Email address</label>
                                    <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label" for="exampleFormControlSelect1">Example select</label>
                                    <select class="form-control" id="exampleFormControlSelect1">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label" for="exampleFormControlSelect2">Example multiple select</label>
                                    <select multiple class="form-control" id="exampleFormControlSelect2">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label" for="exampleFormControlTextarea1">Example textarea</label>
                                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- HTML5 inputs -->
                    <div class="card">
                        <!-- Card header -->
                        <div class="card-header">
                            <h3 class="mb-0">HTML5 inputs</h3>
                        </div>
                        <!-- Card body -->
                        <div class="card-body">
                            <form>
                                <div class="form-group row">
                                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Text</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" value="John Snow" id="example-text-input">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-search-input" class="col-md-2 col-form-label form-control-label">Search</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="search" value="Tell me your secret ..." id="example-search-input">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-email-input" class="col-md-2 col-form-label form-control-label">Email</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="email" value="argon@example.com" id="example-email-input">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-url-input" class="col-md-2 col-form-label form-control-label">URL</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="url" value="https://www.creative-tim.com" id="example-url-input">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-tel-input" class="col-md-2 col-form-label form-control-label">Phone</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="tel" value="40-(770)-888-444" id="example-tel-input">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-password-input" class="col-md-2 col-form-label form-control-label">Password</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="password" value="password" id="example-password-input">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-number-input" class="col-md-2 col-form-label form-control-label">Number</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="number" value="23" id="example-number-input">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-datetime-local-input" class="col-md-2 col-form-label form-control-label">Datetime</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="datetime-local" value="2018-11-23T10:30:00" id="example-datetime-local-input">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-date-input" class="col-md-2 col-form-label form-control-label">Date</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="date" value="2018-11-23" id="example-date-input">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-month-input" class="col-md-2 col-form-label form-control-label">Month</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="month" value="2018-11" id="example-month-input">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-week-input" class="col-md-2 col-form-label form-control-label">Week</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="week" value="2018-W23" id="example-week-input">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-time-input" class="col-md-2 col-form-label form-control-label">Time</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="time" value="10:30:00" id="example-time-input">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-color-input" class="col-md-2 col-form-label form-control-label">Color</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="color" value="#5e72e4" id="example-color-input">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card-wrapper">
                    <!-- Sizes -->
                    <div class="card">
                        <!-- Card header -->
                        <div class="card-header">
                            <h3 class="mb-0">Sizes</h3>
                        </div>
                        <!-- Card body -->
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-control-label">Large input</label>
                                <input class="form-control form-control-lg" type="text" placeholder=".form-control-lg">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Default input</label>
                                <input class="form-control" type="text" placeholder="Default input">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Small input</label>
                                <input class="form-control form-control-sm" type="text" placeholder=".form-control-sm">
                            </div>
                        </div>
                    </div>
                    <!-- Textareas -->
                    <div class="card">
                        <!-- Card header -->
                        <div class="card-header">
                            <h3 class="mb-0">Text inputs</h3>
                        </div>
                        <!-- Card body -->
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label class="form-control-label" for="exampleFormControlTextarea1">Basic textarea</label>
                                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label" for="exampleFormControlTextarea2">Unresizable textarea</label>
                                    <textarea class="form-control" id="exampleFormControlTextarea2" rows="3" resize="none"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Selects -->
                    <div class="card">
                        <!-- Card header -->
                        <div class="card-header">
                            <h3 class="mb-0">Select</h3>
                        </div>
                        <!-- Card body -->
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label class="form-control-label" for="exampleFormControlSelect1">Basic select</label>
                                    <select class="form-control" id="exampleFormControlSelect1">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label" for="exampleFormControlSelect2">Basic select</label>
                                    <select class="form-control" id="exampleFormControlSelect2" disabled>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label" for="exampleFormControlSelect3">Multiple select</label>
                                    <select multiple class="form-control" id="exampleFormControlSelect3">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label" for="exampleFormControlSelect4">Disabled multiple select</label>
                                    <select multiple class="form-control" id="exampleFormControlSelect4" disabled>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- File browser -->
                    <div class="card">
                        <!-- Card header -->
                        <div class="card-header">
                            <h3 class="mb-0">File browser</h3>
                        </div>
                        <!-- Card body -->
                        <div class="card-body">
                            <form>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFileLang" lang="en">
                                    <label class="custom-file-label" for="customFileLang">Select file</label>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Checkboxes and radios -->
                    <div class="card">
                        <!-- Card header -->
                        <div class="card-header">
                            <h3 class="mb-0">Checkboxes and radios</h3>
                        </div>
                        <!-- Card body -->
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox mb-3">
                                            <input class="custom-control-input" id="customCheck1" type="checkbox">
                                            <label class="custom-control-label" for="customCheck1">Unchecked</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mb-3">
                                            <input class="custom-control-input" id="customCheck2" type="checkbox" checked>
                                            <label class="custom-control-label" for="customCheck2">Checked</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mb-3">
                                            <input class="custom-control-input" id="customCheck3" type="checkbox" disabled>
                                            <label class="custom-control-label" for="customCheck3">Disabled Unchecked</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mb-3">
                                            <input class="custom-control-input" id="customCheck4" type="checkbox" checked disabled>
                                            <label class="custom-control-label" for="customCheck4">Disabled Checked</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-radio mb-3">
                                            <input name="custom-radio-1" class="custom-control-input" id="customRadio5" type="radio">
                                            <label class="custom-control-label" for="customRadio5">Unchecked</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-3">
                                            <input name="custom-radio-1" class="custom-control-input" id="customRadio6" checked="" type="radio">
                                            <label class="custom-control-label" for="customRadio6">Checked</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-3">
                                            <input name="custom-radio-3" class="custom-control-input" id="customRadio7" disabled="" type="radio">
                                            <label class="custom-control-label" for="customRadio7">Disabled unchecked</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-3">
                                            <input name="custom-radio-4" class="custom-control-input" id="customRadio8" checked="" disabled="" type="radio">
                                            <label class="custom-control-label" for="customRadio8">Disabled checkbox</label>
                                        </div>
                                    </div>
                                </div>
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