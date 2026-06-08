<?php




use Illuminate\Support\Facades\Route;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Affiche la page d'accueil
Route::get('/', function () {
    $typesChambres = RoomType::all();
    return view('accueil', compact('typesChambres'));
});

// Affiche le formulaire de connexion (GET)
Route::get('/connexion', function () {
    return view('connexion');
})->name('login'); // L'alias 'login' est requis par Laravel

// Traite la soumission du formulaire de connexion (POST)
Route::post('/connexion', function (Request $request) {
    // 1. Validation des champs de saisie
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // 2. Tentative de connexion de l'utilisateur
    if (Auth::attempt($credentials, $request->has('customCheck'))) {
        $request->session()->regenerate();

        // Redirige vers l'accueil ou l'espace client
        return redirect()->intended('/');
    }

    // 3. Retour en arrière avec une erreur si les identifiants sont faux
    return back()->withErrors([
        'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
    ])->onlyInput('email');
});
