@extends('template.index')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Add new alternative</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Formulir</h3>
                            </div>
                            <div class="card-body">
                                {{-- @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif --}}
                                <form action="{{ route('alternatives.store') }}" method="POST">
                                    @csrf
                                    @if (session('duplicate_entry'))
                                        <div class="alert alert-warning">
                                            {{ session('duplicate_entry') }}
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="name">NIM :</label>
                                        <div class="input-group">
                                            <input id="userId" type="text" class="form-control"
                                                value="{{ $userId }}" name="userId" disabled>
                                        </div>
                                    </div>
                                    @foreach ($criteriaweights as $cw)
                                        <div class="form-group">
                                            <label for="criteria[{{ $cw->id }}]">{{ $cw->name }} :</label>
                                            <select class="form-control" id="criteria[{{ $cw->id }}]"
                                                name="criteria[{{ $cw->id }}]">
                                                <!--
                                                                                                    @php
                                                                                                        $res = $criteriaratings->where('criteria_id', $cw->id)->all();
                                                                                                    @endphp
                                                                                                    -->
                                                @foreach ($res as $cr)
                                                    <option value="{{ $cr->id }}">{{ $cr->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col-md-6 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
