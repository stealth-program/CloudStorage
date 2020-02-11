<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'name'=>'required|string|max:40',
            'folder_url'=>'required|string',
        ]);

        if(mb_strlen($_POST['name']) > 40) {
            return back()->with('fail', 'Folder name is too long!');
        }

        foreach ([
                     '  ','/', '\\', '*', "'", '^', ':', ';', '?', '<', '>', '|'
                 ] as $symbol) {
            if (strpos(basename($_POST['name']), $symbol)) {
                return back()->with('fail', 'Invalid Folder Name');

            }
        }

        $folderPath = Auth::id() . '/';
        $pathIsCorrect = false;

        if (Storage::exists($_POST['folder_url'])) {
            $folderPath = $_POST['folder_url'] . '/';
            $pathIsCorrect = true;
        }

        if (Storage::exists($folderPath . $request->input('name'))) {
            return back()->with('fail', 'Folder Already Exists');
        }

        Storage::makeDirectory($folderPath . $request->input('name'));

        if ($pathIsCorrect) {
            return redirect(url('home/' . $folderPath))->with('success', 'Folder uploaded!');
        }

        return redirect('home')->with('success', 'Folder Created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'folder_name'=>'required|string',
        ]);

        $pathArray = explode("/",\request()->input('folder_name'));

        if ($pathArray[0] != Auth::id()) {
            return redirect('home/' . Auth::id())->with('fail', 'Permission denied!');
        }

        Storage::deleteDirectory(\request()->input('folder_name'));

        return back()->with('success','Folder is deleted!');
    }

    public function rename(Request $request)
    {
        $request->validate([
            'folder_name'=>'required|string',
        ]);

        $pathArray = explode("/",\request()->input('folder_name'));

        if ($pathArray[0] != Auth::id()) {
            return redirect('home/' . Auth::id())->with('fail', 'Permission denied!');
        }

        return view('rename_folder', ['folder_name' => \request()->input('folder_name')]);
    }

    public function storeRenamed(Request $request)
    {
        $request->validate([
            'folder_url'=>'required|string',
            'folder_rename'=>'required|string',
        ]);

        $folderUrl = \request()->input('folder_url');
        $folderRename = \request()->input('folder_rename');
        $newFolderUrlWithoutName = substr($folderUrl,
            0,
            mb_strlen( $folderUrl ) - mb_strlen( basename( $folderUrl ) )
        );
        $newFolderUrl = $newFolderUrlWithoutName . $folderRename;

        if(mb_strlen($folderRename) > 40) {
            $request->session()->flash('fail', 'Folder Name Must Be Less Then 40 characters');
            return view('rename_folder', ['folder_name' => $folderUrl]);
        }

        if (Storage::exists($newFolderUrl)) {
            return redirect('home/' . $newFolderUrlWithoutName)->with('fail', 'Folder Already Exists');
        }

        foreach ([
                     '  ','/', '\\', '*', "'", '^', ':', ';', '?', '<', '>', '|'
                 ] as $symbol) {
            if (strpos(basename($folderRename), $symbol)) {
                $request->session()->flash('fail', 'Invalid Folder Name');
                return view('rename_folder', ['folder_name' => $folderUrl]);

            }
        }

        $pathArray = explode("/", $folderUrl);

        if ($pathArray[0] != Auth::id()) {
            return redirect('home/' . Auth::id())->with('fail', 'Permission denied!');
        }

        Storage::move($folderUrl, $newFolderUrl);

        return redirect('home/' . $newFolderUrlWithoutName)->with('success','Folder is renamed!');
    }
}
