@extends('layouts.app')

@section('content')
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <div class="containers">
        @if(session()->get('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
            <br />
        @endif
        @if(session()->get('fail'))
            <div class="alert alert-danger">
                {{ session()->get('fail') }}
            </div>
            <br />
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div><br />
        @endif

        <div class="row">
            <div class="col-md-2">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div class="file-manager">
                            <h4><a href="{{route('home')}}" style="text-decoration: none; color: black;">Home</a></h4>
                            <div class="hr-line-dashed"></div>
                            <div class="card mb-3">
                                <form action="{{route('files.store')}}" method="post" class="m-1" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="file_url" value="{{ $url ?? Auth::id() }}">
                                    <input type="file" name="file" class="form-control-file">
                                    <button class="btn btn-primary btn-block">Upload File</button>
                                </form>
                            </div>
                            <div class="card">
                                <form action="{{route('folders.store')}}" class="m-1" method="post">
                                    @csrf
                                    <div>
                                        <label for="name" class="ml-1">Folder Name:</label>
                                    </div>
                                    <input type="hidden" name="folder_url" value="{{ $url ?? Auth::id() }}">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autocomplete="name">

                                    <button class="btn btn-primary btn-block">Create folder</button>
                                </form>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <h5 class="m-1">Current Folder</h5>
                            <ul class="folder-list" style="padding: 0">
                                <li><a href=""><i class="fa fa-folder"></i> {{ $url ?? Auth::id() }}</a></li>
                            </ul>
                            <form action="{{route('home')}}" class="m-1">
                                <button class="btn btn-primary btn-block">Home Folder</button>
                            </form>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="row">
                    <div class="col-lg-12">

                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th scope="col" width="40%">File Name</th>
                                <th scope="col">File Size</th>
                                <th scope="col">Change Name</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($directories as $number => $directory_name)
                                <tr>
                                    <th>
                                        <a href="{{url('home/' . $directory_name)}}" style="display:block;">
                                            <img width="25px"
                                                 src="{{asset('img/folder.png')}}" alt="">
                                            {{' ' . basename($directory_name)}}
                                        </a>
                                    </th>
                                    <td></td>
                                    <td>
                                        <form action="{{route('folders.rename')}}" class="d-inline" method="post">
                                            <input type="hidden" name="folder_name" value="{{$directory_name}}">
                                            @csrf
                                            <button class="btn btn-info" type="submit">Rename</button>
                                        </form>
                                        <form action="{{route('folders.destroy')}}" class="d-inline" method="post">
                                            @csrf
                                            <input type="hidden" name="folder_name" value="{{$directory_name}}">
                                            <button class="btn btn-danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($files as $number => $file_name)
                                <tr>
                                    <th>
                                        <img width="25px"
                                             src="{{asset('img/file.png')}}" alt="">
                                        {{' ' . basename($file_name)}}
                                    </th>
                                    <td>{{App\Helper::formatSizeUnits(Storage::size($file_name))}}</td>
                                    <td>
                                        <form action="{{route('files.download')}}" class="d-inline" method="post">
                                            <input type="hidden" name="file_name" value="{{$file_name}}">
                                            @csrf
                                            <button class="btn btn-success" type="submit">Download</button>
                                        </form>
                                        <form action="{{route('files.rename')}}" class="d-inline" method="post">
                                            <input type="hidden" name="file_name" value="{{$file_name}}">
                                            @csrf
                                            <button class="btn btn-info" type="submit">Rename</button>
                                        </form>
                                        <form action="{{route('files.destroy')}}" class="d-inline" method="post">
                                            <input type="hidden" name="file_name" value="{{$file_name}}">
                                            @csrf
                                            <input class="btn btn-danger" type="submit" value="delete">
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if(isset($wrongUrl) && $wrongUrl == 1)
                                <tr>
                                    <td>Invalid Path</td>
                                </tr>
                            @elseif(count(array_merge($files, $directories)) == 0 )
                                <tr>
                                    <td>No Files</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>



                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection