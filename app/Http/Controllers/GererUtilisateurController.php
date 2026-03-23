<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\View\View;
class GererUtilisateurController extends Controller
{
    public function index(): View
    {
        return view('admin.gererUtilisateur');
    }

}
