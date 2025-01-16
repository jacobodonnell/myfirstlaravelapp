<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller {

    public function homepage() {
        // imagine we loaded data from the database
        $ourName = 'Jacob';
        $animals = ['Meowsalot', 'Barksalot', 'Purrslour'];

        return view('homepage', ['name' => $ourName, 'animals' => $animals]);
    }

    public function aboutPage() {
        return view('single-post');
    }
}
