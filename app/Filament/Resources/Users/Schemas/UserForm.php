<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Toggle::make('has_seen_intro')
                    ->required(),
                Toggle::make('has_completed_tour')
                    ->required(),
                TextInput::make('estado'),
                DatePicker::make('ano_letivo_inicio'),
                DatePicker::make('ano_letivo_fim'),
                TextInput::make('cidade'),
            ]);
    }
}
