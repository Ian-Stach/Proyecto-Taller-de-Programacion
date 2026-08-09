@extends('layouts.Jurassic_Store')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-7 col-xl-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h1 class="h3 mb-3">Verifica tu correo</h1>
                        <p class="text-muted mb-4">
                            Antes de continuar, revisa tu bandeja de entrada y haz clic en el enlace de verificación que te enviamos al registrarte.
                            Si no lo encuentras, también revisa la carpeta de spam.
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <div class="alert alert-success mb-4" role="alert">
                                Te enviamos un nuevo enlace de verificación a tu correo.
                            </div>
                        @endif

                        <div class="d-flex flex-column flex-sm-row gap-3">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-warning fw-bold">
                                    Reenviar correo de verificación
                                </button>
                            </form>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
