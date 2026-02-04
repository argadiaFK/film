<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Backup Files</x-slot>

        @php $backups = $this->getBackupsList(); @endphp

        @if(count($backups) === 0)
            <p class="text-gray-500 text-center py-8">No backups found. Click "Create Backup" to create one.</p>
        @else
            <div class="overflow-x-auto">
                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 dark:divide-gray-700 text-start">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th
                                class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">
                                Filename</th>
                            <th
                                class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">
                                Size</th>
                            <th
                                class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">
                                Date</th>
                            <th
                                class="fi-ta-header-cell px-4 py-3 text-end text-sm font-semibold text-gray-950 dark:text-white">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($backups as $backup)
                            <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="fi-ta-cell px-4 py-3 text-sm text-gray-950 dark:text-white font-medium">
                                    {{ $backup['name'] }}
                                </td>
                                <td class="fi-ta-cell px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $backup['size'] }}
                                </td>
                                <td class="fi-ta-cell px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $backup['date'] }}
                                </td>
                                <td class="fi-ta-cell px-4 py-3 text-end">
                                    <div class="flex justify-end gap-2">
                                        <a href="/admin/backup-manager/download/{{ $backup['name'] }}"
                                            class="fi-btn fi-btn-size-sm inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-semibold bg-success-600 text-white hover:bg-success-500">
                                            Download
                                        </a>
                                        <button type="button" wire:click="deleteBackup('{{ $backup['name'] }}')"
                                            wire:confirm="Delete {{ $backup['name'] }}?"
                                            class="fi-btn fi-btn-size-sm inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-semibold bg-danger-600 text-white hover:bg-danger-500">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>

    <x-filament::section collapsible collapsed>
        <x-slot name="heading">How to Restore</x-slot>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Run this command:</p>
        <code
            class="block p-3 bg-gray-900 text-green-400 text-sm rounded-lg">docker-compose exec app php artisan backup:restore filename.sql</code>
    </x-filament::section>
</x-filament-panels::page>