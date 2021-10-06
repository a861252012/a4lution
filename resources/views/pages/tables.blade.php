@extends('layouts.app', [
    'parentSection' => 'tables',
    'elementName' => 'tables'
])

@section('content') 
    @component('layouts.headers.auth') 
        @component('layouts.headers.breadcrumbs')
            @slot('title') 
                {{ __('Tables') }} 
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'tables') }}">{{ __('Tables') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Tables') }}</li>
        @endcomponent 
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header border-0">
                        <h3 class="mb-0">Light table</h3>
                    </div>
                    <!-- Light table -->
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">Project</th>
                                    <th scope="col" class="sort" data-sort="budget">Budget</th>
                                    <th scope="col" class="sort" data-sort="status">Status</th>
                                    <th scope="col">Users</th>
                                    <th scope="col" class="sort" data-sort="completion">Completion</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <a href="#" class="avatar rounded-circle mr-3">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/bootstrap.jpg">
                                            </a>
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm">Argon Design System</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="budget">
                                        $2500 USD
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <i class="bg-warning"></i>
                                            <span class="status">pending</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="avatar-group">
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="completion mr-2">60%</span>
                                            <div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="#">Action</a>
                                                <a class="dropdown-item" href="#">Another action</a>
                                                <a class="dropdown-item" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <a href="#" class="avatar rounded-circle mr-3">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/angular.jpg">
                                            </a>
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm">Angular Now UI Kit PRO</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="budget">
                                        $1800 USD
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <i class="bg-success"></i>
                                            <span class="status">completed</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="avatar-group">
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="completion mr-2">100%</span>
                                            <div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="#">Action</a>
                                                <a class="dropdown-item" href="#">Another action</a>
                                                <a class="dropdown-item" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <a href="#" class="avatar rounded-circle mr-3">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/sketch.jpg">
                                            </a>
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm">Black Dashboard</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="budget">
                                        $3150 USD
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <i class="bg-danger"></i>
                                            <span class="status">delayed</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="avatar-group">
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="completion mr-2">72%</span>
                                            <div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100" style="width: 72%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="#">Action</a>
                                                <a class="dropdown-item" href="#">Another action</a>
                                                <a class="dropdown-item" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <a href="#" class="avatar rounded-circle mr-3">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/react.jpg">
                                            </a>
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm">React Material Dashboard</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="budget">
                                        $4400 USD
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <i class="bg-info"></i>
                                            <span class="status">on schedule</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="avatar-group">
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="completion mr-2">90%</span>
                                            <div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-info" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width: 90%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="#">Action</a>
                                                <a class="dropdown-item" href="#">Another action</a>
                                                <a class="dropdown-item" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <a href="#" class="avatar rounded-circle mr-3">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/vue.jpg">
                                            </a>
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm">Vue Paper UI Kit PRO</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="budget">
                                        $2200 USD
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <i class="bg-success"></i>
                                            <span class="status">completed</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="avatar-group">
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="completion mr-2">100%</span>
                                            <div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="#">Action</a>
                                                <a class="dropdown-item" href="#">Another action</a>
                                                <a class="dropdown-item" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Card footer -->
                    <div class="card-footer py-4">
                        <nav aria-label="...">
                            <ul class="pagination justify-content-end mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">
                                        <i class="fas fa-angle-left"></i>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2 <span class="sr-only">(current)</span></a>
                                </li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">
                                        <i class="fas fa-angle-right"></i>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <!-- Card header -->
            <div class="card-header border-0">
                <div class="row">
                    <div class="col-6">
                        <h3 class="mb-0">Inline actions</h3>
                    </div>
                    <div class="col-6 text-right">
                        <a href="#" class="btn btn-sm btn-neutral btn-round btn-icon" data-toggle="tooltip" data-original-title="Edit product">
                        <span class="btn-inner--icon"><i class="fas fa-user-edit"></i></span>
                        <span class="btn-inner--text">Export</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Light table -->
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>Author</th>
                            <th>Created at</th>
                            <th>Product</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="table-user">
                                <img src="{{ asset('argon') }}/img/theme/team-1.jpg" class="avatar rounded-circle mr-3">
                                <b>John Michael</b>
                            </td>
                            <td>
                                <span class="text-muted">10/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Dashboard PRO</a>
                            </td>
                            <td class="table-actions">
                                <a href="#!" class="table-action" data-toggle="tooltip" data-original-title="Edit product">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <a href="#!" class="table-action table-action-delete" data-toggle="tooltip" data-original-title="Delete product">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-user">
                                <img src="{{ asset('argon') }}/img/theme/team-2.jpg" class="avatar rounded-circle mr-3">
                                <b>Alex Smith</b>
                            </td>
                            <td>
                                <span class="text-muted">08/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Design System</a>
                            </td>
                            <td class="table-actions">
                                <a href="#!" class="table-action" data-toggle="tooltip" data-original-title="Edit product">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <a href="#!" class="table-action table-action-delete" data-toggle="tooltip" data-original-title="Delete product">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-user">
                                <img src="{{ asset('argon') }}/img/theme/team-3.jpg" class="avatar rounded-circle mr-3">
                                <b>Samantha Ivy</b>
                            </td>
                            <td>
                                <span class="text-muted">30/08/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Black Dashboard</a>
                            </td>
                            <td class="table-actions">
                                <a href="#!" class="table-action" data-toggle="tooltip" data-original-title="Edit product">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <a href="#!" class="table-action table-action-delete" data-toggle="tooltip" data-original-title="Delete product">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-user">
                                <img src="{{ asset('argon') }}/img/theme/team-1.jpg" class="avatar rounded-circle mr-3">
                                <b>John Michael</b>
                            </td>
                            <td>
                                <span class="text-muted">10/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Dashboard PRO</a>
                            </td>
                            <td class="table-actions">
                                <a href="#!" class="table-action" data-toggle="tooltip" data-original-title="Edit product">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <a href="#!" class="table-action table-action-delete" data-toggle="tooltip" data-original-title="Delete product">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-user">
                                <img src="{{ asset('argon') }}/img/theme/team-2.jpg" class="avatar rounded-circle mr-3">
                                <b>John Michael</b>
                            </td>
                            <td>
                                <span class="text-muted">10/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Dashboard PRO</a>
                            </td>
                            <td class="table-actions">
                                <a href="#!" class="table-action" data-toggle="tooltip" data-original-title="Edit product">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <a href="#!" class="table-action table-action-delete" data-toggle="tooltip" data-original-title="Delete product">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <!-- Card header -->
            <div class="card-header border-0">
                <div class="row">
                    <div class="col-6">
                        <h3 class="mb-0">Striped table</h3>
                    </div>
                    <div class="col-6 text-right">
                        <a href="#" class="btn btn-sm btn-primary btn-round btn-icon" data-toggle="tooltip" data-original-title="Edit product">
                            <span class="btn-inner--icon"><i class="fas fa-user-edit"></i></span>
                            <span class="btn-inner--text">Export</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Light table -->
            <div class="table-responsive">
                <table class="table align-items-center table-flush table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>Author</th>
                            <th>Created at</th>
                            <th>Product</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="table-user">
                                <img src="{{ asset('argon') }}/img/theme/team-1.jpg" class="avatar rounded-circle mr-3">
                                <b>John Michael</b>
                            </td>
                            <td>
                                <span class="text-muted">10/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Dashboard PRO</a>
                            </td>
                            <td class="table-actions">
                                <a href="#!" class="table-action" data-toggle="tooltip" data-original-title="Edit product">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <a href="#!" class="table-action table-action-delete" data-toggle="tooltip" data-original-title="Delete product">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-user">
                                <img src="{{ asset('argon') }}/img/theme/team-2.jpg" class="avatar rounded-circle mr-3">
                                <b>Alex Smith</b>
                            </td>
                            <td>
                                <span class="text-muted">08/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Design System</a>
                            </td>
                            <td class="table-actions">
                                <a href="#!" class="table-action" data-toggle="tooltip" data-original-title="Edit product">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <a href="#!" class="table-action table-action-delete" data-toggle="tooltip" data-original-title="Delete product">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-user">
                                <img src="{{ asset('argon') }}/img/theme/team-3.jpg" class="avatar rounded-circle mr-3">
                                <b>Samantha Ivy</b>
                            </td>
                            <td>
                                <span class="text-muted">30/08/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Black Dashboard</a>
                            </td>
                            <td class="table-actions">
                                <a href="#!" class="table-action" data-toggle="tooltip" data-original-title="Edit product">
                            <i class="fas fa-user-edit"></i>
                            </a>
                                <a href="#!" class="table-action table-action-delete" data-toggle="tooltip" data-original-title="Delete product">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-user">
                                <img src="{{ asset('argon') }}/img/theme/team-1.jpg" class="avatar rounded-circle mr-3">
                                <b>John Michael</b>
                            </td>
                            <td>
                                <span class="text-muted">10/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Dashboard PRO</a>
                            </td>
                            <td class="table-actions">
                                <a href="#!" class="table-action" data-toggle="tooltip" data-original-title="Edit product">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <a href="#!" class="table-action table-action-delete" data-toggle="tooltip" data-original-title="Delete product">
                            <i class="fas fa-trash"></i>
                            </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-user">
                                <img src="{{ asset('argon') }}/img/theme/team-2.jpg" class="avatar rounded-circle mr-3">
                                <b>John Michael</b>
                            </td>
                            <td>
                                <span class="text-muted">10/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Dashboard PRO</a>
                            </td>
                            <td class="table-actions">
                                <a href="#!" class="table-action" data-toggle="tooltip" data-original-title="Edit product">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                <a href="#!" class="table-action table-action-delete" data-toggle="tooltip" data-original-title="Delete product">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <!-- Card header -->
            <div class="card-header border-0">
                <div class="row">
                    <div class="col-6">
                        <h3 class="mb-0">Checkbox + Toggles</h3>
                    </div>
                    <div class="col-6 text-right">
                        <a href="#" class="btn btn-sm btn-danger btn-round btn-icon" data-toggle="tooltip" data-original-title="Edit product">
                            <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                            <span class="btn-inner--text">Delete</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Light table -->
            <div class="table-responsive">
                <table class="table align-items-center table-flush table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="table-check-all" type="checkbox">
                                    <label class="custom-control-label" for="table-check-all"></label>
                                </div>
                            </th>
                            <th>Author</th>
                            <th>Created at</th>
                            <th>Product</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="table-check-all" type="checkbox">
                                    <label class="custom-control-label" for="table-check-all"></label>
                                </div>
                            </th>
                            <td class="table-user">
                                <b>John Michael</b>
                            </td>
                            <td>
                                <span class="text-muted">10/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Dashboard PRO</a>
                            </td>
                            <td>
                                <label class="custom-toggle">
                                    <input type="checkbox" checked>
                                    <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="table-check-all" type="checkbox">
                                    <label class="custom-control-label" for="table-check-all"></label>
                                </div>
                            </th>
                            <td class="table-user">
                                <b>Alex Smith</b>
                            </td>
                            <td>
                                <span class="text-muted">08/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Design System</a>
                            </td>
                            <td>
                                <label class="custom-toggle">
                                    <input type="checkbox">
                                    <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="table-check-all" type="checkbox">
                                    <label class="custom-control-label" for="table-check-all"></label>
                                </div>
                            </th>
                            <td class="table-user">
                                <b>Samantha Ivy</b>
                            </td>
                            <td>
                                <span class="text-muted">30/08/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Black Dashboard</a>
                            </td>
                            <td>
                                <label class="custom-toggle">
                                    <input type="checkbox">
                                    <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="table-check-all" type="checkbox">
                                    <label class="custom-control-label" for="table-check-all"></label>
                                </div>
                            </th>
                            <td class="table-user">
                                <b>John Michael</b>
                            </td>
                            <td>
                                <span class="text-muted">10/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Dashboard PRO</a>
                            </td>
                            <td>
                                <label class="custom-toggle">
                                    <input type="checkbox" checked>
                                    <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="table-check-all" type="checkbox">
                                    <label class="custom-control-label" for="table-check-all"></label>
                                </div>
                            </th>
                            <td class="table-user">
                                <b>John Michael</b>
                            </td>
                            <td>
                                <span class="text-muted">10/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Dashboard PRO</a>
                            </td>
                            <td>
                                <label class="custom-toggle">
                                    <input type="checkbox" checked>
                                    <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <!-- Card header -->
            <div class="card-header border-0">
                <div class="row">
                    <div class="col-6">
                        <h3 class="mb-0">Checkbox + Labels</h3>
                    </div>
                    <div class="col-6 text-right">
                        <a href="#" class="btn btn-sm btn-danger btn-round btn-icon" data-toggle="tooltip" data-original-title="Edit product">
                            <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                            <span class="btn-inner--text">Delete</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Light table -->
            <div class="table-responsive">
                <table class="table align-items-center table-flush table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="table-check-all" type="checkbox">
                                    <label class="custom-control-label" for="table-check-all"></label>
                                </div>
                            </th>
                            <th>Author</th>
                            <th>Created at</th>
                            <th>Product</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-success">
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="table-check-all" type="checkbox">
                                    <label class="custom-control-label" for="table-check-all"></label>
                                </div>
                            </th>
                            <td class="table-user">
                                <b>John Michael</b>
                            </td>
                            <td>
                                <span class="text-muted">10/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Dashboard PRO</a>
                            </td>
                            <td>
                                <label class="custom-toggle">
                                    <input type="checkbox" checked>
                                    <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
                                </label>
                            </td>
                        </tr>
                        <tr class="table-">
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="table-check-all" type="checkbox">
                                    <label class="custom-control-label" for="table-check-all"></label>
                                </div>
                            </th>
                            <td class="table-user">
                                <b>Alex Smith</b>
                            </td>
                            <td>
                                <span class="text-muted">08/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Design System</a>
                            </td>
                            <td>
                                <label class="custom-toggle">
                                    <input type="checkbox">
                                    <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
                                </label>
                            </td>
                        </tr>
                        <tr class="table-warning">
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="table-check-all" type="checkbox">
                                    <label class="custom-control-label" for="table-check-all"></label>
                                </div>
                            </th>
                            <td class="table-user">
                                <b>Samantha Ivy</b>
                            </td>
                            <td>
                                <span class="text-muted">30/08/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Black Dashboard</a>
                            </td>
                            <td>
                                <label class="custom-toggle">
                                    <input type="checkbox">
                                    <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
                                </label>
                            </td>
                        </tr>
                        <tr class="table-">
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="table-check-all" type="checkbox">
                                    <label class="custom-control-label" for="table-check-all"></label>
                                </div>
                            </th>
                            <td class="table-user">
                                <b>John Michael</b>
                            </td>
                            <td>
                                <span class="text-muted">10/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Dashboard PRO</a>
                            </td>
                            <td>
                                <label class="custom-toggle">
                                    <input type="checkbox" checked>
                                    <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
                                </label>
                            </td>
                        </tr>
                        <tr class="table-">
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="table-check-all" type="checkbox">
                                    <label class="custom-control-label" for="table-check-all"></label>
                                </div>
                            </th>
                            <td class="table-user">
                                <b>John Michael</b>
                            </td>
                            <td>
                                <span class="text-muted">10/09/2018</span>
                            </td>
                            <td>
                                <a href="#!" class="font-weight-bold">Argon Dashboard PRO</a>
                            </td>
                            <td>
                                <label class="custom-toggle">
                                    <input type="checkbox" checked>
                                    <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card bg-transparent">
            <!-- Card header -->
            <div class="card-header bg-transparent border-0">
                <h3 class="mb-0">Translucent table</h3>
            </div>
            <!-- Translucent table -->
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="sort" data-sort="name">Project</th>
                            <th scope="col" class="sort" data-sort="budget">Budget</th>
                            <th scope="col" class="sort" data-sort="status">Status</th>
                            <th scope="col">Users</th>
                            <th scope="col" class="sort" data-sort="completion">Completion</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        <tr>
                            <th scope="row">
                                <div class="media align-items-center">
                                    <a href="#" class="avatar rounded-circle mr-3">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/bootstrap.jpg">
                                    </a>
                                    <div class="media-body">
                                        <span class="name mb-0 text-sm">Argon Design System</span>
                                    </div>
                                </div>
                            </th>
                            <td class="budget">
                                $2500 USD
                            </td>
                            <td>
                                <span class="badge badge-dot mr-4">
                                    <i class="bg-warning"></i>
                                    <span class="status">pending</span>
                                </span>
                            </td>
                            <td>
                                <div class="avatar-group">
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="completion mr-2">60%</span>
                                    <div>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <div class="media align-items-center">
                                    <a href="#" class="avatar rounded-circle mr-3">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/angular.jpg">
                                    </a>
                                    <div class="media-body">
                                        <span class="name mb-0 text-sm">Angular Now UI Kit PRO</span>
                                    </div>
                                </div>
                            </th>
                            <td class="budget">
                                $1800 USD
                            </td>
                            <td>
                                <span class="badge badge-dot mr-4">
                                    <i class="bg-success"></i>
                                    <span class="status">completed</span>
                                </span>
                            </td>
                            <td>
                                <div class="avatar-group">
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="completion mr-2">100%</span>
                                    <div>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <div class="media align-items-center">
                                    <a href="#" class="avatar rounded-circle mr-3">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/sketch.jpg">
                                    </a>
                                    <div class="media-body">
                                        <span class="name mb-0 text-sm">Black Dashboard</span>
                                    </div>
                                </div>
                            </th>
                            <td class="budget">
                                $3150 USD
                            </td>
                            <td>
                                <span class="badge badge-dot mr-4">
                            <i class="bg-danger"></i>
                            <span class="status">delayed</span>
                                </span>
                            </td>
                            <td>
                                <div class="avatar-group">
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="completion mr-2">72%</span>
                                    <div>
                                        <div class="progress">
                                            <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100" style="width: 72%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <div class="media align-items-center">
                                    <a href="#" class="avatar rounded-circle mr-3">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/react.jpg">
                                    </a>
                                    <div class="media-body">
                                        <span class="name mb-0 text-sm">React Material Dashboard</span>
                                    </div>
                                </div>
                            </th>
                            <td class="budget">
                                $4400 USD
                            </td>
                            <td>
                                <span class="badge badge-dot mr-4">
                                    <i class="bg-info"></i>
                                    <span class="status">on schedule</span>
                                </span>
                            </td>
                            <td>
                                <div class="avatar-group">
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="completion mr-2">90%</span>
                                    <div>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width: 90%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <div class="media align-items-center">
                                    <a href="#" class="avatar rounded-circle mr-3">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/vue.jpg">
                                    </a>
                                    <div class="media-body">
                                        <span class="name mb-0 text-sm">Vue Paper UI Kit PRO</span>
                                    </div>
                                </div>
                            </th>
                            <td class="budget">
                                $2200 USD
                            </td>
                            <td>
                                <span class="badge badge-dot mr-4">
                                    <i class="bg-success"></i>
                                    <span class="status">completed</span>
                                </span>
                            </td>
                            <td>
                                <div class="avatar-group">
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                    </a>
                                    <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="completion mr-2">100%</span>
                                    <div>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Dark table -->
        <div class="row">
            <div class="col">
                <div class="card bg-default shadow">
                    <div class="card-header bg-transparent border-0">
                        <h3 class="text-white mb-0">Dark table</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-dark table-flush">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">Project</th>
                                    <th scope="col" class="sort" data-sort="budget">Budget</th>
                                    <th scope="col" class="sort" data-sort="status">Status</th>
                                    <th scope="col">Users</th>
                                    <th scope="col" class="sort" data-sort="completion">Completion</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <a href="#" class="avatar rounded-circle mr-3">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/bootstrap.jpg">
                                            </a>
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm">Argon Design System</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="budget">
                                        $2500 USD
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <i class="bg-warning"></i>
                                            <span class="status">pending</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="avatar-group">
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="completion mr-2">60%</span>
                                            <div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="#">Action</a>
                                                <a class="dropdown-item" href="#">Another action</a>
                                                <a class="dropdown-item" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <a href="#" class="avatar rounded-circle mr-3">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/angular.jpg">
                                            </a>
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm">Angular Now UI Kit PRO</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="budget">
                                        $1800 USD
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <i class="bg-success"></i>
                                            <span class="status">completed</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="avatar-group">
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="completion mr-2">100%</span>
                                            <div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="#">Action</a>
                                                <a class="dropdown-item" href="#">Another action</a>
                                                <a class="dropdown-item" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <a href="#" class="avatar rounded-circle mr-3">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/sketch.jpg">
                                            </a>
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm">Black Dashboard</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="budget">
                                        $3150 USD
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <i class="bg-danger"></i>
                                            <span class="status">delayed</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="avatar-group">
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="completion mr-2">72%</span>
                                            <div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100" style="width: 72%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="#">Action</a>
                                                <a class="dropdown-item" href="#">Another action</a>
                                                <a class="dropdown-item" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <a href="#" class="avatar rounded-circle mr-3">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/react.jpg">
                                            </a>
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm">React Material Dashboard</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="budget">
                                        $4400 USD
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <i class="bg-info"></i>
                                            <span class="status">on schedule</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="avatar-group">
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="completion mr-2">90%</span>
                                            <div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-info" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width: 90%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="#">Action</a>
                                                <a class="dropdown-item" href="#">Another action</a>
                                                <a class="dropdown-item" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <a href="#" class="avatar rounded-circle mr-3">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/vue.jpg">
                                            </a>
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm">Vue Paper UI Kit PRO</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="budget">
                                        $2200 USD
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <i class="bg-success"></i>
                                            <span class="status">completed</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="avatar-group">
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Ryan Tompson">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Romina Hadid">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Alexander Smith">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                            </a>
                                            <a href="#" class="avatar avatar-sm rounded-circle" data-toggle="tooltip" data-original-title="Jessica Doe">
                                                <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="completion mr-2">100%</span>
                                            <div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="#">Action</a>
                                                <a class="dropdown-item" href="#">Another action</a>
                                                <a class="dropdown-item" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('layouts.footers.auth')
    </div>
@endsection