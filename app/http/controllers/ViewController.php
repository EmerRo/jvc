<?php

class ViewController extends Controller
{
    public function index(){
        return $this->view('index');
    }
    public function login(){
        return $this->view('login');
    }

}