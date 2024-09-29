<?php

namespace App\Filament\Resources\ExtractionBucketResource\RelationManagers;

use App\Filament\Forms\BucketFileUpload;
use App\Filament\Forms\ImageColumnWithLoadingIndicator;
use App\Models\ExtractionBucket;
use App\Models\File;
use Mateffy\Magic\Artifacts\ArtifactGenerationStatus;
use Mateffy\Magic\Artifacts\GenerateArtifactJob;
use Filament\Actions\StaticAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilesRelationManager extends RelationManager
{
    protected static string $relationship = 'files';

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->columns(4)
            ->schema(function (File $record) {
                $root = Storage::disk('public')->path('');
                $imagesPath = "{$record->artifact_path}/images";

                $publicRelativePath = str($imagesPath)
                    ->after($root);

                $images = collect(\Illuminate\Support\Facades\File::files($imagesPath))
                    ->filter(fn (\SplFileInfo $file) => in_array(Str::lower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp', 'avif']))
                    ->sortBy(fn (\SplFileInfo $file) => $file->getBasename())
                    ->map(fn (\SplFileInfo $file) => ImageEntry::make($file)
                        ->state(url("storage/{$publicRelativePath}/".Str::afterLast($file, '/')))
                        ->label($file->getBasename())
                        ->height('auto')
                        ->extraImgAttributes([
                            'class' => 'w-full h-full object-contain',
                        ])
                        ->extraAttributes([
                            'class' => 'w-full h-full flex justify-center items-center',
                            'style' => 'aspect-ratio: 1/1',
                        ]),
                    )
                    ->all();

                return array_filter([
                    ($text = $record->artifact?->getText())
                        ? TextEntry::make('name')
                            ->lineClamp(10)
                            ->columnSpanFull()
                            ->state($text)

                        : null,

                    Section::make()
                        ->heading('Images')
                        ->columns(4)
                        ->compact()
                        ->collapsed()
                        ->schema($images),
                ]);
            });
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                BucketFileUpload::make('files')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->poll(fn () => $table
                ->getRecords()
                ->contains(fn (File $record) => $record->artifact_status === ArtifactGenerationStatus::Pending
                    || $record->artifact_status === ArtifactGenerationStatus::InProgress
                ) ? '500ms' : null,
            )
            ->defaultSort('name')
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumnWithLoadingIndicator::make('thumbnail_src')
                    ->label('Preview')
                    ->translateLabel()
                    ->width(200),
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->size('base')
                    ->weight('medium')
                    ->description(fn (File $record) => "{$record->humanReadableSize} • {$record->getTypeFromMime()}"),
                Tables\Columns\IconColumn::make('artifact_status')
                    ->state(false)
                    ->true('heroicon-o-check-circle')
                    ->false('heroicon-o-cog-8-tooth')
                    ->icon(fn (File $record) => $record->artifact_status->getIcon())
                    ->color(fn (File $record) => $record->artifact_status->getColor())
                    ->extraAttributes(fn (File $record) => [
                        'class' => $record->artifact_status === ArtifactGenerationStatus::InProgress ? 'animate-spin' : '',
                        'style' => 'animation-duration: 3s',
                        'wire:poll',
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('add_file')
                    ->label('Upload Files')
                    ->translateLabel()
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->modalSubmitAction(fn (StaticAction $action) => $action
                        ->label('Upload files')
                        ->translateLabel()
                        ->icon('heroicon-o-cloud-arrow-up')
                    )
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->form(fn (Form $form) => self::form($form))
//                    ->model(ExtractionBucket::class)
                    ->record($this->getOwnerRecord())
                    ->action(function (array $data) {
                        foreach ($data['files'] as $file) {
                            $path = Storage::disk('local')->path($file);

                            /** @var ExtractionBucket $record */
                            $record = $this->getOwnerRecord();
                            $record
                                ->addMedia($path)
                                ->toMediaCollection('files');
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make('view')
                    ->icon('heroicon-o-eye')
                    ->slideOver(),
                Tables\Actions\Action::make('regenerateArtifact')
                    ->iconButton()
                    ->label('Reanalyze file')
                    ->translateLabel()
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->tooltip(__('Reanalyze file'))
                    ->modalSubmitAction(fn (StaticAction $action) => $action
                        ->label('Reanalyze file')
                        ->translateLabel()
                        ->icon('heroicon-o-arrow-path-rounded-square')
                    )
                    ->action(function (File $record) {
                        /** @var ExtractionBucket $bucket */
                        $bucket = $this->getOwnerRecord();

                        $record->artifact_status = ArtifactGenerationStatus::Pending;
                        $record->save();

                        GenerateArtifactJob::dispatch($bucket, $record);
                    }),
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->tooltip(__('Download file'))
                    ->iconButton()
                    ->color('gray')
                    ->action(function (File $record) {
                        return response()->download($record->getPath());
                    }),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->tooltip(__('Delete file'))
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('regenerate')
                        ->label('Reanalyze file')
                        ->translateLabel()
                        ->icon('heroicon-o-arrow-path-rounded-square')
                        ->requiresConfirmation()
                        ->modalSubmitAction(fn (StaticAction $action) => $action
                            ->label('Reanalyze file')
                            ->translateLabel()
                            ->icon('heroicon-o-arrow-path-rounded-square')
                        )
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                /** @var ExtractionBucket $bucket */
                                $bucket = $this->getOwnerRecord();

                                $record->artifact_status = ArtifactGenerationStatus::Pending;
                                $record->save();

                                GenerateArtifactJob::dispatch($bucket, $record);
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
