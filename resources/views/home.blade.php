@extends('layouts.app')

@section('content')
    <passwords :passwords="{{ $passwords }}"></passwords>
@endsection
