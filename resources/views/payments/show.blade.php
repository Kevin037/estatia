<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">
                Payment Details: {{ $payment->no }}
            </h2>
            <div class="flex gap-x-3">
                <a href="{{ route('payments.pdf', $payment->id) }}" target="_blank" class="btn bg-purple-600 hover:bg-purple-700 text-white">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Information Card -->
            <div class="card">
                <div class="flex items-center justify-between mb-6 pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Payment Information</h3>
                    <div class="flex gap-x-2">
                        <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-sm bg-emerald-600 hover:bg-emerald-700 text-white">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Edit
                        </a>
                        <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm bg-red-600 hover:bg-red-700 text-white">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Payment Number</label>
                        <p class="mt-1 text-base text-gray-900">{{ $payment->no }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Payment Date</label>
                        <p class="mt-1 text-base text-gray-900">{{ $payment->dt?->format('d F Y') ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Payment Method</label>
                        <p class="mt-1">
                            @if($payment->payment_type === 'cash')
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-1 text-sm font-medium text-green-700">Cash</span>
                            @elseif($payment->payment_type === 'transfer')
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-sm font-medium text-blue-700">Bank Transfer</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2.5 py-1 text-sm font-medium text-gray-700">{{ ucfirst($payment->payment_type) }}</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Payment Amount</label>
                        <p class="mt-1 text-lg font-bold text-emerald-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Paid At</label>
                        <p class="mt-1 text-base text-gray-900">{{ $payment->paid_at?->format('d F Y H:i') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Bank Details Card (if transfer) -->
            @if($payment->payment_type === 'transfer' && $payment->bank_account_id)
                <div class="card">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b">Bank Transfer Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Account Number</label>
                            <p class="mt-1 text-base text-gray-900 font-mono">{{ $payment->bank_account_id }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Bank Name</label>
                            <p class="mt-1 text-base text-gray-900">{{ $payment->bank_account_type ?? 'N/A' }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-700">Account Name</label>
                            <p class="mt-1 text-base text-gray-900">{{ $payment->bank_account_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Invoice Information Card -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b">Invoice Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Invoice Number</label>
                        <p class="mt-1">
                            <a href="{{ route('invoices.show', $payment->invoice_id) }}" class="text-emerald-600 hover:text-emerald-700 font-medium hover:underline">
                                {{ $payment->invoice->no }}
                            </a>
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Invoice Date</label>
                        <p class="mt-1 text-base text-gray-900">{{ $payment->invoice->dt?->format('d F Y') ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Order Number</label>
                        <p class="mt-1">
                            <a href="{{ route('orders.show', $payment->invoice->order_id) }}" class="text-emerald-600 hover:text-emerald-700 font-medium hover:underline">
                                {{ $payment->invoice->order->no }}
                            </a>
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Payment Status</label>
                        <p class="mt-1">
                            @php
                                $totalPaid = $payment->invoice->payments->sum('amount');
                                $totalAmount = $payment->invoice->order->total;
                                $isPaid = $totalPaid >= $totalAmount;
                            @endphp
                            @if($isPaid)
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-1 text-sm font-medium text-green-700">Paid</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2.5 py-1 text-sm font-medium text-yellow-700">Pending</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Customer Information Card -->
            @if($payment->invoice->order->customer)
                <div class="card">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b">Customer Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Customer Name</label>
                            <p class="mt-1 text-base text-gray-900">{{ $payment->invoice->order->customer->name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-base text-gray-900">{{ $payment->invoice->order->customer->email ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Phone</label>
                            <p class="mt-1 text-base text-gray-900">{{ $payment->invoice->order->customer->phone ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Address</label>
                            <p class="mt-1 text-base text-gray-900">{{ $payment->invoice->order->customer->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Property Details Card -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b">Property Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($payment->invoice->order->project)
                        <div>
                            <label class="text-sm font-medium text-gray-700">Project</label>
                            <p class="mt-1 text-base text-gray-900">{{ $payment->invoice->order->project->name }}</p>
                        </div>
                    @endif

                    @if($payment->invoice->order->cluster)
                        <div>
                            <label class="text-sm font-medium text-gray-700">Cluster</label>
                            <p class="mt-1 text-base text-gray-900">{{ $payment->invoice->order->cluster->name }}</p>
                        </div>
                    @endif

                    @if($payment->invoice->order->unit)
                        <div>
                            <label class="text-sm font-medium text-gray-700">Unit Number</label>
                            <p class="mt-1 text-base text-gray-900">{{ $payment->invoice->order->unit->no }}</p>
                        </div>

                        @if($payment->invoice->order->unit->product)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Product Type</label>
                                <p class="mt-1 text-base text-gray-900">{{ $payment->invoice->order->unit->product->name }}</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Summary Sidebar (1/3 width) -->
        <div class="lg:col-span-1">
            <div class="card sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b">Payment Summary</h3>

                <div class="space-y-4">
                    <!-- Order Total -->
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Order Total</span>
                        <span class="text-base font-bold text-gray-900">
                            Rp {{ number_format($payment->invoice->order->total, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Total Paid -->
                    @php
                        $totalPaid = $payment->invoice->payments->sum('amount');
                    @endphp
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Paid</span>
                        <span class="text-base font-semibold text-emerald-600">
                            Rp {{ number_format($totalPaid, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Remaining Balance -->
                    @php
                        $remaining = $payment->invoice->order->total - $totalPaid;
                    @endphp
                    <div class="flex justify-between items-center pt-4 border-t">
                        <span class="text-base font-medium text-gray-900">Remaining</span>
                        <span class="text-lg font-bold {{ $remaining > 0 ? 'text-red-600' : 'text-green-600' }}">
                            Rp {{ number_format($remaining, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Payment History -->
                @if($payment->invoice->payments->count() > 0)
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Payment History</h4>
                        <div class="space-y-2">
                            @foreach($payment->invoice->payments as $p)
                                <div class="flex justify-between items-center text-sm {{ $p->id === $payment->id ? 'bg-emerald-50 -mx-3 px-3 py-2 rounded' : '' }}">
                                    <div>
                                        <p class="font-medium {{ $p->id === $payment->id ? 'text-emerald-700' : 'text-gray-900' }}">
                                            {{ $p->no }}
                                            @if($p->id === $payment->id)
                                                <span class="text-xs">(Current)</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $p->dt?->format('d M Y') }}</p>
                                    </div>
                                    <span class="font-semibold {{ $p->id === $payment->id ? 'text-emerald-700' : 'text-gray-900' }}">
                                        Rp {{ number_format($p->amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Delete confirmation with SweetAlert2
        document.querySelector('.delete-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Delete Payment?',
                html: 'This action cannot be undone.<br><strong class="text-red-600">This will restore the product stock quantity!</strong>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>
