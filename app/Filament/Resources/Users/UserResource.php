<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

// Actions (v4)
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Aluno';
    protected static ?string $pluralModelLabel = 'Alunos';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static string|\UnitEnum|null $navigationGroup = 'Gerenciamento';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados Pessoais')
                ->description('Informações de acesso e identificação do aluno.')
                ->icon('heroicon-m-user')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nome Completo')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('password')
                        ->label('Senha')
                        ->password()
                        // só gera hash se preenchido (evita sobrescrever senha com hash de vazio)
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->columnSpanFull()
                        ->helperText('Deixe em branco para manter a senha atual na edição.'),
                ]),

            Section::make('Configuração Acadêmica')
                ->description('Define o calendário e localização do estudante.')
                ->icon('heroicon-m-calendar-days')
                ->columns(3)
                ->schema([
                    Select::make('estado')
                        ->label('Estado (UF)')
                        ->options(config('estados'))
                        ->searchable()
                        ->preload()
                        ->required(),

                    DatePicker::make('ano_letivo_inicio')
                        ->label('Início das Aulas')
                        ->displayFormat('d/m/Y')
                        ->required(),

                    DatePicker::make('ano_letivo_fim')
                        ->label('Fim das Aulas')
                        ->displayFormat('d/m/Y')
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Aluno')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (User $record): string => (string) $record->email),

                Tables\Columns\TextColumn::make('estado')
                    ->label('UF')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('disciplinas_count')
                    ->counts('disciplinas')
                    ->label('Matérias')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'warning')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Filtrar por Estado')
                    ->options(config('estados'))
                    ->searchable()
                    ->multiple(),
            ])
            // Filament v4: recordActions()
            ->recordActions([
                Action::make('view')
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (User $record) => static::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Nenhum aluno encontrado')
            ->emptyStateDescription('Crie um cadastro para começar a gerenciar.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view'   => Pages\ViewUser::route('/{record}'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
