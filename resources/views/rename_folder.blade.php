@extends('layouts.app')

@section('content')
    @if(session()->get('fail'))
        <div class="alert alert-danger">
            {{ session()->get('fail') }}
        </div>
        <br />
    @endif
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Rename Folder</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <h5 class="card-title">Old Folder Name: {{basename($folder_name)}}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">

                            <div class="col-md-6">
                                <form method="post" action="{{route('folders.storerenamed')}}" >
                                    @csrf
                                    <label for="folder_url" class="col-form-label text-md-right">New Folder Name:</label>
                                    <input type="hidden"
                                           name="folder_url"
                                           value="{{ $folder_name ?? ''.Auth::id() }}">
                                    <input id="folder_url"
                                           type="text"
                                           class="form-control"
                                           name="folder_rename"
                                           required>

                                    <button class="btn btn-primary m-1">
                                        Rename Folder
                                    </button>
                                </form>

                            </div>

                        </div>

                        <form action="{{url('home/' . (substr(
                        $folder_name, 0, mb_strlen($folder_name) - mb_strlen(basename($folder_name))
                        ) ?? Auth::id()))}}"
                              method="get">
                            <button type="submit" class="btn btn-secondary">Back</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection