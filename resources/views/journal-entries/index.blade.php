<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">
                Journal Entries
            </h2>
        </div>
    </x-slot>

    <!-- Filter Card -->
    <div class="card mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Periode</h3>
        <form method="GET" action="{{ route('journal-entries.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-input" required>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    Terapkan
                </button>
                <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Journal Entries Table -->
    <div class="card">
        <div class="overflow-x-auto">
            @if ($journalEntries->count() > 0)
                @foreach ($journalEntries as $transactionId => $entries)
                    <!-- Transaction Group Header -->
                    <div class="bg-gray-100 px-6 py-3 border-b border-gray-300">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-700">
                                Transaction ID: <span class="text-emerald-600">#{{ $transactionId }}</span>
                            </h4>
                            <span class="text-xs text-gray-500">
                                {{ $entries->first()->dt ? \Carbon\Carbon::parse($entries->first()->dt)->format('d M Y') : '-' }}
                            </span>
                        </div>
                        @if ($entries->first()->transaction_name && $entries->first()->transaction_id)
                            <p class="text-xs text-gray-600 mt-1">
                                {{ $entries->first()->transaction_name }} #{{ $entries->first()->transaction_id }}
                            </p>
                        @endif
                    </div>

                    <!-- Transaction Entries Table -->
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode Akun
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Akun
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Debit
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kredit
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $totalDebit = 0;
                                $totalKredit = 0;
                            @endphp
                            @foreach ($entries as $entry)
                                @php
                                    $totalDebit += $entry->debit ?? 0;
                                    $totalKredit += $entry->kredit ?? 0;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $entry->dt ? \Carbon\Carbon::parse($entry->dt)->format('d/m/Y') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $entry->account_id }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ $entry->account->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-medium {{ $entry->debit ? 'text-green-600' : 'text-gray-400' }}">
                                            {{ $entry->debit ? 'Rp ' . number_format($entry->debit, 0, ',', '.') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-medium {{ $entry->kredit ? 'text-red-600' : 'text-gray-400' }}">
                                            {{ $entry->kredit ? 'Rp ' . number_format($entry->kredit, 0, ',', '.') : '-' }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <!-- Transaction Total -->
                        <tfoot class="bg-gray-50">
                            <tr class="font-semibold">
                                <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-700">
                                    Total Transaction #{{ $transactionId }}:
                                </td>
                                <td class="px-6 py-3 text-right text-sm {{ $totalDebit > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $totalDebit > 0 ? 'Rp ' . number_format($totalDebit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-3 text-right text-sm {{ $totalKredit > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $totalKredit > 0 ? 'Rp ' . number_format($totalKredit, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Spacing between transactions -->
                    @if (!$loop->last)
                        <div class="h-4 bg-gray-50"></div>
                    @endif
                @endforeach
            @else
                <!-- Empty State -->
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Tidak ada journal entries dalam periode yang dipilih.
                    </p>
                </div>
            @endif
        </div>

        <!-- Footer with total count -->
        @if ($journalEntries->count() > 0)
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <div class="text-sm text-gray-700">
                    Total Transactions: <span class="font-semibold">{{ $journalEntries->count() }}</span>
                    <span class="text-gray-500 mx-2">|</span>
                    Total Entries: <span class="font-semibold">{{ $journalEntries->flatten()->count() }}</span>
                </div>
            </div>
        @endif
    </div>
</x-admin-layout>
