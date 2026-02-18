{{-- Wallet Transaction Modal --}}
@props(['user'])

<div id="walletModal" class="hidden relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div
        class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden p-4 sm:p-6 md:p-20">
        <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" aria-hidden="true"
            onclick="closeModal('walletModal')"></div>

        {{-- Modal Content --}}
        <div
            class="relative w-full max-w-md transform rounded-2xl bg-white text-left shadow-2xl transition-all border-t-8 border-green-500">
            <form action="{{ route('wallet.transact') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">

                <div class="bg-white px-8 pt-8 pb-6 rounded-t-xl">
                    <div class="flex items-center gap-3 mb-6 text-green-600">
                        <div class="p-2 bg-green-50 rounded-full"><i class="fas fa-wallet text-xl"></i></div>
                        <h3 class="text-xl font-black text-gray-900">Manage Wallet</h3>
                    </div>

                    {{-- Current Balance Display in Modal --}}
                    <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100 text-center">
                        <p class="text-xs font-bold text-gray-400 uppercase">Current Balance</p>
                        <p class="text-3xl font-black text-gray-900">{{ number_format($user->wallet_balance, 2) }} <span
                                class="text-sm text-gray-400 font-bold">PTS</span></p>
                    </div>

                    {{-- Action Type (Add / Withdraw) --}}
                    <div class="mb-5" x-data="{ type: 'add' }">
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Action</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="add" class="peer hidden" checked
                                    @click="type = 'add'">
                                <div
                                    class="p-3 rounded-xl border-2 border-gray-100 text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition-all hover:bg-gray-50">
                                    <p class="text-sm font-bold text-gray-600 peer-checked:text-green-700"><i
                                            class="fas fa-plus-circle mr-1"></i> Add Points</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="withdraw" class="peer hidden"
                                    @click="type = 'withdraw'">
                                <div
                                    class="p-3 rounded-xl border-2 border-gray-100 text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all hover:bg-gray-50">
                                    <p class="text-sm font-bold text-gray-600 peer-checked:text-red-700"><i
                                            class="fas fa-minus-circle mr-1"></i> Withdraw</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Amount Input --}}
                    <div class="mb-2">
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Amount</label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 font-bold">$</span>
                            <input type="number" name="amount" step="0.01" min="0.01" required
                                class="w-full pl-8 border-2 border-gray-200 rounded-xl p-3 text-lg font-bold focus:ring-green-500 focus:border-green-500 outline-none transition bg-white text-gray-900"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-gray-100 rounded-b-xl">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded-xl font-bold shadow-md transition transform hover:-translate-y-0.5">Confirm</button>
                    <button type="button" onclick="closeModal('walletModal')"
                        class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 py-2 px-6 rounded-xl font-bold shadow-sm transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openWalletModal() {
        document.getElementById('walletModal').classList.remove('hidden');
        document.body.classList.add('modal-open-lock');
    }
    // closeModal is likely already defined in manage-role-modal or global scope, 
    // but just in case, we rely on the one defined in manage-role-modal if present, 
    // or we should ensure it's available globally. 
    // Since both modals might be on the same page, we shouldn't redeclare if it exists.
    if (typeof closeModal !== 'function') {
        window.closeModal = function (id) {
            document.getElementById(id).classList.add('hidden');
            document.body.classList.remove('modal-open-lock');
        }
    }
</script>