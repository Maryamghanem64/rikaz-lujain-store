@extends('layouts.admin')

@section('title', 'لوحة الإدارة')

@section('content')
    <h2>لوحة الإدارة</h2>

    <p>
        مرحبًا {{ auth()->user()->name }}
    </p>
@endsection