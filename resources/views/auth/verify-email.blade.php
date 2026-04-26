@extends('layouts.Jurassic_Store')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h1 class="h3 mb-3">Verifica tu correo</h1>

                        <p class="text-muted mb-4">
                            Gracias por registrarte. Antes de continuar, revisa tu correo y confirma tu cuenta con el enlace que te enviamos.
                            Si no recibiste el mensaje, puedes pedir otro desde aqui.
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <div class="alert alert-success" role="alert">
                                Enviamos un nuevo enlace de verificacion al correo asociado a tu cuenta.
                            </div>
                        @endif

                        <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-md-center">
                            <form method="POST" action="{{ route('verification.send') }}" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-warning fw-bold">
                                    Reenviar correo de verificacion
                                </button>
                            </form>

                            <form method="POST" action="{{ route('logout') }}" class="m-0">
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
