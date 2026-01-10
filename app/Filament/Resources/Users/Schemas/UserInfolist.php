<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('has_seen_intro')
                    ->boolean(),
                IconEntry::make('has_completed_tour')
                    ->boolean(),
                TextEntry::make('estado')
                    ->placeholder('-'),
                TextEntry::make('ano_letivo_inicio')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('ano_letivo_fim')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('cidade')
                    ->placeholder('-'),
            ]);
    }
}
