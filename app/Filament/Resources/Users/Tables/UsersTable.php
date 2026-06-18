<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn; // 🔥 IMPORTATION DE LA COLONNE DE TEXTE

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Affiche le nom complet et permet de chercher un utilisateur par son nom
                TextColumn::make('name')
                    ->label('Nom complet')
                    ->searchable()
                    ->sortable(),

                // 2. Affiche l'adresse e-mail
                TextColumn::make('email')
                    ->label('Adresse E-mail')
                    ->searchable(),

                // 3. 🔥 AFFICHE LE BADGE DU RÔLE (ex: super_admin, réceptionniste)
                TextColumn::make('roles.name')
                    ->label('Rôles / Profil')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                // Vos filtres si nécessaire
            ]);
    }
}
