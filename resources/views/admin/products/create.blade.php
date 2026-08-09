@extends('admin.layout')

@section('title', 'Nuevo producto')

@section('content')
    <div class="d-flex align-items-center gap-3 mb-4">
        <a class="text-muted text-decoration-none"
           href="{{ route('admin.products') }}"
        >← Productos</a>
        <h2 class="mb-0">Nuevo producto</h2>
    </div>

    <div class="card shadow-sm p-4">
        @include('admin.products.partials.form', [
            'formAction' => route('admin.products.store'),
            'formMethod' => 'POST',
        ])
    </div>
@endsection
