<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        Storage::makeDirectory(Auth::id());

        $files = Storage::files(Auth::id() . '/');
        $directories = Storage::directories(Auth::id() . '/');

        /*return view('home',
            ['files' => $files],
            ['directories' => $directories]);*/
        return redirect(url('home/' . Auth::id()));
    }

    public function path($any)
    {
        $pathArray = explode("/",$any);

        $files = Storage::files($any);
        $directories = Storage::directories($any);

        if ($pathArray[0] != Auth::id()) {
            return redirect('home/' . Auth::id())->with('fail', 'Permission denied!');
        }

        if (!Storage::exists($any)) {
            return view('home',
                ['files' => [], 'url' => $any, 'wrongUrl' => 1],
                ['directories' => [] ]
            );
        }

        return view('home',
            ['files' => $files],
            ['directories' => $directories, 'url' => $any, 'wrongUrl' => 0]
        );
    }
}
