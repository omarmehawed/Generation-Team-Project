@extends('layouts.batu')

@section('content')
    <div class="container mx-auto px-4 py-6 md:py-8" x-data="walletApp()">

        {{-- Page Header --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-ramadan-night dark:text-white font-amiri flex items-center">
                    <i class="fas fa-wallet text-amber-500 dark:text-amber-400 mr-2"></i> Wallet System
                </h1>
                <p class="text-gray-500 dark:text-gray-400 text-xs md:text-sm mt-1">Manage student deposits and withdrawals.
                </p>
            </div>

            {{-- Search Box --}}
            <div class="relative w-full lg:w-96">
                <input type="text" x-model="searchQuery" @keydown.enter="searchUser()"
                    class="w-full pl-10 pr-24 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-amber-500 shadow-sm transition-all placeholder-gray-400"
                    placeholder="Search Academic ID..." :disabled="loading">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <button @click="searchUser()"
                    class="absolute inset-y-1 right-1 px-5 btn-gold text-white rounded-lg text-sm font-bold transition-transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="loading">
                    <i class="fas fa-spinner fa-spin" x-show="loading"></i>
                    <span x-show="!loading">Find</span>
                </button>
            </div>
        </div>

        {{-- Error Message --}}
        <div x-show="errorMessage" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            class="mb-6 p-4 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-500/50 text-red-700 dark:text-red-400 rounded-xl flex items-center gap-3"
            style="display: none;">
            <i class="fas fa-exclamation-circle text-lg"></i>
            <span x-text="errorMessage" class="font-medium"></span>
        </div>

        {{-- User Card (Result) --}}
        <div x-show="user"
            class="mb-8 ramadan-card transform transition-all duration-500"
            x-transition:enter="translate-y-4 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            style="display: none;">

            <div
                class="p-6 md:p-8 flex flex-col md:flex-row gap-6 md:gap-8 items-center md:items-start border-b border-gray-100 dark:border-gray-700 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900">

                {{-- Avatar --}}
                <div class="flex-shrink-0">
                    <div
                        class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-amber-100 dark:bg-gray-700 flex items-center justify-center text-3xl md:text-4xl font-bold text-amber-600 dark:text-amber-400 shadow-inner ring-4 ring-white dark:ring-gray-800">
                        <span x-text="user?.academic_id?.substring(0, 2)"></span>
                    </div>
                </div>

                {{-- User Info --}}
                <div class="flex-1 text-center md:text-left w-full">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white mb-1" x-text="user?.name"></h2>
                    <p class="text-gray-500 dark:text-gray-400 font-mono text-sm mb-4" x-text="user?.email"></p>

                    <div class="flex flex-wrap justify-center md:justify-start gap-2">
                        <span
                            class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300 border border-amber-100 dark:border-amber-800">
                            Student
                        </span>
                        <span
                            class="px-3 py-1 rounded-full text-xs font-bold font-mono bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                            ID: <span x-text="user?.academic_id"></span>
                        </span>
                    </div>
                </div>

                {{-- Balance & Actions --}}
                <div class="flex flex-col items-center md:items-end gap-6 w-full md:w-auto mt-4 md:mt-0">
                    <div class="text-center md:text-right">
                        <p class="text-xs text-gray-400 uppercase font-bold tracking-widest mb-1">Current Balance</p>
                        <div class="text-4xl md:text-5xl font-black text-gray-800 dark:text-white font-mono tracking-tight">
                            <span class="text-green-500 text-2xl md:text-3xl align-top">$</span><span
                                x-text="user?.balance"></span>
                        </div>
                    </div>

                    <div class="flex gap-3 w-full sm:w-auto">
                        <button @click="openModal('deposit')"
                            class="flex-1 sm:flex-none justify-center px-6 py-2.5 bg-green-500 hover:bg-green-600 active:bg-green-700 text-white rounded-xl font-bold shadow-lg shadow-green-500/20 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                            <i class="fas fa-plus"></i> Deposit
                        </button>
                        <button @click="openModal('withdrawal')"
                            class="flex-1 sm:flex-none justify-center px-6 py-2.5 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white rounded-xl font-bold shadow-lg shadow-red-500/20 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                            <i class="fas fa-minus"></i> Withdraw
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('wallet.index') }}"
            class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Transaction
                    Type</label>
                <div class="relative">
                    <select name="type"
                        class="w-full pl-3 pr-10 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm focus:ring-2 focus:ring-blue-500 appearance-none text-gray-700 dark:text-gray-200">
                        <option value="">All Transactions</option>
                        <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                        <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-3 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm focus:ring-2 focus:ring-blue-500 text-gray-700 dark:text-gray-200">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full px-3 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm focus:ring-2 focus:ring-blue-500 text-gray-700 dark:text-gray-200">
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full py-2.5 bg-gray-900 hover:bg-black dark:bg-gray-700 dark:hover:bg-gray-600 text-white rounded-xl font-bold text-sm transition-colors shadow-lg">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
            </div>
        </form>

        {{-- History Table --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
            <div
                class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-history text-blue-500"></i> Transaction History
                </h3>
            </div>

            <div class="overflow-x-auto custom-scroll">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-700/30 text-xs uppercase text-gray-500 font-bold tracking-wider">
                        <tr>
                            <th class="px-4 md:px-6 py-4 whitespace-nowrap">Student</th>
                            <th class="px-4 md:px-6 py-4 text-center">Type</th>
                            <th class="px-4 md:px-6 py-4 text-right">Amount</th>
                            <th class="px-4 md:px-6 py-4 text-center hidden md:table-cell">Admin</th>
                            <th class="px-4 md:px-6 py-4 text-right whitespace-nowrap">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($transactions as $txn)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors group">
                                <td class="px-4 md:px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-xs md:text-sm font-bold text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                            {{ substr($txn->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p
                                                class="font-bold text-sm text-gray-800 dark:text-gray-200 group-hover:text-blue-600 transition-colors line-clamp-1 max-w-[150px] md:max-w-none">
                                                {{ $txn->user->name }}
                                            </p>
                                            <p class="text-[10px] md:text-xs text-gray-400 font-mono">
                                                {{ Str::before($txn->user->email, '@') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-4 text-center">
                                    @if($txn->type == 'deposit')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg text-[10px] md:text-xs font-bold uppercase tracking-wide border border-green-100 dark:border-green-800">
                                            <i class="fas fa-arrow-down mr-1.5"></i> Deposit
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg text-[10px] md:text-xs font-bold uppercase tracking-wide border border-red-100 dark:border-red-800">
                                            <i class="fas fa-arrow-up mr-1.5"></i> Withdraw
                                        </span>
                                    @endif
                                </td>
                                <td
                                    class="px-4 md:px-6 py-4 text-right font-mono font-bold text-sm md:text-base whitespace-nowrap {{ $txn->type == 'deposit' ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                    {{ $txn->type == 'deposit' ? '+' : '-' }} ${{ number_format($txn->amount, 2) }}
                                </td>
                                <td class="px-4 md:px-6 py-4 text-center hidden md:table-cell">
                                    <span
                                        class="bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 px-2 py-1 rounded text-xs border border-gray-200 dark:border-gray-600">
                                        {{ $txn->admin->name ?? 'System' }}
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-4 text-right text-xs text-gray-400 whitespace-nowrap">
                                    {{ $txn->created_at->format('M d, H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-300 dark:text-gray-600">
                                        <i class="fas fa-receipt text-5xl mb-4"></i>
                                        <p class="text-gray-500 dark:text-gray-400 font-medium">No transactions found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                {{ $transactions->links() }}
            </div>
        </div>

        {{-- Modal --}}
        <div x-show="modalOpen" class="fixed inset-0 z-[100] flex items-end md:items-center justify-center p-4 sm:p-6"
            style="display: none;">

            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" x-show="modalOpen"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="modalOpen = false"></div>

            <div class="bg-white dark:bg-gray-800 rounded-t-3xl md:rounded-2xl shadow-2xl w-full max-w-md relative z-10 p-6 md:p-8 border-t md:border border-gray-200 dark:border-gray-700 transform transition-all"
                x-show="modalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 md:scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95">

                <div
                    class="absolute top-3 left-1/2 transform -translate-x-1/2 w-12 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full md:hidden">
                </div>

                <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-white capitalize flex items-center gap-2">
                    <i
                        :class="txnType == 'deposit' ? 'fas fa-plus-circle text-green-500' : 'fas fa-minus-circle text-red-500'"></i>
                    Confirm <span x-text="txnType"></span>
                </h3>

                <form action="{{ route('wallet.transact') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" :value="user?.id">
                    <input type="hidden" name="type" :value="txnType">

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Student</label>
                        <div
                            class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div
                                class="w-10 h-10 rounded-full bg-blue-100 dark:bg-gray-600 flex items-center justify-center text-blue-600 dark:text-blue-300 font-bold">
                                <span x-text="user?.academic_id?.substring(0, 2)"></span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 dark:text-white text-sm" x-text="user?.name"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono" x-text="user?.email"></p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Amount
                            ($)</label>
                        <div class="relative">
                            <span
                                class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl font-bold">$</span>
                            <input type="number" name="amount" step="0.01" min="0.1" required autofocus
                                class="w-full pl-10 pr-4 py-4 text-2xl font-mono font-bold bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 text-gray-800 dark:text-white transition-all placeholder-gray-300"
                                placeholder="0.00">
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="button" @click="modalOpen = false"
                            class="flex-1 py-3.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 py-3.5 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 transition-transform active:scale-95"
                            :class="txnType == 'deposit' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'">
                            Confirm Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function walletApp() {
            return {
                searchQuery: '',
                user: null,
                loading: false,
                errorMessage: null,
                modalOpen: false,
                txnType: 'deposit',

                async searchUser() {
                    if (!this.searchQuery) return;

                    this.loading = true;
                    this.errorMessage = null;
                    this.user = null;

                    try {
                        const response = await fetch(`{{ route('wallet.search') }}?academic_id=${this.searchQuery}`);
                        if (!response.ok) throw new Error('User not found');

                        this.user = await response.json();
                    } catch (error) {
                        this.errorMessage = "User not found with that Academic ID.";
                    } finally {
                        this.loading = false;
                    }
                },

                openModal(type) {
                    this.txnType = type;
                    this.modalOpen = true;
                }
            }
        }
    </script>
@endsection