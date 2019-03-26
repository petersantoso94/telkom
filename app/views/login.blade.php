<!DOCTYPE html>

<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
        <meta name="description" content="Telkom Indonesia" />
        <meta name="keywords" content="Telkom Indonesia" />
        <meta name="author" content="Telkom Indonesia" />

        <title>Telkom Indonesia</title>
        <link href='https://fonts.googleapis.com/css?family=Roboto:100,400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="{{URL::asset('lib/css/bootstrap.min.css')}}" />
        <link rel="stylesheet" href="{{URL::asset('css/dist/template/header-footer.css')}}" />
        <link rel="stylesheet" href="{{URL::asset('css/dist/login.css')}}" />
    </head>

    <body>
        <section id="jumbo-login">
            <div id="inner-jumbo">
                <h4 class="fw100 text-center" id="login-text">Login to</h4>
                <p id="login-desc" class="cwhite fw400 text-center">Inventory management system for Telkom Indonesia</p>
            </div>
        </section>
        <section id="login-section">
            @if(isset($messages))
            @if($messages == 'salahLogin')
            <div class="row nopadd">
                <div class="col-xs-20 col-xs-offset-2 col-sm-12 col-sm-offset-6 col-md-8 col-md-offset-8 nopadd">
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Failed!</strong> Data entry error occurred.
                        @if(isset($errors))
                        <ul>
                            @foreach($errors->all('<li>:message</li>') as $message)
                            {{$message}}
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            @if($messages == 'gagalLogin')
            <div class="row nopadd">
                <div class="col-xs-20 col-xs-offset-2 col-sm-12 col-sm-offset-6 col-md-8 col-md-offset-8 nopadd">
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close no-shadow" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Oops !</strong> your username or password are mismatch, please try again.
                    </div>
                </div>
            </div>
            @endif
              @if($messages == 'belumLogin')
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="alert alert-danger alert-dismissible fr400" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Sorry!</strong> You must login first!
                    </div>
                </div>
                @endif
            @endif
            <!--{{Hash::make('admintelkom123!')}}-->
            <!--{{Request::ip();}}-->
            <div class="clearfix">
                <div class="col-xs-20 col-xs-offset-2 col-sm-12 col-sm-offset-6 col-md-8 col-md-offset-8">
                    <div class="white-pane padd15" id="form-wrapper">
                        <form method="POST">
                            <label class="cgrey fw300 margbot10">ID</label>
                            <input required type="text" class="input-stretch margbot20" name="email-parent"  placeholder="ID" />
                            <label class="cgrey fw300 margbot10">Password</label>
                            <input required type="password" class="input-stretch margbot30" name="password" placeholder="password" />
                            <button type="submit" class="button wide-h-47 btn-wide btnprim no-shadow">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <script src="{{Asset('lib/js/jquery-1.11.3.min.js')}}"></script>
        <script src="{{Asset('lib/js/bootstrap.min.js')}}"></script>
    </body>
</html>