@extends('laravel-admin-backup::iframe.index', ['header' => $header])

@section('content')
    <section class="content">

        @include('admin::partials.alerts')
        @include('admin::partials.exception')
        @include('admin::partials.toastr')

        {!! $content !!}

    </section>
@endsection