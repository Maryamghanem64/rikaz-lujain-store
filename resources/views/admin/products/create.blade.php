@extends('layouts.admin')

@section('title', 'إضافة منتج')

@section('content')

    <h2>إضافة منتج جديد</h2>

    @if ($errors->any())
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.products.store') }}"
    >
        @csrf

        @include('admin.products._form')

        <button type="submit">
            حفظ المنتج
        </button>
    </form>

@endsection