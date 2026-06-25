<?php

use App\Filament\Pages\RapportFinancier;
use App\Http\Controllers\PaymentReceiptController;
use App\Models\Payment;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // 🔥 IMPORTATION REQUISE POUR LES OBJETS DE DATES
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
| 2. Authentification (Connexion / Déconnexion)
|--------------------------------------------------------------------------
*/

// Affichage du formulaire de connexion
Route::get('/connexion', function () {
    return view('connexion');
})->name('login');

// Traitement du formulaire de connexion
Route::post('/connexion', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->has('customCheck'))) {
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    return back()->withErrors([
        'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
    ])->onlyInput('email');
});

/*
|--------------------------------------------------------------------------
| 3. Zone Sécurisée (Authentifiés uniquement)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /* --- 3.1 Gestion des Reçus & Paiements --- */

    // Téléchargement depuis l'historique financier de PaymentResource
    Route::get('/admin/payments/{record}/receipt', [PaymentReceiptController::class, 'download'])
        ->name('payment.receipt.download');

    // Visualisation du reçu au format standard / PDF
    Route::get('/payments/{payment}/receipt', function (Payment $payment) {
        $payment->load(['eventBooking.room.roomType', 'booking']);

        return view('pdf.receipt', [
            'payment' => $payment,
            'booking' => $payment->eventBooking ?? $payment->booking,
        ]);
    })->name('payments.receipt');

    // Impression Directe (Intercepte le clic du bouton d'impression Filament)
    Route::get('/admin/receipt/{record}/print', function ($record) {
        $recu = Payment::findOrFail($record);
        return view('print.receipt', compact('recu'));
    })->name('receipt.print');

    /* --- 3.2 Rapports Administratifs --- */

    // 🔥 RECTIFICATION SCELLÉE : Capture les query-parameters réactifs envoyés par le bouton vert
    Route::get('/admin/rapport-financier/print', function (Request $request) {
        // Sécurité d'accès additionnelle : réservé au super_admin
        if (! Auth::user()?->hasRole('super_admin')) {
            abort(403);
        }

        // 💡 CAPTURE STRICTE : Reçoit les dates ISO de l'URL, ou applique le mois en cours par défaut
        $dateDebut = $request->query('debut', Carbon::now()->startOfMonth()->toDateString());
        $dateFin = $request->query('fin', Carbon::now()->endOfMonth()->toDateString());

        // Instanciation de la page pour appeler la méthode de calcul
        $page = new RapportFinancier();

        // 🔥 FORCE L'ALIGNEMENT V5 : Alimente le tableau d'état $data pour getRecettesProperty()
        $page->data = [
            'date_debut' => $dateDebut,
            'date_fin'   => $dateFin,
        ];

        // Exécute le calcul SQL étanche sur la base de données MariaDB
        $data = $page->getRecettesProperty();

        // Renvoie les données recalculées à votre fiche de mise en page blanche
        return view('print.rapport-financier', compact('data', 'dateDebut', 'dateFin'));
    })->name('admin.rapport.print');

});
