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
                                Akun / Penjelasan
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Debit
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kredit
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @php
                            $grandTotalDebit = 0;
                            $grandTotalCredit = 0;
                        @endphp
                        @foreach ($journalEntries as $transactionId => $entries)
                            @php
                                $firstEntry = true;
                                $transactionDate = $entries->first()->dt ? \Carbon\Carbon::parse($entries->first()->dt)->format('d M Y') : '-';
                            @endphp
                            @foreach ($entries as $entry)
                                @php
                                    $grandTotalDebit += $entry->debit ?? 0;
                                    $grandTotalCredit += $entry->credit ?? 0;
                                @endphp
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        @if ($firstEntry)
                                            <div class="text-sm text-gray-900">
                                                {{ $transactionDate }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $entry->account->code ?? $entry->account_id }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="text-sm text-gray-900">
                                            {{ $entry->account->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900">
                                            {{ $entry->debit > 0 ? number_format($entry->debit, 2, ',', '.') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900">
                                            {{ $entry->credit > 0 ? number_format($entry->credit, 2, ',', '.') : '-' }}
                                        </div>
                                    </td>
                                </tr>
                                @php
                                    $firstEntry = false;
                                @endphp
                            @endforeach
                            <!-- Explanation row -->
                            <tr class="bg-gray-50 border-b-2 border-gray-300">
                                <td class="px-6 py-2" colspan="2"></td>
                                <td class="px-6 py-2" colspan="3">
                                    <div class="text-xs italic text-gray-600">
                                        <span class="font-medium">Penjelasan:</span>
                                        @php
                                            $transaction = $entries->first()->transaction();
                                            $transactionType = $entries->first()->transaction_name;
                                            $transactionNo = $transaction->no ?? null;
                                        @endphp
                                        @if ($entries->first()->desc)
                                            {{ $entries->first()->desc }}
                                        @else
                                            [{{ date('Y') }}/{{ $transactionType }}/{{ $transactionNo ?? $entries->first()->transaction_id }}] 
                                            @if ($transactionType === 'Order')
                                                Untuk Order #{{ $transactionNo ?? $entries->first()->transaction_id }}
                                            @elseif ($transactionType === 'Payment')
                                                Untuk Payment #{{ $transactionNo ?? $entries->first()->transaction_id }}
                                            @elseif ($transactionType === 'PurchaseOrder')
                                                Untuk Vendor Invoice #{{ $transactionNo ?? $entries->first()->transaction_id }}
                                            @else
                                                Untuk {{ $transactionType }} #{{ $transactionNo ?? $entries->first()->transaction_id }}
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <!-- Empty spacing row -->
                            <tr class="border-b border-gray-100">
                                <td colspan="5" class="py-2"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
    </div>
</x-admin-layout>
