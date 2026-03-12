@extends('layouts.batu')

@section('content')
    <div class="container mx-auto px-4 py-6 md:py-8" x-data="walletApp()">

        {{-- Page Header --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-ramadan-night dark:text-white font-amiri flex items-center">
                    <i class="fas fa-wallet text-amber-500 dark:text-amber-400 mr-2"></i> 
                    @if($hasManagement) Wallet System @else My Wallet @endif
                </h1>
                <p class="text-gray-500 dark:text-gray-400 text-xs md:text-sm mt-1">
                    @if($hasManagement) Manage student deposits and withdrawals. @else Track your balance and deposit requests. @endif
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if($hasManagement)
                    @if(auth()->user()->hasPermission('deposit_requests'))
                        <button @click="openDepositRequestsList()"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2 relative">
                            <i class="fas fa-list-alt"></i> Deposit Requests
                            @if(isset($pendingCount) && $pendingCount > 0)
                                <span class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white text-[10px] flex items-center justify-center rounded-full border-2 border-white dark:border-gray-800 animate-pulse">
                                    {{ $pendingCount }}
                                </span>
                            @endif
                        </button>
                    @endif

                    @if($hasManagement)
                        <button @click="openModal('bulk')"
                            class="px-5 py-2.5 bg-gray-800 hover:bg-black text-[#FFD700] rounded-xl font-bold text-sm shadow-lg shadow-black/20 transition-all flex items-center gap-2">
                            <i class="fas fa-layer-group"></i> Bulk Operations
                        </button>
                    @endif
                    {{-- Search Box --}}
                    <form @submit.prevent="searchUser()" class="relative w-72 lg:w-96">
                        <input type="search" enterkeyhint="search" x-model="searchQuery"
                            class="w-full pl-10 pr-24 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-amber-500 shadow-sm transition-all placeholder-gray-400"
                            placeholder="Search Academic ID..." :disabled="loading">
                        <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center z-10">
                            <i class="fas fa-search text-gray-400 hover:text-amber-500 transition-colors cursor-pointer"></i>
                        </button>
                        <button type="submit"
                            class="absolute inset-y-1 right-1 px-5 btn-gold text-white rounded-lg text-sm font-bold transition-transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="loading">
                            <i class="fas fa-spinner fa-spin" x-show="loading"></i>
                            <span x-show="!loading">Find</span>
                        </button>
                    </form>
                @else
                    <button onclick="openDepositRequestModal()"
                        class="px-8 py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-lg shadow-xl shadow-amber-500/30 transition-all flex items-center gap-3 transform active:scale-95 hover:-translate-y-1">
                        <i class="fas fa-plus-circle text-2xl"></i> OK. Make Request Deposit
                    </button>
                @endif
            </div>
        </div>

        @if($hasManagement)
            {{-- Management View --}}
            
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
            <div x-show="user" class="mb-8 ramadan-card transform transition-all duration-500"
                x-transition:enter="translate-y-4 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                style="display: none;">

                <div
                    class="p-6 md:p-8 flex flex-col md:flex-row gap-6 md:gap-8 items-center md:items-start border-b border-gray-100 dark:border-gray-700 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900">

                    {{-- Avatar --}}
                    <div class="flex-shrink-0">
                        <div
                            class="w-20 h-20 md:w-24 md:h-24 rounded-full overflow-hidden shadow-inner ring-4 ring-white dark:ring-gray-800">
                            <img :src="user?.avatar" class="w-full h-full object-cover" :alt="user?.name">
                        </div>
                    </div>

                    {{-- User Info --}}
                    <div class="flex-1 text-center md:text-left w-full">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white mb-1" x-text="user?.name">
                        </h2>
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
                            <div
                                class="text-4xl md:text-5xl font-black text-gray-800 dark:text-white font-mono tracking-tight">
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
                class="mb-6 grid grid-cols-1 {{ $hasManagement ? 'md:grid-cols-5' : 'md:grid-cols-4' }} gap-4 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">

                @if($hasManagement)
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Search Member</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full pl-3 pr-10 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm focus:ring-2 focus:ring-blue-500 text-gray-700 dark:text-gray-200"
                            placeholder="Name or ID...">
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                    </div>
                </div>
                @endif

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Transaction
                        Type</label>
                    <div class="relative">
                        <select name="type"
                            class="w-full pl-3 pr-10 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm focus:ring-2 focus:ring-blue-500 appearance-none text-gray-700 dark:text-gray-200">
                            <option value="">All Transactions</option>
                            <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                            <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal
                            </option>
                        </select>
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Date
                        From</label>
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
                        <thead
                            class="bg-gray-50 dark:bg-gray-700/30 text-xs uppercase text-gray-500 font-bold tracking-wider">
                            <tr>
                                <th class="px-4 md:px-6 py-4 whitespace-nowrap">Student</th>
                                <th class="px-4 md:px-6 py-4">Details</th>
                                <th class="px-4 md:px-6 py-4 text-center">Type</th>
                                <th class="px-4 md:px-6 py-4 text-right">Amount</th>
                                <th class="px-4 md:px-6 py-4 text-right">Balance After</th>
                                <th class="px-4 md:px-6 py-4 text-center hidden md:table-cell">Admin</th>
                                <th class="px-4 md:px-6 py-4 text-right whitespace-nowrap">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($transactions as $txn)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors group">
                                    <td class="px-4 md:px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <x-user-avatar :user="$txn->user" size="w-8 h-8 md:w-10 md:h-10" />
                                            <div>
                                                <p
                                                    class="font-bold text-sm text-gray-800 dark:text-gray-200 group-hover:text-blue-600 transition-colors line-clamp-1 max-w-[150px] md:max-w-none">
                                                    {{ $txn->user->name }}
                                                </p>
                                                <p class="text-[10px] md:text-xs text-gray-400 font-mono">
                                                    {{ Str::before($txn->user->email, '@') }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4">
                                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 max-w-[200px] truncate" title="{{ $txn->notes }}">
                                            {{ $txn->notes ?? 'N/A' }}
                                        </p>
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
                                    <td class="px-4 md:px-6 py-4 text-right font-bold text-gray-800 dark:text-gray-200">
                                        {{ $txn->type == 'deposit' ? '+' : '-' }} {{ number_format($txn->amount, 2) }} EGP
                                    </td>
                                    <td
                                        class="px-4 md:px-6 py-4 text-right font-mono font-bold text-sm md:text-base whitespace-nowrap text-gray-600 dark:text-gray-400">
                                        {{ number_format($txn->balance_after ?? 0, 2) }} EGP
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
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-300 dark:text-gray-600">
                                            <i class="fas fa-receipt text-5xl mb-4"></i>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">No transactions found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700">
                            <tr>
                                <td colspan="3"
                                    class="px-4 md:px-6 py-4 text-right font-bold text-gray-500 uppercase tracking-wider text-xs">
                                    Total Members Balance:</td>
                                <td
                                    class="px-4 md:px-6 py-4 text-right font-mono font-black text-lg text-amber-600 dark:text-amber-400">
                                    {{ number_format($totalBalance, 2) }} EGP
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    {{ $transactions->links() }}
                </div>
            </div>

            {{-- Deposit/Withdraw Modal --}}
            <div x-show="modalOpen" class="fixed inset-0 z-[100] flex items-end md:items-center justify-center p-4 sm:p-6"
                style="display: none;">

                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" x-show="modalOpen"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="modalOpen = false">
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-t-3xl md:rounded-2xl shadow-2xl w-full max-w-md relative z-10 p-6 md:p-8 border-t md:border border-gray-200 dark:border-gray-700 transform transition-all"
                    x-show="modalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave="ease-in duration-200"
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
                            <label
                                class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Student</label>
                            <div
                                class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="w-10 h-10 rounded-full overflow-hidden">
                                    <img :src="user?.avatar" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 dark:text-white text-sm" x-text="user?.name"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-mono" x-text="user?.email"></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Amount (EGP)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl font-bold">£</span>
                                <input type="number" name="amount" step="0.01" min="0.1" required autofocus
                                    class="w-full pl-10 pr-4 py-4 text-2xl font-mono font-bold bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 text-gray-800 dark:text-white transition-all placeholder-gray-300"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Notes / Reason</label>
                            <textarea name="notes" rows="2"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-gray-800 dark:text-white text-sm"
                                placeholder="Optional reason for this transaction..."></textarea>
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
        @else
            {{-- Member View --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                {{-- Balance Card --}}
                <div class="md:col-span-1 bg-gradient-to-br from-gray-900 to-gray-800 rounded-[2rem] p-8 text-white shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all duration-700"></div>
                    <div class="relative z-10">
                        <p class="text-amber-500 text-[10px] font-bold tracking-[0.2em] uppercase mb-4">Current Balance</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-5xl font-black tracking-tighter">{{ number_format(auth()->user()->wallet_balance, 2) }}</span>
                            <span class="text-amber-500 font-bold">EGP</span>
                        </div>
                        <div class="mt-8 pt-6 border-t border-white/10 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center">
                                <i class="fas fa-shield-alt text-amber-500 text-sm"></i>
                            </div>
                            <p class="text-[10px] text-gray-400 leading-tight uppercase tracking-wider">Securely stored in your <br> student wallet</p>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions / Stats --}}
                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center text-blue-600">
                            <i class="fas fa-paper-plane text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Deposits</p>
                            <p class="text-xl font-black text-gray-800 dark:text-white">{{ number_format(auth()->user()->walletTransactions()->where('type', 'deposit')->sum('amount'), 2) }} EGP</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 bg-purple-50 dark:bg-purple-900/20 rounded-2xl flex items-center justify-center text-purple-600">
                            <i class="fas fa-shopping-bag text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Spent</p>
                            <p class="text-xl font-black text-gray-800 dark:text-white">{{ number_format(auth()->user()->walletTransactions()->where('type', 'withdrawal')->sum('amount'), 2) }} EGP</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Current Activity / Pending Requests (Only if any) --}}
            @if($myRequests->count() > 0)
                <div class="mb-8 space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                        Pending Activities
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($myRequests as $req)
                            <div class="bg-amber-50/50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/50 rounded-2xl p-4 flex items-center justify-between group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center text-amber-600">
                                        <i class="fas fa-clock animate-spin-slow"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200 capitalize">
                                            {{ str_replace('_', ' ', $req->payment_method) }} Deposit Request
                                        </p>
                                        <p class="text-[10px] text-amber-600/70 font-medium">Waiting for admin review...</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-amber-600">+{{ number_format($req->amount, 2) }} EGP</p>
                                    <p class="text-[10px] text-gray-400">{{ $req->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Unified Transaction History --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-900/30 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-list-ul text-blue-500"></i> Platform History
                    </h3>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                        {{ $transactions->count() }} Records
                    </span>
                </div>

                <div class="overflow-x-auto custom-scroll">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/30 dark:bg-gray-700/10 text-[10px] uppercase text-gray-400 font-black tracking-[0.1em]">
                            <tr>
                                <th class="px-8 py-5">Activity Details</th>
                                <th class="px-6 py-5">Processed By</th>
                                <th class="px-6 py-5 text-right">Amount</th>
                                <th class="px-6 py-5 text-right">Balance</th>
                                <th class="px-8 py-5 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @forelse($transactions as $txn)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/40 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div @class([
                                                'w-10 h-10 rounded-xl flex items-center justify-center text-sm',
                                                'bg-green-50 text-green-600 dark:bg-green-900/20' => $txn->type == 'deposit',
                                                'bg-red-50 text-red-600 dark:bg-red-900/20' => $txn->type == 'withdrawal',
                                            ])>
                                                <i @class([
                                                    'fas fa-plus' => $txn->type == 'deposit',
                                                    'fas fa-minus' => $txn->type == 'withdrawal',
                                                ])></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                                    {{ $txn->notes ?? ($txn->type == 'deposit' ? 'Direct Deposit' : 'Direct Withdrawal') }}
                                                </p>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">
                                                        {{ $txn->type }}
                                                    </span>
                                                    @if($txn->depositRequest && $txn->depositRequest->screenshot_path)
                                                        <a href="{{ $txn->depositRequest->screenshot_path }}" target="_blank" 
                                                           class="text-[10px] font-bold text-blue-500 hover:text-blue-600 flex items-center gap-1 bg-blue-50 dark:bg-blue-900/20 px-2 py-0.5 rounded">
                                                            <i class="fas fa-image"></i> View Receipt
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full overflow-hidden border border-gray-100 dark:border-gray-700">
                                                <img src="{{ $txn->admin->profile_photo_url }}" class="w-full h-full object-cover">
                                            </div>
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400 capitalize">
                                                {{ $txn->admin->name ?? 'System' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td @class([
                                        'px-6 py-5 text-right font-black text-sm',
                                        'text-green-500' => $txn->type == 'deposit',
                                        'text-red-500' => $txn->type == 'withdrawal',
                                    ])>
                                        {{ $txn->type == 'deposit' ? '+' : '-' }}{{ number_format($txn->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-5 text-right font-mono font-bold text-gray-700 dark:text-gray-300">
                                        {{ number_format($txn->balance_after, 2) }}
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <p class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $txn->created_at->format('M d, Y') }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $txn->created_at->format('H:i') }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center justify-center opacity-30">
                                            <i class="fas fa-layer-group text-6xl mb-4"></i>
                                            <p class="text-lg font-bold">No activity history yet</p>
                                            <p class="text-sm">Your platform activities will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Include Wallet Modals (Shared) --}}
        @include('wallet.partials.modals')

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
                },

                openDepositRequestsList() {
                    if (typeof window.openDepositRequestsModal === 'function') {
                        window.openDepositRequestsModal();
                    }
                }
            }
        }
    </script>
@endsection