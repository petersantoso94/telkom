<!DOCTYPE html>

<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
        <meta name="description" content="Telkom Indonesia" />
        <meta name="keywords" content="Telkom Indonesia" />
        <meta name="author" content="Telkom Indonesia" />

        <title>So mini - @yield('title')</title>
        <link href='https://fonts.googleapis.com/css?family=Roboto:100,400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="{{Asset('lib/css/bootstrap.min.css')}}" />
        <link rel="stylesheet" href="{{Asset('css/dist/template/header-footer.css')}}" />
        <link rel="stylesheet" type="text/css" href="{{Asset('lib/bootstrap/css/jquery.dataTables.css')}}">
        <link rel="stylesheet" href="{{Asset('css/jquery-ui.min.css')}}">
        <link rel="stylesheet" href="{{Asset('css/jquery-ui.structure.min.css')}}">
        <link rel="stylesheet" href="{{Asset('css/jquery-ui.theme.min.css')}}">
        <link rel="stylesheet" href="{{Asset('css/chosen.css')}}">
        @yield('css-content')
    </head>

    <body>

        <section id="sidebar">
            <div class="sidebar-sheet">
                <div class="head-sidebar">
                    <!--<img class="img-responsive" src="{{Asset('images/profpic-holder.png')}}" alt="profil picture" />-->
                    <p class="fl300 margbot20">Hello, <span class="fl700">{{Auth::user()->UserEmail}}</span></p>

                    <!--<a id="profile-trigger" href="{{--Route('showProfile')--}}" @if($page == 'profile') class="active-sidemenu margr20 fl300 cwhite" @else class="margr20 fl300 cwhite" @endif>Profile</a>-->
                    <a href="{{Route('showLogout')}}" class="fl300 cwhite">Logout</a>
                </div>

                
                <ul class="list-sidebar-menu">
                    <li><a href="{{Route('showInventory')}}" @if($page == 'inventory') class="active-sidemenu" @endif >View Inventory</a></li>
                </ul>
                <ul class="list-sidebar-menu">
                    <li><a href="{{Route('showInsertInventory')}}" @if($page == 'insert inventory') class="active-sidemenu" @endif >Inventory Shipin</a></li>
                </ul>
                <ul class="list-sidebar-menu">
                    <li><a href="{{Route('showInventoryShipout')}}" @if($page == 'inventory shipout') class="active-sidemenu" @endif >Inventory Shipout</a></li>
                </ul>
                <ul class="list-sidebar-menu">
                    <li><a href="{{Route('showReturnInventory')}}" @if($page == 'inventory return') class="active-sidemenu" @endif >Inventory Return</a></li>
                </ul>
                <ul class="list-sidebar-menu">
                    <li><a href="{{Route('showWarehouseInventory')}}" @if($page == 'inventory warehouse') class="active-sidemenu" @endif >Move Warehouse</a></li>
                </ul>
                <ul class="list-sidebar-menu">
                    <li><a href="{{Route('showChange')}}" @if($page == 'edit name') class="active-sidemenu" @endif >Edit Name</a></li>
                </ul>
            </div>
        </section>

        <section class="nav-primary container-fluid">
            <div id="logo" class="hoverpointer text-capitalize"><span class="glyphicon glyphicon-menu-hamburger"></span> @yield('title-view')</div>
        </section>

        <section class="main-section container-fluid">
            @yield('main-section')
        </section>

        <script src="{{Asset('lib/js/jquery-1.11.3.min.js')}}"></script>
        <script src="{{Asset('lib/js/bootstrap.min.js')}}"></script>
        <script src="{{Asset('js/header-footer.js')}}"></script>
        @yield('js-content')
    </body>
</html>