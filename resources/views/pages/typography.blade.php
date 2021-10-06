@extends('layouts.app', [
    'parentSection' => 'components',
    'elementName' => 'typography'
]) 

@section('content') 
    @component('layouts.headers.auth') 
        @component('layouts.headers.breadcrumbs')
            @slot('title') 
                {{ __('Typography') }} 
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'typography') }}">{{ __('Components') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Typography') }}</li>
        @endcomponent 
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row justify-content-center">
            <div class="col-lg-8 card-wrapper">
                <!-- Headings -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Headings</h3>
                    </div>
                    <div class="card-body">
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Heading 1</small>
                            </div>
                            <div class="col-sm-9">
                                <h1 class="mb-0">Argon Dashboard PRO</h1>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Heading 2</small>
                            </div>
                            <div class="col-sm-9">
                                <h2 class="mb-0">Argon Dashboard PRO</h2>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Heading 3</small>
                            </div>
                            <div class="col-sm-9">
                                <h3 class="mb-0">Argon Dashboard PRO</h3>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Heading 4</small>
                            </div>
                            <div class="col-sm-9">
                                <h4 class="mb-0">Argon Dashboard PRO</h4>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Heading 5</small>
                            </div>
                            <div class="col-sm-9">
                                <h5 class="mb-0">Argon Dashboard PRO </h5>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Heading 6</small>
                            </div>
                            <div class="col-sm-9">
                                <h6 class="mb-0">Argon Dashboard PRO </h6>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Display titles -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Display titles</h3>
                    </div>
                    <div class="card-body">
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Display 1</small>
                            </div>
                            <div class="col-sm-9">
                                <h1 class="display-1 mb-0">Argon Dashboard PRO</h1>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Display 2</small>
                            </div>
                            <div class="col-sm-9">
                                <h2 class="display-2 mb-0">Argon Dashboard PRO</h2>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Display 3</small>
                            </div>
                            <div class="col-sm-9">
                                <h3 class="display-3 mb-0">Argon Dashboard PRO</h3>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Display 4</small>
                            </div>
                            <div class="col-sm-9">
                                <h4 class="display-4 mb-0">Argon Dashboard PRO</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Specialized titles -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Specialized titles</h3>
                    </div>
                    <div class="card-body">
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Heading</small>
                            </div>
                            <div class="col-sm-9">
                                <h3 class="heading mb-0">Argon Dashboard PRO</h3>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Heading title</small>
                            </div>
                            <div class="col-sm-9">
                                <h3 class="heading-title text-warning mb-0">Argon Dashboard PRO</h3>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Heading seaction</small>
                            </div>
                            <div class="col-sm-9">
                                <div>
                                    <h2 class="display-3">Header with small subtitle </h2>
                                    <p class="lead text-muted">According to the National Oceanic and Atmospheric Administration, Ted, Scambos, NSIDClead
                                        scentist, puts the potentially record maximum.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Paragraphs -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Paragraphs</h3>
                    </div>
                    <div class="card-body">
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Paragraph</small>
                            </div>
                            <div class="col-sm-9">
                                <p>I will be the leader of a company that ends up being worth billions of dollars, because I got
                                    the answers. I understand culture. I am the nucleus. I think that’s a responsibility that
                                    I have, to push possibilities, to show people, this is the level that things could be at.</p>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Lead text</small>
                            </div>
                            <div class="col-sm-9">
                                <p class="lead">I will be the leader of a company that ends up being worth billions of dollars, because I got
                                    the answers. I understand culture. I am the nucleus. I think that’s a responsibility that
                                    I have, to push possibilities, to show people, this is the level that things could be at.</p>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Quote</small>
                            </div>
                            <div class="col-sm-9">
                                <blockquote class="blockquote">
                                    <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
                                    <footer class="blockquote-footer">Someone famous in
                                        <cite title="Source Title">Source Title</cite>
                                    </footer>
                                </blockquote>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Muted text</small>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">I will be the leader of a company that ends up being worth billions of dollars, because I got
                                    the answers...</p>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Primary text</small>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-primary">I will be the leader of a company that ends up being worth billions of dollars, because I got
                                    the answers...</p>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Info text</small>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-info mb-0">I will be the leader of a company that ends up being worth billions of dollars, because I got
                                    the answers...</p>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Success text</small>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-success mb-0">I will be the leader of a company that ends up being worth billions of dollars, because I got
                                    the answers...</p>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Warning text</small>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-warning mb-0">I will be the leader of a company that ends up being worth billions of dollars, because I got
                                    the answers...</p>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-sm-3">
                                <small class="text-uppercase text-muted font-weight-bold">Danger text</small>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-danger mb-0">I will be the leader of a company that ends up being worth billions of dollars, because I got
                                    the answers...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('layouts.footers.auth')
    </div>
@endsection