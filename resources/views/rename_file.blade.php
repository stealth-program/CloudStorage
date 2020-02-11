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
                        <h3>Rename File</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <h5 class="card-title">Old FIle Name: {{basename($file_name)}}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">

                            <div class="col-md-6">
                                <form method="post" action="{{route('files.storerenamed')}}" >
                                    @csrf
                                    <label for="file_url" class="col-form-label text-md-right">New File Name:</label>
                                    <input type="hidden"
                                           name="file_url"
                                           value="{{ $file_name ?? ''.Auth::id() }}">
                                    <input id="file_url"
                                           type="text"
                                           class="form-control"
                                           name="file_rename"
                                           required>

                                    <button class="btn btn-primary m-1">
                                        Rename File
                                    </button>
                                </form>

                            </div>

                        </div>

                        <form action="{{url('home/' . (substr(
                        $file_name, 0, mb_strlen($file_name) - mb_strlen(basename($file_name))
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
