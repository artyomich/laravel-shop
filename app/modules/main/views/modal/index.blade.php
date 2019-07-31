@extends('layouts.' . ($isAjax ? 'post' : 'default'))
@section('content')

    {{ $modal }}

@stop