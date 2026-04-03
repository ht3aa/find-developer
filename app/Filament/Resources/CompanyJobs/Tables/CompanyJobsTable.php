<?php

namespace App\Filament\Resources\CompanyJobs\Tables;

use App\Enums\JobStatus;
use App\Jobs\ProvisionCompanyJobGiteaJob;
use App\Models\CompanyJob;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompanyJobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('company_name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                IconColumn::make('first_payment_qi_confirmed')
                    ->label('Qi paid')
                    ->boolean(),
                TextColumn::make('gitea_owner')
                    ->label('Gitea owner')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gitea_repo_name')
                    ->label('Repo')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gitea_provisioned_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('confirmQiPayment')
                    ->label('Confirm payment')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->color('gray')
                    ->visible(fn (CompanyJob $record): bool => $record->status === JobStatus::PENDING
                        && ! $record->first_payment_qi_confirmed)
                    ->requiresConfirmation()
                    ->modalHeading('Confirm first payment received')
                    ->modalDescription('Confirm that the minimum first payment was sent to the Qi Card number shown in the page subtitle.')
                    ->action(fn (CompanyJob $record): bool => $record->update([
                        'first_payment_qi_confirmed' => true,
                    ])),
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (CompanyJob $record): bool => $record->status === JobStatus::PENDING)
                    ->disabled(fn (CompanyJob $record): bool => ! $record->first_payment_qi_confirmed)
                    ->tooltip(fn (CompanyJob $record): ?string => $record->first_payment_qi_confirmed
                        ? null
                        : 'Confirm Qi Card payment first.')
                    ->requiresConfirmation()
                    ->modalHeading('Approve remote work post')
                    ->modalDescription('Approves the post and provisions a private Gitea repository for the owner.')
                    ->action(function (CompanyJob $record): void {
                        $record->update(['status' => JobStatus::APPROVED]);
                        ProvisionCompanyJobGiteaJob::dispatch($record->id);
                    })
                    ->successNotificationTitle('Post approved and Gitea provisioning queued'),
                Action::make('reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (CompanyJob $record): bool => $record->status === JobStatus::PENDING)
                    ->requiresConfirmation()
                    ->action(fn (CompanyJob $record): bool => $record->update([
                        'status' => JobStatus::REJECTED,
                    ]))
                    ->successNotificationTitle('Post rejected'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
