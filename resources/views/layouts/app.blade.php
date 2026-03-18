<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{ @$allSetting['name']->field_value_en }}</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="/uploads/images/{{ @$allSetting['logo']->field_value_en }}" rel="icon">
    <link href="/uploads/images/{{ @$allSetting['logo']->field_value_en }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Cairo:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

    <link href="/assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="/assets/vendor/quill/quill.bubble.css" rel="stylesheet">

    <link href="/assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="/assets/css/style.css" rel="stylesheet">
    <link href="/dist/filepond.css" rel="stylesheet" />
    @if (app()->getLocale() == 'ar' or session()->get('locale') == 'ar')
        <style>
            .header {
                direction: rtl;
            }

            .header-nav .nav-icon {
                margin-left: 25px;
                margin-right: 0px;
            }

            .header-nav .nav-profile img {
                padding-left: 4px;
            }
            .plus {
                border-top-right-radius: 0px;
                border-bottom-right-radius: 0px;
                border-top-left-radius: 5px;
                border-bottom-left-radius: 5px;
            }
            .minus {
                border-top-left-radius: 0px;
                border-bottom-left-radius: 0px;
                border-top-right-radius: 5px;
                  border-bottom-right-radius: 5px;
            }
            @media (min-width: 1200px) {

                #main,
                #footer {
                    @if(!isset(request()-> pos))
                    margin-right: 300px;
                    @endif
                    margin-left: 0px;
                }
            }

            .sidebar {
                right: 0;
                left: unset;
            }

            section {
                direction: rtl;
            }

            .breadcrumb {
                direction: rtl;
                text-align: right;
            }

            .breadcrumb-item+.breadcrumb-item::before {
                float: right;
                padding-left: var(--bs-breadcrumb-item-padding-x);
            }

            .breadcrumb-item+.breadcrumb-item .active {
                padding-right: var(--bs-breadcrumb-item-padding-x);

            }

            .pagetitle {
                direction: rtl;
            }

            .header-nav {
                margin-right: auto;
                margin-left: 0px !important;
            }

            @media (max-width: 1199px) {
                .sidebar {
                    left: -300px;
                }

                .toggle-sidebar .sidebar {}
            }

            @media (min-width: 1200px) {

                .toggle-sidebar #main,
                .toggle-sidebar #footer {
                    margin-right: 0;
                }
            }

            .header .search-form button {
                margin-right: -30px;
            }

            form {
                direction: rtl;
            }

            form input {
                direction: ltr;
            }

            .card-header {
                text-align: right;
            }

            .search-form {
                direction: ltr;
            }

            @media (min-width: 768px) {
                .dropdown-menu-arrow::before {
                    left: 20px;
                    right: unset;
                }
            }

            .table {
                direction: rtl;

            }

            .dropdown-item span {
                padding-right: 5px;
            }

            .card-body h2 {
                text-align: right;
            }

            #message {
                text-align: right;
            }
            .custom-dir{
                direction: rtl;
            }
        </style>
    @endif
    <style>
        .rounded-circle {
            display: none;
        }

        .table {
            margin-top: 10px;
        }

        .filepond--credits {
            display: none;
        }

        .text-gray-700 {
            margin-top: 10px;
        }


        .search-container .search-form input {
            border: 0;
            font-size: 14px;
            color: #012970;
            border: 1px solid rgba(1, 41, 112, 0.2);
            padding: 7px 38px 7px 8px;
            border-radius: 3px;
            transition: 0.3s;
            width: 100%;
        }

        .search-container .search-form button {
            border: 0;
            padding: 0;
            margin-left: -30px;
            background: none;
        }

        .dashboard .filter {
            display: none;
        }

        .ml-10 {
            margin-left: 10px;
        }

        .ml-20 {
            margin-left: 24px;
        }

        .mr-2 {
            margin-right: 5px;
        }

        .dir-rtl {
            direction: rtl;
        }

        .add-btn {
            background: #4154f1;
            border: solid 1px #4154f1;
        }

        .nav-tabs .nav-link {
            cursor: pointer;
        }

        .thin-p {
            padding: 5px;
            font-size: 13px;
        }

        .nav-tabs .nav-link {
            padding: 10px;
        }

        .text-right {
            text-align: right;
        }

        @media (max-width: 800px) {
            .text-right {
                text-align: left;
            }

            .mt-mobile {
                margin-top: 10px;
            }

            .toggle-sidebar .sidebar {}
        }
        .fitc{
            width: fit-content;
        }
    </style>
    @guest
        <style>
            @media (min-width: 1200px) {

                #main,
                #footer {
                    margin-left: unset;
                }
            }
        </style>
    @endguest
    @if(isset(request()-> pos))
    <style>
        #sidebar{
            left:-300px;
        }
        #main, #footer{
         margin-left: 0px;
        }
        .pagetitle{
            display:none;
        }
        .bold{
            font-weight: bolder;
        }
        .font-weight-bold{
            font-weight: bolder;
            Color: #2f5e2f;
        }
     </style>

    @endif

</head>

<body>

    <!-- ======= Header ======= -->
    @auth
        <header id="header" class="header fixed-top d-flex align-items-center">

            <div class="d-flex align-items-center justify-content-between">
                <a href="/admin" class="logo d-flex align-items-center">
                    <img src="/uploads/images/{{ @$allSetting['logo']->field_value_en }}" alt="" id="logo">
                    <span class="d-none d-lg-block">{{ @$allSetting['name']->field_value_en }}</span>
                </a>
                <i class="bi bi-list toggle-sidebar-btn"></i>
            </div><!-- End Logo -->

            <div class="search-bar">
                <form class="search-form d-flex align-items-center" method="POST" action="#">
                    <input type="text" name="query" placeholder="{{ __('Search Word') }}" title="Enter search keyword">
                    <button type="submit" title="{{ __('Search') }}"><i class="bi bi-search"></i></button>
                </form>
            </div><!-- End Search Bar -->

            <nav class="header-nav ms-auto">
                <ul class="d-flex align-items-center">
                    <li>
                        <a class="nav-link nav-icon" href="/admin/sales/create?pos=true" title=" POS ">
                            <i class="bi bi-coin"></i> POS
                        </a>
                    </li>
                    <li class="nav-item d-block d-lg-none">
                        <a class="nav-link nav-icon search-bar-toggle " href="#">
                            <i class="bi bi-search"></i>
                        </a>
                    </li><!-- End Search Icon-->
                    <li class="nav-item dropdown">

                        <!-- lang -->

                        @if (app()->getLocale() == 'ar' or session()->get('locale') == 'ar')
                            <a class="nav-link nav-icon" href="/admin/lang/en" title=" English ">
                                <i class="bi bi-globe"></i>
                            </a><!-- End Notification Icon -->
                    </li>
                @else
                    <a class="nav-link nav-icon" href="/admin/lang/ar" title=" العربية ">
                        <i class="bi bi-globe"></i>
                    </a><!-- End Notification Icon -->
                    </li>
                    @endif

                    <li class="nav-item dropdown pe-3">
                        @if (auth()->check())
                            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#"
                                data-bs-toggle="dropdown">
                                <img src="/assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
                                <span class="d-none d-md-block dropdown-toggle ps-2"> {{ Auth::user()->name }} </span>
                            </a><!-- End Profile Iamge Icon -->

                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                                <li class="dropdown-header">
                                    <h6> {{ Auth::user()->name }}</h6>
                                    <span>
                                        @if (Auth::user()->role)
                                            {{ app()->getLocale() == 'ar' ? @Auth::user()->role->label_ar : @Auth::user()->role->label_en }}
                                        @endif
                                    </span>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>


                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="/admin/editMyAccount">
                                        <i class="bi bi-gear"></i>
                                        <span>{{ __('Account Setting') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>


                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ env('APP_URL') }}/logout"
                                        onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();"
                                        role="button">
                                        <i class="bi bi-box-arrow-right"></i><span> {{ __('Sign Out') }}</span>
                                    </a>
                                </li>



                                <form id="logout-form" action="{{ env('APP_URL') }}/logout" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </ul><!-- End Profile Dropdown Items -->
                    </li><!-- End Profile Nav -->
                    @endif
                </ul>
            </nav><!-- End Icons Navigation -->

        </header><!-- End Header -->

        <!-- ======= Sidebar ======= -->
        <aside id="sidebar" class="sidebar" >


            <ul class="sidebar-nav" id="sidebar-nav">
                @if (auth()->user()->type == 1)
                    <li class="nav-item">
                        <a class="nav-link " href="/admin/configurations">
                            <i class="bi bi-grid"></i>
                            <span>{{ __('Configurations') }}</span>
                        </a>
                    </li><!-- End Dashboard Nav -->

                    <li class="nav-item">
                        <a class="nav-link @if (!Str::contains(Route::currentRouteName(), ['users', 'roles', 'permissions'])) collapsed @endif" data-bs-target="#users"
                            data-bs-toggle="collapse" href="#"
                            @if (Str::contains(Route::currentRouteName(), ['users', 'roles', 'permissions'])) aria-expanded="true" @endif>
                            <i class="bi  bi-person"></i><span>{{ __('Users') }}</span><i
                                class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <ul id="users" class="nav-content collapse @if (Str::contains(Route::currentRouteName(), ['users', 'roles', 'permissions'])) show @endif"
                            data-bs-parent="#sidebar-nav">
                            <li>
                                <a href="/admin/roles">
                                    <i class="bi bi-journal-check"></i>
                                    <span>{{ __('Roles') }}</span>
                                </a>
                            </li><!-- End Dashboard Nav -->
                            <li>
                                <a href="/admin/users">
                                    <i class="bi bi-circle"></i><span>{{ __('Users') }}</span>
                                </a>
                            </li>

                            <li>
                                <a href="/admin/user/create">
                                    <i class="bi bi-circle"></i><span>{{ __('Add User') }}</span>
                                </a>
                            </li>


                        </ul>
                    </li><!-- End Components Nav -->
                    <li class="nav-item">
                        <a class="nav-link @if (!Str::contains(Route::currentRouteName(), 'measures')) collapsed @endif" data-bs-target="#measures"
                            data-bs-toggle="collapse" href="#"
                            @if (Str::contains(Route::currentRouteName(), 'measures')) aria-expanded="true" @endif>
                            <i class="bi  bi-speedometer"></i><span>{{ __('Measures') }}</span><i
                                class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <ul id="measures" class="nav-content collapse @if (Str::contains(Route::currentRouteName(), 'measures')) show @endif"
                            data-bs-parent="#sidebar-nav">
                            <li>
                                <a href="/admin/measures">
                                    <i class="bi bi-circle"></i><span>{{ __('Measures') }}</span>
                                </a>
                            </li>

                            <li>
                                <a href="/admin/measures/create">
                                    <i class="bi bi-circle"></i><span>{{ __('Add Measure') }}</span>
                                </a>
                            </li>


                        </ul>
                    </li><!-- End Components Nav -->
                @endif
                @can('can-enter', ['edit_warehouse','add_warehouse','add_adjustment','edit_adjustment','delete_adjustment'])

                <li class="nav-item">
                    <a class="nav-link @if (!Str::contains(Route::currentRouteName(),['warehouses','adjustments'])) collapsed @endif" data-bs-target="#warehouses"
                        data-bs-toggle="collapse" href="#"
                        @if (Str::contains(Route::currentRouteName(), ['warehouses','adjustments'])) aria-expanded="true" @endif>
                        <i class="bi  bi-house-door"></i><span>{{ __('ًWarehouses') }}</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="warehouses" class="nav-content collapse @if (Str::contains(Route::currentRouteName(), ['warehouses','adjustments'])) show @endif"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="/admin/warehouses">
                                <i class="bi bi-circle"></i><span>{{ __('ًWarehouses') }}</span>
                            </a>
                        </li>
                        @can('can-enter', ['add_warehouse'])
                        <li>
                            <a href="/admin/warehouses/create">
                                <i class="bi bi-circle"></i><span>{{ __('Add Warehouse') }}</span>
                            </a>
                        </li>
                        @endcan
                        @can('can-enter', ['edit_adjustment'])
                        <li>
                            <a href="/admin/adjustments">
                                <i class="bi bi-circle"></i><span>{{ __('Adjustments') }}</span>
                            </a>
                        </li>
                        @endcan
                        @can('can-enter', ['add_adjustment'])
                        <li>
                            <a href="/admin/adjustments/create">
                                <i class="bi bi-circle"></i><span>{{ __('Add Adjustment') }}</span>
                            </a>
                        </li>
                        @endcan

                    </ul>

                </li><!-- End Components Nav -->
                @endcan
                @can('can-enter', ['add_currency','edit_currency'])
                <!-- currency -->
                <li class="nav-item">
                    <a class="nav-link @if (!Str::contains(Route::currentRouteName(), 'currencies')) collapsed @endif" data-bs-target="#currencies"
                        data-bs-toggle="collapse" href="#"
                        @if (Str::contains(Route::currentRouteName(), 'currencies')) aria-expanded="true" @endif>
                        <i class="bi  bi-cash-coin"></i><span>{{ __('Currencies') }}</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="currencies" class="nav-content collapse @if (Str::contains(Route::currentRouteName(), 'currencies')) show @endif"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="/admin/currencies">
                                <i class="bi bi-circle"></i><span>{{ __('Currencies') }}</span>
                            </a>
                        </li>
                        @can('can-enter', ['add_currency'])
                        <li>
                            <a href="/admin/currencies/create">
                                <i class="bi bi-circle"></i><span>{{ __('Add Currency') }}</span>
                            </a>
                        </li>
                        @endcan

                    </ul>
                </li><!-- End Components Nav -->
                @endcan
                @can('can-enter', ['add_expense','edit_expense'])
                <li class="nav-item">
                    <a class="nav-link @if (!Str::contains(Route::currentRouteName(), 'expenses')) collapsed @endif" data-bs-target="#expenses"
                        data-bs-toggle="collapse" href="#"
                        @if (Str::contains(Route::currentRouteName(), 'expenses')) aria-expanded="true" @endif>
                        <i class="bi  bi-cash-stack"></i><span>{{ __('Expenses') }}</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="expenses" class="nav-content collapse @if (Str::contains(Route::currentRouteName(), 'expenses')) show @endif"
                        data-bs-parent="#sidebar-nav">
                        @can('can-enter', ['add_expense_category','edit_expense_category'])
                        <li>
                            <a href="/admin/expensescategories">
                                <i class="bi bi-circle"></i><span>{{ __('Expenses Categories') }}</span>
                            </a>
                        </li>
                        @endcan
                        @can('can-enter', ['add_expense_category'])
                        <li>
                            <a href="/admin/expensescategories/create">
                                <i class="bi bi-circle"></i><span>{{ __('Add Expenses Category') }}</span>
                            </a>
                        </li>
                        @endcan
                        <li>
                            <a href="/admin/expenses">
                                <i class="bi bi-circle"></i><span>{{ __('Expenses') }}</span>
                            </a>
                        </li>
                        @can('can-enter', ['add_expense'])
                        <li>
                            <a href="/admin/expenses/create">
                                <i class="bi bi-circle"></i><span>{{ __('Add Expense') }}</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li><!-- End Components Nav -->
                @endcan
                <li class="nav-item">
                    <a class="nav-link @if (!Str::contains(Route::currentRouteName(), ['products', 'variants'])) collapsed @endif" data-bs-target="#products"
                        data-bs-toggle="collapse" href="#"
                        @if (Str::contains(Route::currentRouteName(), ['products', 'variants'])) aria-expanded="true" @endif>
                        <i class="bi  bi-back"></i><span>{{ __('Products') }}</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="products" class="nav-content collapse @if (Str::contains(Route::currentRouteName(), ['products', 'variants'])) show @endif"
                        data-bs-parent="#sidebar-nav">
                        @can('can-enter', ['add_brand','edit_brand'])
                        <li>
                            <a href="/admin/productsbrands"><i class="bi bi-circle"></i>{{ __('Brands') }}</a>
                        </li>
                        @endcan
                        @can('can-enter', ['add_brand'])
                        <li>
                            <a href="/admin/productsbrands/create"><i class="bi bi-circle"></i>{{ __('Add Brand') }}</a>
                        </li>
                        @endcan
                        @can('can-enter', ['add_variant','edit_variant'])
                        <li>
                            <a href="/admin/variants"><i class="bi bi-circle"></i>{{ __('Variants') }}</a>
                        </li>
                        @endcan
                        @can('can-enter', ['add_variant'])
                        <li>
                            <a href="/admin/variants/create"><i class="bi bi-circle"></i>{{ __('Add Variant') }}</a>
                        </li>
                        @endcan
                        @can('can-enter', ['add_product_category','edit_product_category'])
                        <li>
                            <a href="/admin/productscategories"><i
                                    class="bi bi-circle"></i>{{ __('Products Categories') }}</a>
                        </li>
                        @endcan
                        @can('can-enter', ['add_product_category'])
                        <li>
                            <a href="/admin/productscategories/create"><i
                                    class="bi bi-circle"></i>{{ __('Add Products Category') }}</a>
                        </li>
                        @endcan
                        @can('can-enter', ['add_product','edit_product'])
                        <li>
                            <a href="/admin/products">
                                <i class="bi bi-circle"></i><span>{{ __('Products') }}</span>
                            </a>
                        </li>
                        @endcan
                        @can('can-enter', ['add_product'])
                        <li>
                            <a href="/admin/products/create">
                                <i class="bi bi-circle"></i><span>{{ __('Add Product') }}</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li><!-- End Components Nav -->
                @can('can-enter', ['add_purchase','edit_purchase'])
                <li class="nav-item">
                    <a class="nav-link @if (!Str::contains(Route::currentRouteName(), 'purchases')) collapsed @endif" data-bs-target="#purchases"
                        data-bs-toggle="collapse" href="#"
                        @if (Str::contains(Route::currentRouteName(), 'purchases')) aria-expanded="true" @endif>
                        <i class="bi  bi-basket2"></i><span>{{ __('Purchases') }}</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="purchases" class="nav-content collapse @if (Str::contains(Route::currentRouteName(), 'purchases')) show @endif"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="/admin/purchases">
                                <i class="bi bi-circle"></i><span>{{ __('Purchases') }}</span>
                            </a>
                        </li>
                        @can('can-enter', ['add_purchase'])
                        <li>
                            <a href="/admin/purchases/create">
                                <i class="bi bi-circle"></i><span>{{ __('Add Purchase') }}</span>
                            </a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcan
                @can('can-enter', ['add_sale','edit_sale'])
                <li class="nav-item">
                    <a class="nav-link @if (!Str::contains(Route::currentRouteName(), 'sales')) collapsed @endif" data-bs-target="#sales"
                        data-bs-toggle="collapse" href="#"
                        @if (Str::contains(Route::currentRouteName(), 'sales')) aria-expanded="true" @endif>
                        <i class="bi  bi-currency-dollar"></i><span>{{ __('Sales') }}</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="sales" class="nav-content collapse @if (Str::contains(Route::currentRouteName(), 'sales')) show @endif"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="/admin/sales">
                                <i class="bi bi-circle"></i><span>{{ __('Sales') }}</span>
                            </a>
                        </li>
                        @can('can-enter', ['add_sale'])
                        <li>
                            <a href="/admin/sales/create">
                                <i class="bi bi-circle"></i><span>{{ __('Add Sale') }}</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @can('can-enter', ['add_supplier','edit_supplier'])
                <li class="nav-item">
                    <a class="nav-link @if (!Str::contains(Route::currentRouteName(), 'suppliers')) collapsed @endif" data-bs-target="#suppliers"
                        data-bs-toggle="collapse" href="#"
                        @if (Str::contains(Route::currentRouteName(), 'suppliers')) aria-expanded="true" @endif>
                        <i class="bi  bi-truck"></i><span>{{ __('Suppliers') }}</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="suppliers" class="nav-content collapse @if (Str::contains(Route::currentRouteName(), 'suppliers')) show @endif"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="/admin/suppliers">
                                <i class="bi bi-circle"></i><span>{{ __('Suppliers') }}</span>
                            </a>
                        </li>
                        @can('can-enter', ['add_supplier'])
                        <li>
                            <a href="/admin/suppliers/create">
                                <i class="bi bi-circle"></i><span>{{ __('Add Supplier') }}</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @can('can-enter', ['edit_client','add_client'])
                <li class="nav-item">
                    <a class="nav-link @if (!Str::contains(Route::currentRouteName(), 'clients')) collapsed @endif" data-bs-target="#clients"
                        data-bs-toggle="collapse" href="#"
                        @if (Str::contains(Route::currentRouteName(), 'clients')) aria-expanded="true" @endif>
                        <i class="bi  bi-file-earmark-person-fill"></i><span>{{ __('Clients') }}</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="clients" class="nav-content collapse @if (Str::contains(Route::currentRouteName(), 'clients')) show @endif"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="/admin/clients">
                                <i class="bi bi-circle"></i><span>{{ __('Clients') }}</span>
                            </a>
                        </li>
                        @can('can-enter', ['add_client'])
                        <li>
                            <a href="/admin/clients/create">
                                <i class="bi bi-circle"></i><span>{{ __('Add Client') }}</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                <li class="nav-item">
                    <a class="nav-link @if (!Str::contains(Route::currentRouteName(), 'accounting')) collapsed @endif" data-bs-target="#accounting"
                        data-bs-toggle="collapse" href="#"
                        @if (Str::contains(Route::currentRouteName(), 'accounting')) aria-expanded="true" @endif>
                        <i class="bi  bi-currency-exchange"></i><span>{{ __('Accounting') }}</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="accounting" class="nav-content collapse @if (Str::contains(Route::currentRouteName(), 'accounting')) show @endif"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('accounting.profit-loss') }}">
                                <i class="bi bi-circle"></i><span>{{ __('Profit & Loss') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounting.accounts-receivable') }}">
                                <i class="bi bi-circle"></i><span>{{ __('Accounts Receivable') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounting.accounts-payable') }}">
                                <i class="bi bi-circle"></i><span>{{ __('Accounts Payable') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounting.cashflow') }}">
                                <i class="bi bi-circle"></i><span>{{ __('Cashflow') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounting.vat-summary') }}">
                                <i class="bi bi-circle"></i><span>{{ __('VAT Summary') }}</span>
                            </a>
                        </li>
                        <li class="nav-heading pb-0">{{ __('reports.menu.gl_reports') }}</li>
                        <li>
                            <a href="{{ route('accounting.gl-reports.trial-balance') }}">
                                <i class="bi bi-circle"></i><span>{{ __('Trial Balance') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounting.gl-reports.profit-loss') }}">
                                <i class="bi bi-circle"></i><span>{{ __('Profit & Loss') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounting.gl-reports.balance-sheet') }}">
                                <i class="bi bi-circle"></i><span>{{ __('Balance Sheet') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounting.gl-reports.vat-summary') }}">
                                <i class="bi bi-circle"></i><span>{{ __('reports.gl.vat_summary') }}</span>
                            </a>
                        </li>
                        <li class="nav-heading pb-0">{{ __('reports.menu.gl_management') }}</li>
                        <li>
                            <a href="{{ route('accounting.gl-management.chart-of-accounts') }}">
                                <i class="bi bi-circle"></i><span>{{ __('Chart of Accounts') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounting.gl-management.journal-entries') }}">
                                <i class="bi bi-circle"></i><span>{{ __('Journal Entries') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounting.gl-management.accounting-mappings') }}">
                                <i class="bi bi-circle"></i><span>{{ __('Accounting Mappings') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounting.gl-management.opening-balances') }}">
                                <i class="bi bi-circle"></i><span>{{ __('reports.menu.opening_balances') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounting.gl-management.periods') }}">
                                <i class="bi bi-circle"></i><span>{{ __('reports.menu.periods') }}</span>
                            </a>
                        </li>
                        @if (auth()->user()->type == 1)
                        <li>
                            <a href="{{ route('accounting.gl-management.vat-rates') }}">
                                <i class="bi bi-circle"></i><span>{{ __('VAT Options') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @if (false)
                <li class="nav-item">
                    <a class="nav-link @if (!Str::contains(Route::currentRouteName(), 'reports')) collapsed @endif" data-bs-target="#reports"
                        data-bs-toggle="collapse" href="#"
                        @if (Str::contains(Route::currentRouteName(), 'reports')) aria-expanded="true" @endif>
                        <i class="bi  bi-bar-chart-line-fill"></i><span>{{ __('Reports') }}</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="reports" class="nav-content collapse @if (Str::contains(Route::currentRouteName(), 'reports')) show @endif"
                        data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="#">
                                <i class="bi bi-circle"></i><span>{{ __('Purchases Reports') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="bi bi-circle"></i><span>{{ __('Sales Reports') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

            </ul>

        </aside><!-- End Sidebar-->
    @endauth
    <main id="main" class="main">
        <!-- ====== Messages ====== -->
        @if ($errors->any())
            <div class="alert alert-danger toremove-beforeajax mt-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }} </li>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success toremove-beforeajax mt-4" id="message">
                <i class="fa fa-check-circle fa-lg"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger toremove-beforeajax mt-4" id="message">
                <i class="fa fa-exclamation-triangle fa-lg"></i> {{ session('error') }}
            </div>
        @endif
        <!-- ====== End Messages == -->
        @yield('content')
    </main>
    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; {{ __('Copyright') }} <strong><span> {{ @$allSetting['name']->field_value_en }} </span></strong>
            {{ __('All Rights Reserved') }}

        </div>
        <div class="credits">
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="/assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/vendor/chart.js/chart.umd.js"></script>
    <script src="/assets/vendor/echarts/echarts.min.js"></script>
    <script src="/assets/vendor/quill/quill.min.js"></script>
    <script src="/assets/vendor/simple-datatables/simple-datatables.js"></script>
    @if (Str::contains(Route::currentRouteName(), ['products']))
    <script src="/assets/vendor/tinymce/tinymce.min.js"></script>
    @endif
    <script src="/assets/vendor/php-email-form/validate.js"></script>
    <script src="/dist/filepond.js"></script>
    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>
    <script>
        // Get a reference to the file input element

        const inputElement = document.querySelector('input[type="file"].filepond');
        if (inputElement) {
            const process = inputElement.getAttribute('process');
            const toUpdate = inputElement.getAttribute('toUpdate');
            const updateHidden = inputElement.getAttribute('updateHidden');


            const csrfToken = '{{ csrf_token() }}';

            const pond = FilePond.create(inputElement).setOptions({
                server: {

                    process: {
                        url: process,
                        method: 'POST',

                        onload: (response) => {
                            const json = JSON.parse(response);
                            if (json.success && toUpdate != null && toUpdate != 'imgproduct') {
                                var image = document.getElementById(toUpdate);
                                image.src = json.file.url;
                                if (updateHidden != null) {
                                    document.getElementById(updateHidden).value = json.file.filename;
                                }
                            }
                            if (toUpdate == 'imgproduct' && json.success) {
                                console.log('entered');
                                const url = inputElement.getAttribute('afterUpload');
                                const productImagesDiv = document.getElementById('product-images-ajax');
                                fetch(url)
                                    .then(response => response.text()) // convert the response to text
                                    .then(html => {
                                        productImagesDiv.innerHTML = html;

                                    })
                                    .catch(error => {
                                        console.error(error);
                                    });
                            }

                        },
                        onerror: null,
                        ondata: null,
                    },

                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    onpreparefile: (fileItem, output) => {
                        const img = new Image();
                        img.src = URL.createObjectURL(output);
                        document.body.appendChild(img);
                    }
                }
            });
        }
        /** begin modal */

        function deleteit(id, type = '', msq = '{{ __('Delete') }}') {
            event.preventDefault();
            (async () => {
                const result = await b_confirm(msq)
                if (!result) {
                    console.log(id);
                    if (type != 'deleteproduct') {
                        document.getElementById(id).submit();
                    }
                    if (type == 'deleteproduct') {
                        var element = document.getElementById("img-container-" + id);
                        element.style.display = "none";
                        product_image(id, 'delete')

                    }
                }
            })()
        }
        async function b_confirm(msg) {
            const modalElem = document.createElement('div')
            modalElem.id = "modal-confirm"
            modalElem.className = "modal"
            modalElem.innerHTML = `
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-body fs-6">
          <p>${msg}</p>
          <p>{{ __('Are you Sure?') }}</p>
      </div>    <!-- modal-body -->
      <div class="modal-footer" style="border-top:0px">
        <button id="modal-btn-descartar" type="button" class="btn btn-danger">{{ __('Yes') }}</button>
        <button id="modal-btn-aceptar" type="button" class="btn btn-success">{{ __('No') }}</button>
      </div>
    </div>
  </div>
  `
            const myModal = new bootstrap.Modal(modalElem, {
                keyboard: false,
                backdrop: 'static'
            })
            myModal.show()

            return new Promise((resolve, reject) => {
                document.body.addEventListener('click', response)

                function response(e) {
                    let bool = false
                    if (e.target.id == 'modal-btn-descartar') bool = false
                    else if (e.target.id == 'modal-btn-aceptar') bool = true
                    else return

                    document.body.removeEventListener('click', response)
                    document.body.querySelector('.modal-backdrop').remove()
                    modalElem.remove()
                    resolve(bool)
                }
            })
        }

        function product_image(id, type = 'main') {

            var url = "{{ env('APP_URL') }}/admin/products/setimage/?id=" + id;
            if (type == 'delete') {
                var url = "{{ env('APP_URL') }}/admin/products/deleteimage/?id=" + id;
            }
            // Make the POST request
            fetch(url)
                .then(response => response.text()) // convert the response to text
                .then(data => {
                    if (type == 'main') {
                        document.getElementById('main-' + id).innerHTML =
                            "<span class=\"badge bg-success\"> {{ __('Main') }} </span>";
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                });
        }
    </script>
</body>

</html>
