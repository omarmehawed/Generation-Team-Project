{{-- Deposit Request Modal (For Regular Members) --}}
<div id="depositRequestModal" class="fixed inset-0 z-[100] flex items-end md:items-center justify-center p-4 sm:p-6"
    x-cloak style="display: none;" x-data="depositRequestApp()">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeDepositRequestModal()">
    </div>

    <div
        class="bg-white dark:bg-gray-800 rounded-t-3xl md:rounded-2xl shadow-2xl w-full max-w-lg relative z-10 p-6 md:p-8 border-t md:border border-gray-200 dark:border-gray-700 transform transition-all">
        <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-white flex items-center gap-2">
            <i class="fas fa-hand-holding-usd text-amber-500"></i> Request Wallet Deposit
        </h3>

        <form action="{{ route('wallet.request.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Payment
                    Method</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="cash" class="peer hidden" checked
                            x-model="method">
                        <div
                            class="p-3 rounded-xl border-2 border-gray-100 dark:border-gray-700 text-center peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 transition-all group-hover:bg-gray-50 dark:group-hover:bg-gray-700/50">
                            <i
                                class="fas fa-money-bill-wave text-lg mb-1 block text-gray-400 group-hover:text-amber-500 peer-checked:text-amber-500"></i>
                            <p
                                class="text-[10px] font-bold text-gray-600 dark:text-gray-400 peer-checked:text-amber-600 dark:peer-checked:text-amber-400">
                                Cash</p>
                        </div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="vodafone_cash" class="peer hidden"
                            x-model="method">
                        <div
                            class="p-3 rounded-xl border-2 border-gray-100 dark:border-gray-700 text-center peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 transition-all group-hover:bg-gray-50 dark:group-hover:bg-gray-700/50">
                            <i
                                class="fas fa-mobile-alt text-lg mb-1 block text-gray-400 group-hover:text-red-500 peer-checked:text-red-500"></i>
                            <p
                                class="text-[10px] font-bold text-gray-600 dark:text-gray-400 peer-checked:text-red-600 dark:peer-checked:text-red-400">
                                V. Cash</p>
                        </div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="instapay" class="peer hidden" x-model="method">
                        <div
                            class="p-3 rounded-xl border-2 border-gray-100 dark:border-gray-700 text-center peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 transition-all group-hover:bg-gray-50 dark:group-hover:bg-gray-700/50">
                            <i
                                class="fas fa-university text-lg mb-1 block text-gray-400 group-hover:text-purple-500 peer-checked:text-purple-500"></i>
                            <p
                                class="text-[10px] font-bold text-gray-600 dark:text-gray-400 peer-checked:text-purple-600 dark:peer-checked:text-purple-400">
                                InstaPay</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Amount (EGP)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold">£</span>
                    <input type="number" name="amount" step="0.01" min="1" required
                        class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-white"
                        placeholder="0.00">
                </div>
            </div>

            {{-- Cash Specific --}}
            <div x-show="method === 'cash'" class="mb-5">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Notes</label>
                <textarea name="notes" rows="2"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-white text-sm"
                    placeholder="e.g., I handed the money to the leader at university."></textarea>
            </div>

            {{-- Transfer Specific --}}
            <div x-show="method !== 'cash'" class="space-y-4 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Transfer Phone
                        Number</label>
                    <input type="text" name="phone_number" :required="method !== 'cash'"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-white text-sm"
                        placeholder="01XXXXXXXXX">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Transfer
                            Date</label>
                        <input type="date" name="transfer_date" :required="method !== 'cash'"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Transfer
                            Time</label>
                        <input type="time" name="transfer_time" :required="method !== 'cash'"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-white text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Upload
                        Screenshot</label>
                    <input type="file" name="screenshot" :required="method !== 'cash'"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button" onclick="closeDepositRequestModal()"
                    class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm transition-colors">Cancel</button>
                <button type="submit"
                    class="flex-1 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-amber-500/20 transition-transform active:scale-95">Submit
                    Request</button>
            </div>
        </form>
    </div>
</div>

{{-- Deposit Requests List Modal (For Leaders) --}}
<div id="depositRequestsListModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-cloak
    style="display: none;" x-data="depositRequestsListApp()">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeRequestsListModal()">
    </div>

    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl relative z-10 overflow-hidden border border-gray-200 dark:border-gray-700 max-h-[90vh] flex flex-col">
        <div
            class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-list-alt text-indigo-500"></i> Pending Deposit Requests
            </h3>
            <button onclick="closeRequestsListModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="overflow-y-auto flex-1 p-6">
            <template x-if="loading">
                <div class="flex justify-center p-12">
                    <i class="fas fa-spinner fa-spin text-4xl text-indigo-500"></i>
                </div>
            </template>

            <template x-if="!loading && requests.length === 0">
                <div class="text-center p-12 text-gray-400">
                    <i class="fas fa-check-circle text-5xl mb-4"></i>
                    <p class="font-bold">No pending requests!</p>
                </div>
            </template>

            <template x-if="!loading && requests.length > 0">
                <div class="space-y-4">
                    <template x-for="req in requests" :key="req.id">
                        <div
                            class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-2xl border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex items-center gap-4 w-full md:w-auto">
                                <div
                                    class="w-12 h-12 rounded-full overflow-hidden border-2 border-white dark:border-gray-800 shadow-sm">
                                    <img :src="req.user.profile_photo_url" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 dark:text-white" x-text="req.user.name"></p>
                                    <p class="text-xs text-gray-400 font-mono" x-text="req.user.email"></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-8 w-full md:w-auto justify-between md:justify-end">
                                <div class="text-center md:text-right">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Amount</p>
                                    <p class="text-lg font-black text-amber-600 dark:text-amber-400"
                                        x-text="req.amount + ' EGP'"></p>
                                </div>
                                <div class="text-center md:text-right">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Method</p>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider"
                                        :class="req.payment_method === 'cash' ? 'bg-amber-100 text-amber-700' : (req.payment_method === 'vodafone_cash' ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700')"
                                        x-text="req.payment_method.replace('_', ' ')"></span>
                                </div>
                                <button @click="viewDetails(req)"
                                    class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl text-xs font-bold transition-transform active:scale-95 shadow-md">View
                                    Details</button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- Request Details Modal (Popup from List) --}}
<div id="requestDetailsModal" class="fixed inset-0 z-[110] flex items-center justify-center p-4 sm:p-6" x-cloak
    style="display: none;" x-data="requestDetailsApp()">
    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" onclick="closeDetailsModal()"></div>

    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md relative z-10 p-6 md:p-8 border border-gray-200 dark:border-gray-700 transform transition-all">
        <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-white flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-500"></i> Request Details
        </h3>

        <template x-if="request">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-amber-500 p-0.5">
                        <img :src="request?.user?.profile_photo_url" class="w-full h-full object-cover rounded-full">
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-800 dark:text-white" x-text="request?.user?.name"></p>
                        <p class="text-xs text-amber-600 font-black" x-text="'Amount: ' + request?.amount + ' EGP'"></p>
                    </div>
                </div>

                <div
                    class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-xs font-bold text-gray-400">METHOD</span>
                        <span class="text-sm font-black text-gray-700 dark:text-gray-300 uppercase"
                            x-text="request?.payment_method.replace('_', ' ')"></span>
                    </div>

                    <template x-if="request?.payment_method === 'cash'">
                        <div>
                            <span class="text-xs font-bold text-gray-400 block mb-1">NOTES</span>
                            <p class="text-sm text-gray-600 dark:text-gray-400 italic" x-text="request?.notes"></p>
                        </div>
                    </template>

                    <template x-if="request?.payment_method !== 'cash'">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-gray-400">PHONE</span>
                                <span class="text-sm font-mono font-bold text-gray-700 dark:text-gray-300"
                                    x-text="request?.phone_number || 'N/A'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-gray-400">DATE/TIME</span>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300"
                                    x-text="(request?.transfer_date || 'N/A') + ' ' + (request?.transfer_time || '')"></span>
                            </div>
                            <div x-show="request?.screenshot_url">
                                <span class="text-xs font-bold text-gray-400 block mb-2">SCREENSHOT</span>
                                <a :href="request?.screenshot_url" target="_blank"
                                    class="block rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                                    <img :src="request?.screenshot_url"
                                        class="w-full h-48 object-cover hover:scale-105 transition-transform">
                                </a>
                            </div>
                        </div>
                    </template>
                </div>

                <form :action="'{{ url('wallet/requests') }}/' + request?.id + '/process'" method="POST"
                    class="space-y-4">
                    @csrf
                    <div x-show="action === 'reject'">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Rejection
                            Reason</label>
                        <textarea name="rejection_reason" rows="2"
                            class="w-full px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl outline-none text-red-800 dark:text-red-300 text-sm placeholder-red-300"
                            placeholder="Why is this request being rejected?"
                            :required="action === 'reject'"></textarea>
                    </div>

                    <div class="flex gap-4">
                        <template x-if="action === 'idle'">
                            <div class="flex gap-4 w-full">
                                <button type="button" @click="action = 'reject'"
                                    class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-red-500/20">Reject</button>
                                <button type="submit" name="action" value="accept"
                                    class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-green-500/20">Accept</button>
                            </div>
                        </template>
                        <template x-if="action === 'reject'">
                            <div class="flex gap-4 w-full">
                                <button type="button" @click="action = 'idle'"
                                    class="flex-1 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm">Back</button>
                                <button type="submit" name="action" value="reject"
                                    class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-red-500/20">Confirm
                                    Reject</button>
                            </div>
                        </template>
                    </div>
                </form>
            </div>
        </template>
    </div>
</div>

<script>
    window.openDepositRequestModal = function () {
        document.getElementById('depositRequestModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };
    window.closeDepositRequestModal = function () {
        document.getElementById('depositRequestModal').style.display = 'none';
        document.body.style.overflow = '';
    };

    window.openDepositRequestsModal = function () {
        document.getElementById('depositRequestsListModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        // Trigger fetch via Alpine
        window.dispatchEvent(new CustomEvent('fetch-requests'));
    };
    window.closeRequestsListModal = function () {
        document.getElementById('depositRequestsListModal').style.display = 'none';
        document.body.style.overflow = '';
    };

    window.closeDetailsModal = function () {
        document.getElementById('requestDetailsModal').style.display = 'none';
    };

    // Alpine Apps
    function depositRequestApp() {
        return {
            method: 'cash'
        }
    }

    function depositRequestsListApp() {
        return {
            loading: false,
            requests: [],
            init() {
                window.addEventListener('fetch-requests', () => this.fetchRequests());
            },
            async fetchRequests() {
                this.loading = true;
                try {
                    const res = await fetch('{{ route("wallet.requests.all") }}');
                    this.requests = await res.json();
                } catch (e) {
                    console.error('Failed to load requests');
                } finally {
                    this.loading = false;
                }
            },
            viewDetails(req) {
                window.dispatchEvent(new CustomEvent('show-request-details', { detail: req }));
                document.getElementById('requestDetailsModal').style.display = 'flex';
            }
        }
    }

    function requestDetailsApp() {
        return {
            request: null,
            action: 'idle',
            init() {
                window.addEventListener('show-request-details', (e) => {
                    this.request = e.detail;
                    this.action = 'idle';
                });
            }
        }
    }
</script>