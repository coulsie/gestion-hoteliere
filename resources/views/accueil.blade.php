@extends('layouts.app')

@section('content')
<div class="row justify-content-center text-center mt-5">
    <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-5">
                <i class="fas fa-hotel fa-4x text-primary mb-4"></i>
                <h1 class="h2 text-gray-900 mb-2 font-weight-bold">Bienvenue à l'Hôtel Horizon</h1>
                <p class="mb-4 text-muted">Système de gestion hôtelière en temps réel. Réservez une chambre ou accédez au tableau de bord administratif.</p>

                <div class="row justify-content-center g-3">
                    <div class="col-sm-4">
                        <a href="{{ url('/connexion') }}" class="btn btn-primary btn-block shadow-sm py-2">
                            <i class="fas fa-sign-in-alt mr-2"></i>Connexion Client
                        </a>
                    </div>
                    <div class="col-sm-4">
                        <a href="{{ url('/admin') }}" class="btn btn-warning btn-block shadow-sm py-2 text-dark font-weight-bold">
                            <i class="fas fa-user-shield mr-2"></i>Espace Staff (Filament)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="text-white mb-4 font-weight-bold">Nos Catégories de Chambres disponibles</h3>

        <div class="row text-left">
            @forelse($typesChambres as $type)
                <!-- Génération automatique d'une carte par type de chambre existant en BDD -->
                <div class="col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        {{ $type->name }}
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($type->base_price, 2, ',', ' ') }} FCFA / Nuit
                                    </div>
                                    <p class="text-muted small mt-2">Profitez de notre service d'étage de qualité hôtelière et d'un confort optimal.</p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-bed fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Message d'alerte si aucune catégorie n'est encore créée -->
                <div class="col-12 text-center text-white-50">
                    <p>Aucune catégorie de chambre n'est configurée pour le moment. Connectez-vous à l'espace Staff pour ajouter des offres.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
