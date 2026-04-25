@extends('layouts.Jurassic_Store')

@section('content')
<div class="container-fluid my-5" data-products-page>
    @include('products.partials.catalog-content')
</div>

<script src="{{ asset('js/products-index.js') }}" defer></script>
@endsection
