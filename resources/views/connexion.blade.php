@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-xl-5 col-lg-6 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="p-5">
                    <div class="text-center">
                        <i class="fas fa-user-lock fa-3x text-primary mb-3"></i>
                        <h1 class="h4 text-gray-900 mb-4 font-weight-bold">Espace Connexion</h1>
                    </div>

                    <!-- MODIFICATION : Redirection vers l'URL /connexion pour la méthode POST -->
                    <form class="user" action="{{ url('/connexion') }}" method="POST">
                        @csrf

                        <!-- MODIFICATION : Affichage dynamique des erreurs de connexion (Identifiants faux) -->
                        @if ($errors->any())
                            <div class="alert alert-danger small py-2 font-weight-bold text-center">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div class="form-group">
                            <!-- MODIFICATION : Ajout de value="{{ old('email') }}" pour éviter de retaper l'email si erreur -->
                            <input type="email" class="form-control form-control-user" id="email" name="email" value="{{ old('email') }}" required placeholder="Votre adresse email...">
                        </div>

                        <div class="form-group">
                            <input type="password" class="form-control form-control-user" id="password" name="password" required placeholder="Mot de passe">
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox small">
                                <!-- MODIFICATION : Ajout de l'attribut name="customCheck" pour le traitement PHP -->
                                <input type="checkbox" class="custom-control-input" id="customCheck" name="customCheck">
                                <label class="custom-control-label" for="customCheck">Se souvenir de moi</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-user btn-block shadow font-weight-bold">
                            Se connecter
                        </button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <a class="small" href="{{ url('/') }}"><i class="fas fa-arrow-left mr-1"></i> Retour à l'accueil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
