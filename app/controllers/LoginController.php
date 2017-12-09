<?php

class LoginController extends \BaseController {

    public function showLogin() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            return $this->login();
        }
        return View::make('login')->withPage('login');
    }

    public function login() {
        //rule
        $rule = array(
            'email-parent' => 'required',
            'password' => 'required'
        );
        
        $data = Input::all();
        $validator = Validator::make($data, $rule);
        if ($validator->fails()) {
            return View::make('login')
                            ->withMessages('salahLogin')
                            ->withErrors($validator->messages());
        } else {
            $email = Input::get('email-parent');
            $password = Input::get('password');
            if (Auth::attempt(array('UserEmail' => $email, 'password' => $password), true)) {
                return Redirect::route('showDashboard');
            }
        }
        return View::make('login')
                        ->withMessages('gagalLogin');
    }

    public function showLogout() {
        Auth::logout();
        return Redirect::route('/');
    }

}
