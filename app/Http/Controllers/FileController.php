<?php

namespace App\Http\Controllers;

use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        //
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
            'file'=>'required|max:40000',
            'file_url'=>'required|string',
        ]);

        if(mb_strlen($_FILES['file']['name']) > 40) {
            return back()->with('fail', 'File name is too long!');
        }

        $filePath = Auth::id() . '/';
        $pathIsCorrect = false;
        if (Storage::exists($_POST['file_url'])) {
            $filePath = $_POST['file_url'] . '/';
            $pathIsCorrect = true;
        }

        if (Storage::exists($_POST['file_url'] . '/' . $_FILES['file']['name'])) {
            return redirect('home/' . $_POST['file_url'])->with('fail', 'File Already Exists');
        }

        $request
            ->file('file')
            ->storeAs($filePath, $_FILES['file']['name']);

        if ($pathIsCorrect) {
            return redirect(url('home/' . $filePath))->with('success', 'File uploaded!');
        } else {
            return redirect(url('home/' . $filePath))->with('fail', 'Something went wrong!');
        }
    }

    public function download(Request $request)
    {
        $request->validate([
            'file_name'=>'required|string',
        ]);

        $pathArray = explode("/",\request()->input('file_name'));

        if ($pathArray[0] != Auth::id()) {
            return redirect('home/' . Auth::id())->with('fail', 'Permission denied!');
        }

        return Storage::download(\request()->input('file_name'));
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
            'file_name'=>'required|string',
        ]);

        $pathArray = explode("/",\request()->input('file_name'));

        if ($pathArray[0] != Auth::id()) {
            return redirect('home/' . Auth::id())->with('fail', 'Permission denied!');
        }

        Storage::delete(\request()->input('file_name'));

        return back()->with('success','File is deleted!');
    }

    public function rename(Request $request)
    {
        $request->validate([
            'file_name'=>'required|string',
        ]);

        $pathArray = explode("/",\request()->input('file_name'));

        if ($pathArray[0] != Auth::id()) {
            return redirect('home/' . Auth::id())->with('fail', 'Permission denied!');
        }

        return view('rename_file', ['file_name' => \request()->input('file_name')]);
    }

    public function storeRenamed(Request $request)
    {
        $request->validate([
            'file_url'=>'required|string',
            'file_rename'=>'required|string',
        ]);

        $fileUrl = \request()->input('file_url');
        $fileRename = \request()->input('file_rename');
        $newFileUrlWithoutName =substr($fileUrl,
            0,
            mb_strlen( $fileUrl ) - mb_strlen( basename( $fileUrl ) )
        );
        $newFileUrl = $newFileUrlWithoutName . $fileRename;

        if(mb_strlen($fileRename) > 40) {
            $request->session()->flash('fail', 'File Name Must Be Less Then 40 characters');
            return view('rename_file', ['file_name' => $fileUrl]);
        }

        if (Storage::exists($newFileUrl)) {
            return redirect('home/' . $newFileUrlWithoutName)->with('fail', 'File Already Exists');
        }

        foreach ([
            '  ','/', '\\', '*', "'", '^', ':', ';', '?', '<', '>', '|'
                 ] as $symbol) {
            if (strpos(basename($fileRename), $symbol)) {

                $request->session()->flash('fail', 'Invalid File Name');
                return view('rename_file', ['file_name' => $fileUrl]);

            }
        }

        $pathArray = explode("/", $fileUrl);

        if ($pathArray[0] != Auth::id()) {
            return redirect('home/' . Auth::id())->with('fail', 'Permission denied!');
        }

        Storage::move($fileUrl, $newFileUrl);

        return redirect('home/' . $newFileUrlWithoutName)->with('success','File is renamed!');
    }
}
