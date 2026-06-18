<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Champ pour le Nom Complet
                TextInput::make('name')
                    ->label('Nom complet')
                    ->required(),

                // 2. Champ pour l'E-mail
                TextInput::make('email')
                    ->label('Adresse E-mail')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                // 3. Champ pour le Mot de passe (Sécurisé et haché automatiquement)
                TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),

                // 4. 🔥 LE SÉLECTEUR DE RÔLES SHIELD (v5 compatible)
                Select::make('roles')
                    ->label('Rôles / Permissions')
                    ->relationship('roles', 'name')
                    ->multiple() // Permet d'attribuer plusieurs rôles si nécessaire
                    ->preload()
                    ->required(),
            ]);
    }
}
