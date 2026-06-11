<?php

use App\Http\Controllers\PaymentReceiptController;
use App\Models\Payment;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. Routes Publiques (Visiteurs)
|--------------------------------------------------------------------------
*/

// Page d'accueil avec la liste des types de chambres
Route::get('/', function () {
    $typesChambres = RoomType::all();
    return view('accueil', compact('typesChambres'));
});

/*
|--------------------------------------------------------------------------
| 2. Authentification (Connexion)
|--------------------------------------------------------------------------
*/

// Affichage du formulaire de connexion
Route::get('/connexion', function () {
    return view('connexion');
})->name('login'); // L'alias 'login' est requis par Laravel pour les redirections de sécurité

// Traitement du formulaire de connexion
Route::post('/connexion', function (Request $request) {
    // Validation des données saisies
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // Tentative de connexion de l'utilisateur
    if (Auth::attempt($credentials, $request->has('customCheck'))) {
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    // Retour avec une erreur si les identifiants sont invalides
    return back()->withErrors([
        'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
    ])->onlyInput('email');
});

/*
|--------------------------------------------------------------------------
| 3. Gestion de la Comptabilité & Reçus (Sécurisés par Auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Route de téléchargement depuis l'historique financier de PaymentResource
    Route::get('/admin/payments/{record}/receipt', [PaymentReceiptController::class, 'download'])
        ->name('payment.receipt.download');

    // Nouvelle route pour l'ouverture du reçu en plein écran (Bouton d'encaissement direct)
   

    // Nouvelle route pour l'ouverture du reçu en plein écran
    Route::get('/payments/{payment}/receipt', function (Payment $payment) {
        // On charge la relation pour être sûr qu'elle ne soit pas nulle
        // REMARQUE : Si votre relation s'appelle 'booking' et non 'eventBooking', écrivez 'booking' ci-dessous
        $payment->load('eventBooking.room.roomType');

        return view('pdf.receipt', [
            'payment' => $payment,
            'booking' => $payment->eventBooking ?? $payment->booking,
        ]);
    })->name('payments.receipt');

});
