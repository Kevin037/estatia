<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('general-ledger.index', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                       class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                    </a>
                    <h2 class="text-2xl font-bold text-gray-900">
                        Detail Buku Besar
                    </h2>
                </div>
                <div class="mt-2">
                    <p class="text-sm text-gray-600">
                        <span class="font-semibold text-emerald-600">{{ $account->code }}</span> - {{ $account->name }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="btn btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                    </svg>
                    Print
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Filter Card -->
    <div class="card mb-6 no-print">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Periode</h3>
        <form method="GET" action="{{ route('general-ledger.show', $account->id) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-input">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    Terapkan
                </button>
                <a href="{{ route('general-ledger.show', $account->id) }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No Dokumen
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Penjelasan
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Akun
                        </th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Debit
                        </th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kredit
                        </th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Saldo
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($entriesWithBalance as $entry)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $entry->dt->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $entry->transaction_name }} #{{ $entry->transaction_id }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    JE-{{ str_pad($entry->journal_entry_id, 6, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $entry->desc ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $account->code }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $account->name }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right">
                                @if($entry->debit > 0)
                                    <div class="text-sm font-medium text-emerald-600">
                                        Rp {{ number_format($entry->debit, 0, ',', '.') }}
                                    </div>
                                @else
                                    <div class="text-sm text-gray-400">
                                        -
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right">
                                @if($entry->credit > 0)
                                    <div class="text-sm font-medium text-red-600">
                                        Rp {{ number_format($entry->credit, 0, ',', '.') }}
                                    </div>
                                @else
                                    <div class="text-sm text-gray-400">
                                        -
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold {{ $entry->running_balance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                    Rp {{ number_format($entry->running_balance, 0, ',', '.') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2">Tidak ada transaksi dalam periode ini</p>
                                <p class="text-xs text-gray-400 mt-1">Coba ubah filter tanggal untuk melihat transaksi lainnya</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($entriesWithBalance->count() > 0)
                    <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                        <tr class="font-semibold">
                            <td colspan="4" class="px-4 py-4 text-right text-sm text-gray-900">
                                TOTAL:
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-bold text-emerald-600">
                                    Rp {{ number_format($totalDebit, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-bold text-red-600">
                                    Rp {{ number_format($totalCredit, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-bold {{ $runningBalance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                    Rp {{ number_format($runningBalance, 0, ',', '.') }}
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if($entriesWithBalance->count() > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="text-sm text-gray-700">
                    Total: <span class="font-semibold">{{ $entriesWithBalance->count() }}</span> transaksi
                </div>
            </div>
        @endif
    </div>

    @push('styles')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                font-size: 12px;
            }
            
            .card {
                box-shadow: none;
                border: 1px solid #e5e7eb;
            }
            
            table {
                font-size: 11px;
            }
            
            @page {
                margin: 1cm;
            }
        }
    </style>
    @endpush
</x-admin-layout>
