<!DOCTYPE html>

<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
        <meta name="description" content="Telkom Indonesia" />
        <meta name="keywords" content="Telkom Indonesia" />
        <meta name="author" content="Telkom Indonesia" />

        <title>Telkom - @yield('title')</title>
        <link href='https://fonts.googleapis.com/css?family=Roboto:100,400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="{{Asset('lib/css/bootstrap.min.css')}}" />
        <link rel="stylesheet" href="{{Asset('lib/c3/c3.css')}}" />
        <link rel="stylesheet" href="{{Asset('css/dist/template/header-footer.css')}}" />
        <link rel="stylesheet" type="text/css" href="{{Asset('lib/bootstrap/css/jquery.dataTables.css')}}">
        <link rel="stylesheet" href="{{Asset('css/jquery-ui.min.css')}}">
        <link rel="stylesheet" href="{{Asset('css/jquery-ui.structure.min.css')}}">
        <link rel="stylesheet" href="{{Asset('css/jquery-ui.theme.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{Asset('adminlte/bower_components/font-awesome/css/font-awesome.min.css')}}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{Asset('adminlte/bower_components/Ionicons/css/ionicons.min.css')}}">
        <link rel="stylesheet" href="{{Asset('adminlte/bower_components/jvectormap/jquery-jvectormap.css')}}">
        <link rel="stylesheet" href="{{Asset('adminlte/dist/css/AdminLTE.min.css')}}">
        <link rel="stylesheet" href="{{Asset('adminlte/dist/css/skins/_all-skins.min.css')}}">
        <link rel="stylesheet" href="{{Asset('css/chosen.css')}}">
        <style>
            .loader {
                border: 16px solid #f3f3f3; /* Light grey */
                border-top: 16px solid #3498db; /* Blue */
                border-radius: 50%;
                width: 60px;
                height: 60px;
                margin-left: 50%;
                animation: spin 2s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .legend {
                width: 100%;
                right: 20px;
                text-align: center;
            }
            .legend ul li{list-style:none;float:left;margin-right:8px;display:inline-block;padding-bottom:10px;padding-left :10px;padding-right :10px;}
            .legend ul li:nth-child(6n){

            }

            .legend ul li:nth-child(6n+1){
                clear:both;
            }

            .legend ul li:nth-child(-n+6){
            }
            .legend ul li span{ width:50px; height:15px; margin-right:5px; float:left; }
        </style>
        @yield('css-content')
    </head>

    <body>

        <section id="sidebar" style="padding-top:8px;">
            <div class="sidebar-sheet">
                <div class="head-sidebar">
                    <!--<img class="img-responsive" src="{{Asset('images/profpic-holder.png')}}" alt="profil picture" />-->
                    <p class="fl300 margbot20">Hello, <span class="fl700">{{Auth::user()->UserEmail}}</span></p>

                    <!--<a id="profile-trigger" href="{{--Route('showProfile')--}}" @if($page == 'profile') class="active-sidemenu margr20 fl300 cwhite" @else class="margr20 fl300 cwhite" @endif>Profile</a>-->
                    <a href="{{Route('showLogout')}}" class="fl300 cwhite">Logout</a>
                </div>
                <ul class="sidebar-menu tree list-sidebar-menu" data-widget="tree">
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-area-chart"></i> <span>Reporting View</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu list-sidebar-menu" style="display: none;">
                            <li><a href="{{Route('showDashboard')}}" @if($page == 'dashboard') class="active-sidemenu" @endif >Dashboard</a></li>
                            @if(Auth::user()->Position <= 1)
                            <li><a href="{{Route('showInventory')}}" @if($page == 'inventory') class="active-sidemenu" @endif >View Inventory</a></li>
                            @endif
                        </ul>
                    </li>
                    @if(Auth::user()->Position <= 1)
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-spinner"></i> <span>Main Process</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu list-sidebar-menu" style="display: none;">
                            <li><a href="{{Route('showInsertInventory')}}" @if($page == 'insert inventory') class="active-sidemenu" @endif >Inventory Shipin</a></li>
                            <li><a href="{{Route('showInventoryShipout')}}" @if($page == 'inventory shipout') class="active-sidemenu" @endif >Inventory Shipout</a></li>
                            <li><a href="{{Route('showReturnInventory')}}" @if($page == 'inventory return') class="active-sidemenu" @endif >Inventory Return</a></li>
                            <li><a href="{{Route('showConsignment')}}" @if($page == 'shipout consignment') class="active-sidemenu" @endif >Shipout Consignment</a></li>
                            <li><a href="{{Route('showWarehouseInventory')}}" @if($page == 'inventory warehouse') class="active-sidemenu" @endif >Move Warehouse</a></li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-cog"></i> <span>Setting</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu list-sidebar-menu" style="display: none;">
                            <li><a href="{{Route('showChange')}}" @if($page == 'edit name') class="active-sidemenu" @endif >Edit Name</a></li>
                            <li><a href="{{Route('showUncat')}}" @if($page == 'Uncatagorized Inventory') class="active-sidemenu" @endif >Uncatagorized Inventory</a></li>
                            <li><a href="{{Route('showInsertReporting')}}" @if($page == 'insert reporting') class="active-sidemenu" @endif >Insert Reporting</a></li>
                            @if(Auth::user()->Position == 0)
                            <li><a href="{{Route('showResetReporting')}}" @if($page == 'reset reporting') class="active-sidemenu" @endif >Reset Reporting</a></li>
                            <li><a href="{{Route('showAddAdmin')}}" @if($page == 'Add User') class="active-sidemenu" @endif >Add User</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                </ul>
            </div>
        </section>

        <section class="nav-primary container-fluid">
            <div style="color: #ffffff;
                 font-weight: 400;
                 display: inline-block;
                 font-size: 18px;
                 padding: 5px 0;" class="hoverpointer text-capitalize"><span id="logo" class="glyphicon glyphicon-menu-hamburger"></span> @yield('title-view')</div>
        </section>

        <section class="main-section container-fluid">
            @yield('main-section')
        </section>

        <script src="{{Asset('lib/js/jquery-1.11.3.min.js')}}"></script>
        <script src="{{Asset('lib/js/bootstrap.min.js')}}"></script>
        <!--<script src="{{Asset('lib/c3/d3.js')}}"></script>-->
        <!--<script src="{{Asset('lib/c3/c3.min.js')}}"></script>-->
        <script src="{{Asset('js/header-footer.js')}}"></script>
        <!-- FastClick -->
        <script src="{{Asset('adminlte/bower_components/fastclick/lib/fastclick.js')}}"></script>
        <!-- AdminLTE App -->
        <script src="{{Asset('adminlte/dist/js/adminlte.min.js')}}"></script>
        <!-- Sparkline -->
        <script src="{{Asset('adminlte/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js')}}"></script>
        <!-- jvectormap  -->
        <script src="{{Asset('adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
        <script src="{{Asset('adminlte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
        <!-- SlimScroll -->
        <script src="{{Asset('adminlte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>
        <!-- ChartJS -->
        <!--<script src="{{Asset('adminlte/bower_components/Chart.js/Chart.js')}}"></script>-->
        <script src="{{Asset('lib/js/Chart.bundle.js')}}"></script>
        <script src="{{Asset('lib/js/utils.js')}}"></script>
        @yield('js-content')
    </body>
</html>