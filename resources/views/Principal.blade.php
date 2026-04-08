<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>JURASSIC STORE</title>
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
        <style>
            .navbar-short {
                min-height: 50px;
                padding: 10px 0;
            }
            .navbar-tall {
                min-height: 80px;
                padding: 20px 0;
            }
            .dropdown-toggle-custom:hover {
                border: 2px solid black !important;
            }
            .dropdown-toggle-custom::after {
                content: '▼';
                border: none;
                margin-left: 8px;
                font-size: 0.6rem;
            }
        </style>
    <head>
    
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-black navbar-tall">
            <div class="container-fluid">
                <div class="d-flex gap-3 align-items-center">
                    <img src="{{ asset('images/jp_logo.jpg') }}" alt="logo" width="60" height="40" class="d-inline-block align-text-top">
                    <a class="navbar-brand" href="/Principal">
                        Jurassic Store
                    </a>
                </div>

                <ul class="nav justify-content-end">
                    <li class="nav-item">
                        <a class="nav-link" href="/Favorites">Favorites</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/register">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/cart">Cart</a>
                    </li>
                </ul>
            </div>
        </nav>

        <nav class="navbar navbar-expand-lg navbar-dark bg-warning navbar-short">
            <div class="container-fluid">
                <div class="d-flex gap-3 align-items-center">
                    <a class="nav-link" href="/Principal">Home</a>
                    <div class="dropdown">
                        <button class="btn btn-warning dropdown-toggle dropdown-toggle-custom" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Categories
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/products">All Products</a></li>
                            <li><a class="dropdown-item" href="/products?category=action-figures">Action Figures</a></li>
                            <li><a class="dropdown-item" href="/products?category=plush-toys">Plush Toys</a></li>
                            <li><a class="dropdown-item" href="/products?category=apparel">Apparel</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <h1>Welcome to JURASSIC STORE</h1>
        <p>Here you can find the best dinosaur toys and merchandise!</p>
        <a href="/products">View Products</a>

        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <body>
<html>