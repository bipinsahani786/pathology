<x-landing-layout>
    <x-slot name="title">Report Access Restricted - {{ $invoice->company->name ?? 'Pathology Lab' }}</x-slot>

    <div class="min-h-screen bg-zinc-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 selection:bg-brand-500/10 selection:text-brand-700 font-sans">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            @if($invoice->company && $invoice->company->logo)
                <img class="mx-auto h-16 w-auto object-contain mb-6" src="{{ secure_storage_url($invoice->company->logo) }}" alt="{{ $invoice->company->name }}">
            @else
                <div class="mx-auto h-16 w-16 bg-brand-100 text-brand-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="feather-activity text-3xl"></i>
                </div>
            @endif
            <h2 class="mt-2 text-center text-3xl font-extrabold text-zinc-900 font-display tracking-tight">
                Access Restricted
            </h2>
            <p class="mt-2 text-center text-sm text-zinc-600">
                Invoice #{{ $invoice->invoice_number }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl shadow-zinc-200/50 sm:rounded-[2rem] border border-zinc-100 sm:px-10 text-center relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-red-500"></div>
                
                <div class="w-16 h-16 mx-auto bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-6 border border-red-100">
                    <i class="feather-lock text-2xl"></i>
                </div>

                <h3 class="text-lg font-bold text-zinc-900 mb-2">Payment Pending</h3>
                
                <p class="text-sm text-zinc-600 mb-6 leading-relaxed">
                    Your diagnostic report is ready, but cannot be downloaded yet. Please clear your pending dues to unlock and view your test results.
                </p>

                <div class="bg-zinc-50 rounded-xl p-4 mb-6 border border-zinc-100">
                    <div class="flex justify-between items-center text-sm mb-2">
                        <span class="text-zinc-500 font-medium">Total Bill:</span>
                        <span class="text-zinc-900 font-bold">₹{{ number_format($invoice->net_payable, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm mb-2">
                        <span class="text-zinc-500 font-medium">Paid Amount:</span>
                        <span class="text-emerald-600 font-bold">₹{{ number_format($invoice->paid_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm pt-2 border-t border-zinc-200 mt-2">
                        <span class="text-zinc-900 font-bold">Balance Due:</span>
                        <span class="text-red-600 font-extrabold text-lg">₹{{ number_format($invoice->balance_amount, 2) }}</span>
                    </div>
                </div>

                @if($invoice->company && $invoice->company->phone)
                    <div class="mt-6 text-sm text-zinc-500">
                        Need help? Contact the lab at <br>
                        <a href="tel:{{ $invoice->company->phone }}" class="font-bold text-brand-600 hover:text-brand-500">{{ $invoice->company->phone }}</a>
                    </div>
                @endif
                
                <div class="mt-6">
                    <a href="{{ route('portal.login') }}" class="inline-block w-full text-center px-4 py-3 border border-zinc-300 shadow-sm text-sm font-semibold rounded-xl text-zinc-700 bg-white hover:bg-zinc-50 transition-colors">
                        Go to Patient Portal
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-landing-layout>
