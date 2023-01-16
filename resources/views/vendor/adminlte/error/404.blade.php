@extends('adminlte::page')

@section('title', '404 Page not found!')

@section('content_header') 
<div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>404 Forbidden Access</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('home.index') }}">@lang('adminlte::menu.dashboard')</a></li>
          <li class="breadcrumb-item active">404 Forbidden Access</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
@stop

@section('content')
<div class="error-page">
    <h2 class="headline text-warning"> 403</h2>

    <div class="error-content">
      <h3><i class="fas fa-exclamation-triangle text-warning"></i> Forbidden Access!</h3>

      <p>
        You didnt have permission to access this page.
        Please go to another page or back to <a href="{{ route('home.index') }}">dashboard</a>.
      </p>
    </div>
    <!-- /.error-content -->
  </div>
  <!-- /.error-page -->
@stop