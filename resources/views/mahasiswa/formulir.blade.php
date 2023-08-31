@extends('template.index')

@section('content')

    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1>General Form</h1>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active">Formulir Data</li>
                                    </ol>
                                </div>
                            </div>
                        </div><!-- /.container-fluid -->
                    </section>

                    <!-- /.card -->
                    <div class="col-md-8">
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Quick Example</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <form action="{{ route('alternatives.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Name :</label>
                                        <div class="input-group">
                                            <input id="name" type="text" class="form-control"
                                                placeholder="Someone or Something" name="name" required>
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
                                    <!-- /.card-body -->

                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                        </div>
                        <!-- /.card -->

                        <!-- Form Element sizes -->

                        <!-- /.card -->

                    </div>
                </div>
                <!-- /.col-md-6 -->

            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>


@endsection
