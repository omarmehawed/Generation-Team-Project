{{-- Deposit Request Modal (For Regular Members) --}}
<div id="depositRequestModal" class="fixed inset-0 z-[100] flex items-end md:items-center justify-center p-4 sm:p-6"
    x-cloak style="display: none;" x-data="depositRequestApp()">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeDepositRequestModal()">
    </div>

    <div
        class="bg-white dark:bg-gray-800 rounded-t-3xl md:rounded-2xl shadow-2xl w-full max-w-lg relative z-10 p-6 md:p-8 border-t md:border border-gray-200 dark:border-gray-700 transform transition-all">
        <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-200 flex items-center gap-2">
            <i class="fas fa-hand-holding-usd text-amber-500"></i> Request Wallet Deposit
        </h3>

        <form action="{{ route('wallet.request.submit') }}" method="POST" enctype="multipart/form-data" onsubmit="handleAjaxFormSubmit(event)">
            @csrf

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Payment
                    Method</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="cash" class="peer hidden" checked
                            x-model="method">
                        <div
                            class="p-3 rounded-xl border-2 border-gray-100 dark:border-gray-700 text-center peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all group-hover:bg-gray-50">
                            <i
                                class="fas fa-money-bill-wave text-lg mb-1 block text-gray-400 group-hover:text-amber-500 peer-checked:text-amber-500"></i>
                            <p
                                class="text-[10px] font-bold text-gray-600 dark:text-gray-400 peer-checked:text-amber-600">
                                Cash</p>
                        </div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="vodafone_cash" class="peer hidden"
                            x-model="method">
                        <div
                            class="p-3 rounded-xl border-2 border-gray-100 dark:border-gray-700 text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all group-hover:bg-gray-50">
                            <i
                                class="fas fa-mobile-alt text-lg mb-1 block text-gray-400 group-hover:text-red-500 peer-checked:text-red-500"></i>
                            <p
                                class="text-[10px] font-bold text-gray-600 dark:text-gray-400 peer-checked:text-red-600">
                                V. Cash</p>
                        </div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="instapay" class="peer hidden" x-model="method">
                        <div
                            class="p-3 rounded-xl border-2 border-gray-100 dark:border-gray-700 text-center peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all group-hover:bg-gray-50">
                            <i
                                class="fas fa-university text-lg mb-1 block text-gray-400 group-hover:text-purple-500 peer-checked:text-purple-500"></i>
                            <p
                                class="text-[10px] font-bold text-gray-600 dark:text-gray-400 peer-checked:text-purple-600">
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
                        class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-gray-200"
                        placeholder="0.00">
                </div>
            </div>

            {{-- Cash Specific --}}
            <div x-show="method === 'cash'" class="mb-5">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Notes</label>
                <textarea name="notes" rows="2"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-gray-200 text-sm"
                    placeholder="e.g., I handed the money to the leader at university."></textarea>
            </div>

            {{-- Transfer Specific --}}
            <div x-show="method !== 'cash'" class="space-y-4 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Transfer Phone
                        Number</label>
                    <input type="text" name="phone_number" :required="method !== 'cash'"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-gray-200 text-sm"
                        placeholder="01XXXXXXXXX">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Transfer
                            Date</label>
                        <input type="date" name="transfer_date" :required="method !== 'cash'"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-gray-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Transfer
                            Time</label>
                        <input type="time" name="transfer_time" :required="method !== 'cash'"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-gray-200 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Upload
                        Screenshot</label>
                    <input type="file" name="screenshot" :required="method !== 'cash'"
                        class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button" onclick="closeDepositRequestModal()"
                    class="flex-1 py-3 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm transition-colors">Cancel</button>
                <button type="submit"
                    class="flex-1 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-amber-500/20 transition-transform active:scale-95">Submit
                    Request</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Deposit Request Modal --}}
<div id="editDepositRequestModal" class="fixed inset-0 z-[100] flex items-end md:items-center justify-center p-4 sm:p-6"
    x-cloak style="display: none;" x-data="editDepositRequestApp()">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeEditDepositRequestModal()">
    </div>

    <div
        class="bg-white dark:bg-gray-800 rounded-t-3xl md:rounded-2xl shadow-2xl w-full max-w-lg relative z-10 p-6 md:p-8 border-t md:border border-gray-200 dark:border-gray-700 transform transition-all">
        <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-200 flex items-center gap-2">
            <i class="fas fa-edit text-blue-500"></i> Edit Deposit Request
        </h3>

        <form :action="`/wallet/requests/${request.id}/update`" method="POST" enctype="multipart/form-data" onsubmit="handleAjaxFormSubmit(event)">
            @csrf
            
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Payment
                    Method</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="cash" class="peer hidden"
                            x-model="request.payment_method">
                        <div
                            class="p-3 rounded-xl border-2 border-gray-100 dark:border-gray-700 text-center peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all group-hover:bg-gray-50">
                            <i
                                class="fas fa-money-bill-wave text-lg mb-1 block text-gray-400 group-hover:text-amber-500 peer-checked:text-amber-500"></i>
                            <p
                                class="text-[10px] font-bold text-gray-600 dark:text-gray-400 peer-checked:text-amber-600">
                                Cash</p>
                        </div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="vodafone_cash" class="peer hidden"
                            x-model="request.payment_method">
                        <div
                            class="p-3 rounded-xl border-2 border-gray-100 dark:border-gray-700 text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all group-hover:bg-gray-50">
                            <i
                                class="fas fa-mobile-alt text-lg mb-1 block text-gray-400 group-hover:text-red-500 peer-checked:text-red-500"></i>
                            <p
                                class="text-[10px] font-bold text-gray-600 dark:text-gray-400 peer-checked:text-red-600">
                                V. Cash</p>
                        </div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="radio" name="payment_method" value="instapay" class="peer hidden" x-model="request.payment_method">
                        <div
                            class="p-3 rounded-xl border-2 border-gray-100 dark:border-gray-700 text-center peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all group-hover:bg-gray-50">
                            <i
                                class="fas fa-university text-lg mb-1 block text-gray-400 group-hover:text-purple-500 peer-checked:text-purple-500"></i>
                            <p
                                class="text-[10px] font-bold text-gray-600 dark:text-gray-400 peer-checked:text-purple-600">
                                InstaPay</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Amount (EGP)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold">£</span>
                    <input type="number" name="amount" step="0.01" min="1" required x-model="request.amount"
                        class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-gray-200"
                        placeholder="0.00">
                </div>
            </div>

            {{-- Cash Specific --}}
            <div x-show="request.payment_method === 'cash'" class="mb-5">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Notes</label>
                <textarea name="notes" rows="2" x-model="request.notes"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-gray-200 text-sm"
                    placeholder="e.g., I handed the money to the leader at university."></textarea>
            </div>

            {{-- Transfer Specific --}}
            <div x-show="request.payment_method !== 'cash'" class="space-y-4 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Transfer Phone
                        Number</label>
                    <input type="text" name="phone_number" :required="request.payment_method !== 'cash'" x-model="request.phone_number"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-gray-200 text-sm"
                        placeholder="01XXXXXXXXX">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Transfer
                            Date</label>
                        <input type="date" name="transfer_date" :required="request.payment_method !== 'cash'" 
                            :value="request.transfer_date ? request.transfer_date.split('T')[0] : ''"
                            @change="request.transfer_date = $event.target.value"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-gray-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Transfer
                            Time</label>
                        <input type="time" name="transfer_time" :required="request.payment_method !== 'cash'" x-model="request.transfer_time"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-gray-200 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Upload
                        New Screenshot (Optional)</label>
                    <div x-show="request.screenshot_url" class="mb-2">
                        <span class="text-[10px] text-gray-500 block mb-1">Current Screenshot:</span>
                        <a :href="request.screenshot_url" target="_blank" class="block w-20 h-20 rounded border border-gray-200 overflow-hidden relative group">
                           <img :src="request.screenshot_url" class="w-full h-full object-cover">
                           <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                               <i class="fas fa-external-link-alt text-white"></i>
                           </div>
                        </a>
                    </div>
                    <input type="file" name="screenshot" :required="request.payment_method !== 'cash' && !request.screenshot_url"
                        class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button" onclick="closeEditDepositRequestModal()"
                    class="flex-1 py-3 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm transition-colors">Cancel</button>
                <button type="submit"
                    class="flex-1 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 transition-transform active:scale-95">Update
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
            class="p-6 border-b border-gray-100 dark:border-gray-700 space-y-4 bg-gray-50 dark:bg-gray-900">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                    <i class="fas fa-list-alt text-indigo-500"></i> Pending Deposit Requests
                </h3>
                <button onclick="closeRequestsListModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            {{-- Search Bar --}}
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" x-model="searchQuery" 
                       placeholder="Search by Name or Academic Number..."
                       class="w-full pl-10 pr-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm text-gray-700 dark:text-gray-300 transition-all">
            </div>
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

            <template x-if="!loading && requests.length > 0 && filteredRequests.length === 0">
                <div class="text-center p-12 text-gray-400">
                    <i class="fas fa-search text-5xl mb-4 opacity-20"></i>
                    <p class="font-bold uppercase tracking-widest text-[10px]">No matching results found</p>
                </div>
            </template>

            <template x-if="!loading && filteredRequests.length > 0">
                <div class="space-y-4">
                    <template x-for="req in filteredRequests" :key="req.id">
                        <div
                            class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex items-center gap-4 w-full md:w-auto">
                                <div
                                    class="w-12 h-12 rounded-full overflow-hidden border-2 border-white shadow-sm">
                                    <img :src="req.user.profile_photo_url" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 dark:text-gray-200" x-text="req.user.name"></p>
                                    <p class="text-xs text-gray-400 font-mono" x-text="req.user.email"></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-8 w-full md:w-auto justify-between md:justify-end">
                                <div class="text-center md:text-right">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Amount</p>
                                    <p class="text-lg font-black text-amber-600"
                                        x-text="req.amount + ' EGP'"></p>
                                </div>
                                <div class="text-center md:text-right">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Method</p>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider"
                                        :class="req.payment_method ==='cash' ? 'bg-amber-100 text-amber-700' : (req.payment_method === 'vodafone_cash' ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700')"
                                        x-text="req.payment_method.replace('_', ' ')"></span>
                                </div>
                                <template x-if="req.is_edited">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-[10px] font-bold uppercase tracking-wider mb-1"><i class="fas fa-pencil-alt"></i> Edited</span>
                                        <button @click="viewEdit(req)" class="text-[10px] font-bold text-blue-500 hover:text-blue-700 underline">View Edit</button>
                                    </div>
                                </template>
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
        <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-200 flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-500"></i> Request Details
        </h3>

        <template x-if="request">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-amber-500 p-0.5">
                        <img :src="request?.user?.profile_photo_url" class="w-full h-full object-cover rounded-full">
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-800 dark:text-gray-200" x-text="request?.user?.name"></p>
                        <p class="text-xs text-amber-600 font-black" x-text="'Amount: ' + request?.amount + ' EGP'"></p>
                    </div>
                </div>

                <div
                    class="bg-gray-50 dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 space-y-3">
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
                    class="space-y-4" onsubmit="handleAjaxFormSubmit(event)">
                    @csrf
                    <div x-show="action === 'reject'">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Rejection
                            Reason</label>
                        <textarea name="rejection_reason" rows="2"
                            class="w-full px-4 py-3 bg-red-50 border border-red-200 rounded-xl outline-none text-red-800 text-sm placeholder-red-300"
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
                                    class="flex-1 py-3 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm">Back</button>
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

{{-- View Edit Request Modal (For Leaders) --}}
<div id="viewEditDepositRequestModal" class="fixed inset-0 z-[110] flex items-center justify-center p-4 sm:p-6" x-cloak
    style="display: none;" x-data="viewEditRequestApp()">
    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" onclick="closeViewEditModal()"></div>

    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl relative z-10 p-6 md:p-8 border border-gray-200 dark:border-gray-700 transform transition-all max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-200 flex items-center gap-2">
            <i class="fas fa-history text-amber-500"></i> Request Change Log
        </h3>

        <template x-if="request && request.old_values">
            <div class="space-y-5">

                {{-- Member Info Strip --}}
                <div class="flex items-center gap-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800">
                    <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-amber-400 flex-shrink-0">
                        <img :src="request.user?.profile_photo_url" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-800 dark:text-gray-200 text-sm truncate" x-text="request.user?.name"></p>
                        <p class="text-[10px] text-amber-600 font-bold uppercase tracking-wide">Edited their pending request</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Current Amount</p>
                        <p class="text-base font-black text-amber-600" x-text="parseFloat(request.amount).toFixed(2) + ' EGP'"></p>
                    </div>
                </div>

                {{-- Section Header --}}
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/40 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-indigo-500 text-xs"></i>
                    </div>
                    <h4 class="text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-[0.15em]">Field-by-Field Changes</h4>
                </div>

                {{-- ─── Change Matrix (one row per changed field) ─── --}}
                <div class="space-y-2">

                    {{-- ① Payment Method / Transaction Type --}}
                    <template x-if="request.old_values.payment_method !== request.payment_method">
                        <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3"
                               ><i class="fas fa-exchange-alt mr-1 text-purple-400"></i>Transaction Type — Changed</p>
                            <div class="grid grid-cols-[1fr_36px_1fr] items-center gap-2">
                                {{-- Before Badge --}}
                                <div>
                                    <p class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-1.5">Before</p>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wide line-through opacity-60"
                                          :class="methodColor(request.old_values.payment_method, 'before')">
                                        <i :class="methodIcon(request.old_values.payment_method)"></i>
                                        <span x-text="methodLabel(request.old_values.payment_method)"></span>
                                    </span>
                                </div>
                                {{-- Arrow --}}
                                <div class="flex justify-center">
                                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center shadow-md shadow-indigo-500/30">
                                        <i class="fas fa-arrow-right text-white text-xs"></i>
                                    </div>
                                </div>
                                {{-- After Badge --}}
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-green-500 uppercase tracking-widest mb-1.5">After</p>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wide"
                                          :class="methodColor(request.payment_method, 'after')">
                                        <i :class="methodIcon(request.payment_method)"></i>
                                        <span x-text="methodLabel(request.payment_method)"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- ② Amount --}}
                    <template x-if="Number(request.old_values.amount) !== Number(request.amount)">
                        <div class="grid grid-cols-[1fr_28px_1fr] items-center gap-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5"><i class="fas fa-coins mr-1 text-amber-400"></i>Amount — Before</p>
                                <span class="text-red-500 font-bold text-sm line-through" x-text="parseFloat(request.old_values.amount).toFixed(2) + ' EGP'"></span>
                            </div>
                            <div class="flex justify-center">
                                <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center">
                                    <i class="fas fa-arrow-right text-indigo-500 text-[10px]"></i>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">After</p>
                                <span class="text-green-600 font-black text-sm" x-text="parseFloat(request.amount).toFixed(2) + ' EGP'"></span>
                            </div>
                        </div>
                    </template>

                    {{-- ③ Phone Number --}}
                    <template x-if="(request.old_values.phone_number || '') !== (request.phone_number || '')">
                        <div class="grid grid-cols-[1fr_28px_1fr] items-center gap-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5"><i class="fas fa-phone mr-1 text-blue-400"></i>Phone — Before</p>
                                <span class="text-red-500 font-mono text-sm line-through" x-text="request.old_values.phone_number || '(none)'"></span>
                            </div>
                            <div class="flex justify-center">
                                <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center">
                                    <i class="fas fa-arrow-right text-indigo-500 text-[10px]"></i>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">After</p>
                                <span class="text-green-600 font-mono font-bold text-sm" x-text="request.phone_number || '(none)'"></span>
                            </div>
                        </div>
                    </template>

                    {{-- ④ Transfer Date --}}
                    <template x-if="normDate(request.old_values.transfer_date) !== normDate(request.transfer_date)">
                        <div class="grid grid-cols-[1fr_28px_1fr] items-center gap-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5"><i class="fas fa-calendar-day mr-1 text-teal-400"></i>Date — Before</p>
                                <span class="text-red-500 font-bold text-sm line-through" x-text="formatDate(request.old_values.transfer_date)"></span>
                            </div>
                            <div class="flex justify-center">
                                <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center">
                                    <i class="fas fa-arrow-right text-indigo-500 text-[10px]"></i>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">After</p>
                                <span class="text-green-600 font-bold text-sm" x-text="formatDate(request.transfer_date)"></span>
                            </div>
                        </div>
                    </template>

                    {{-- ⑤ Transfer Time --}}
                    <template x-if="(request.old_values.transfer_time || '') !== (request.transfer_time || '')">
                        <div class="grid grid-cols-[1fr_28px_1fr] items-center gap-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5"><i class="fas fa-clock mr-1 text-orange-400"></i>Time — Before</p>
                                <span class="text-red-500 font-bold text-sm line-through" x-text="formatTime(request.old_values.transfer_time)"></span>
                            </div>
                            <div class="flex justify-center">
                                <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center">
                                    <i class="fas fa-arrow-right text-indigo-500 text-[10px]"></i>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">After</p>
                                <span class="text-green-600 font-bold text-sm" x-text="formatTime(request.transfer_time)"></span>
                            </div>
                        </div>
                    </template>

                    {{-- ⑥ Notes --}}
                    <template x-if="(request.old_values.notes || '') !== (request.notes || '')">
                        <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700 space-y-2">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest"><i class="fas fa-sticky-note mr-1 text-yellow-400"></i>Notes — Changed</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-2.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                                    <p class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-1">Before</p>
                                    <p class="text-xs text-red-700 dark:text-red-300 italic leading-relaxed" x-text="request.old_values.notes || '(empty)'"></p>
                                </div>
                                <div class="p-2.5 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                                    <p class="text-[9px] font-black text-green-500 uppercase tracking-widest mb-1">After</p>
                                    <p class="text-xs text-green-700 dark:text-green-300 italic leading-relaxed" x-text="request.notes || '(empty)'"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                </div>

                {{-- ─── Screenshot Comparison (only if actually changed) ─── --}}
                <template x-if="screenshotChanged()">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-6 h-6 bg-violet-100 dark:bg-violet-900/40 rounded-lg flex items-center justify-center">
                                <i class="fas fa-images text-violet-500 text-xs"></i>
                            </div>
                            <h4 class="text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-[0.15em]">Screenshot Changed</h4>
                        <div class="space-y-4">
                            {{-- Before --}}
                            <div>
                                <p class="text-[9px] font-black text-red-400 uppercase tracking-widest text-center mb-2">Before (Original)</p>
                                <template x-if="oldScreenshot()">
                                    <a :href="oldScreenshot()" target="_blank" class="block rounded-xl overflow-hidden border-2 border-red-200 relative group cursor-zoom-in">
                                        <img :src="oldScreenshot() + '?cache=' + Date.now()" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300"
                                             x-on:error="$el.closest('a').style.display='none'; $el.closest('a').nextElementSibling.style.setProperty('display','flex','important')">
                                        <div class="absolute inset-0 bg-red-900/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <i class="fas fa-expand-alt text-white text-lg"></i>
                                        </div>
                                    </a>
                                </template>
                                <div class="w-full h-40 bg-gray-100 dark:bg-gray-800 rounded-xl items-center justify-center text-gray-400 text-xs italic border-2 border-dashed border-gray-200"
                                     :style="oldScreenshot() ? 'display:none' : 'display:flex'">
                                    No Screenshot
                                </div>
                            </div>
                            {{-- After --}}
                            <div>
                                <p class="text-[9px] font-black text-green-500 uppercase tracking-widest text-center mb-2">After (Current)</p>
                                <template x-if="newScreenshot()">
                                    <a :href="newScreenshot()" target="_blank" class="block rounded-xl overflow-hidden border-2 border-green-200 relative group cursor-zoom-in">
                                        <img :src="newScreenshot() + '?cache=' + Date.now()" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300"
                                             x-on:error="$el.closest('a').style.display='none'; $el.closest('a').nextElementSibling.style.setProperty('display','flex','important')">
                                        <div class="absolute inset-0 bg-green-900/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <i class="fas fa-expand-alt text-white text-lg"></i>
                                        </div>
                                    </a>
                                </template>
                                <div class="w-full h-40 bg-gray-100 dark:bg-gray-800 rounded-xl items-center justify-center text-gray-400 text-xs italic border-2 border-dashed border-gray-200"
                                     :style="newScreenshot() ? 'display:none' : 'display:flex'">
                                    No Screenshot
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Footer --}}
                <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeViewEditModal()"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm transition-colors">Close</button>
                    <button type="button" @click="closeAndOpenDetails()"
                        class="px-6 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-500/20 transition-colors">
                        <i class="fas fa-tasks mr-1.5"></i> Go To Process Action
                    </button>
                </div>

            </div>
        </template>
    </div>
</div>

@if($hasManagement)
    {{-- Bulk Operation Modal (For Admins) --}}
    <div id="bulkModal" class="fixed inset-0 z-[100] flex items-end md:items-center justify-center p-4 sm:p-6" x-cloak
        style="display: none;" x-data="bulkApp()">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeBulkModal()">
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-t-3xl md:rounded-2xl shadow-2xl w-full max-w-lg relative z-10 p-6 border-t md:border border-gray-200 dark:border-gray-700 transform transition-all flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                    <i class="fas fa-layer-group text-amber-500"></i> Bulk Operation
                </h3>
                <button @click="closeBulkModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('wallet.bulk_transact') }}" method="POST"
                class="overflow-y-auto flex-1 pr-2 custom-scroll" onsubmit="handleAjaxFormSubmit(event)">
                @csrf

                {{-- Student Selector --}}
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Students</label>
                    <div class="space-y-3">
                        {{-- ID Input --}}
                        <div class="relative">
                            <input type="text" x-model="studentQuery" @keydown.enter.prevent="addStudent()"
                                class="w-full pl-4 pr-12 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-gray-800 dark:text-gray-200 text-sm"
                                placeholder="Type Academic ID and press Enter..." :disabled="loading">
                            <button type="button" @click="addStudent()"
                                class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors"
                                :disabled="loading || !studentQuery">
                                <i class="fas fa-plus" x-show="!loading"></i>
                                <i class="fas fa-spinner fa-spin" x-show="loading"></i>
                            </button>
                        </div>
                        <p x-show="error" x-text="error" class="text-red-500 text-[10px] font-bold mt-1"></p>

                        {{-- Selected Students Avatars --}}
                        <div
                            class="flex flex-wrap gap-2 p-2 bg-gray-50 dark:bg-gray-900 rounded-xl border border-dashed border-gray-200 dark:border-gray-700 min-h-[60px]">
                            <template x-for="(student, index) in selectedStudents" :key="student.id">
                                <div class="relative group">
                                    <div class="w-10 h-10 rounded-full border-2 border-white overflow-hidden shadow-sm"
                                        :title="student.name">
                                        <img :src="student.avatar" class="w-full h-full object-cover">
                                    </div>
                                    <button type="button" @click="removeStudent(index)"
                                        class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white rounded-full flex items-center justify-center text-[10px] shadow-sm hover:scale-110 transition-transform">
                                        <i class="fas fa-times text-[8px]"></i>
                                    </button>
                                    {{-- Hidden input for form submission --}}
                                    <input type="hidden" name="user_ids[]" :value="student.id">
                                </div>
                            </template>
                            <template x-if="selectedStudents.length === 0">
                                <div class="flex items-center justify-center w-full h-full text-gray-400 text-xs italic">
                                    No students selected yet.
                                </div>
                            </template>
                        </div>
                        <p class="text-[10px] text-gray-400" x-text="selectedStudents.length + ' students selected'"></p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Operation
                        Type</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer group">
                            <input type="radio" name="type" value="deposit" class="peer hidden" checked x-model="type">
                            <div
                                class="p-4 rounded-xl border-2 border-gray-100 dark:border-gray-700 text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition-all hover:bg-gray-50">
                                <i class="fas fa-plus text-lg mb-1 block text-gray-400 peer-checked:text-green-500"></i>
                                <p class="text-xs font-bold text-gray-600 dark:text-gray-400 peer-checked:text-green-600">
                                    Deposit</p>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="type" value="withdrawal" class="peer hidden" x-model="type">
                            <div
                                class="p-4 rounded-xl border-2 border-gray-100 dark:border-gray-700 text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all hover:bg-gray-50">
                                <i class="fas fa-minus text-lg mb-1 block text-gray-400 peer-checked:text-red-500"></i>
                                <p class="text-xs font-bold text-gray-600 dark:text-gray-400 peer-checked:text-red-600">
                                    Withdrawal</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Amount (EGP)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold">£</span>
                        <input type="number" name="amount" step="0.1" min="0.1" required x-model="amount"
                            class="w-full pl-10 pr-4 py-4 text-2xl font-mono font-black bg-gray-50 dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 text-gray-800 dark:text-gray-200 transition-all placeholder-gray-300"
                            placeholder="0.0">
                    </div>
                    {{-- Total Calculation --}}
                    <div
                        class="mt-4 p-4 bg-amber-50/50 rounded-2xl border border-amber-100 flex justify-between items-center">
                        <span class="text-xs font-bold text-amber-700/70 uppercase">Total Estimated</span>
                        <span class="text-xl font-black text-amber-600"
                            x-text="(amount * selectedStudents.length).toLocaleString() + ' EGP'"></span>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="button" @click="closeBulkModal()"
                        class="flex-1 py-4 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 text-gray-700 dark:text-gray-300 rounded-2xl font-bold text-sm transition-all">
                        Cancel
                    </button>
                    <button type="submit" :disabled="selectedStudents.length === 0 || !amount"
                        class="flex-1 py-4 bg-gray-900 hover:bg-black text-amber-400 rounded-2xl font-black text-sm shadow-xl shadow-black/20 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="fas fa-bolt"></i> Process Operation
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<script>
    window.openDepositRequestModal = function () {
        document.getElementById('depositRequestModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };
    window.closeDepositRequestModal = function () {
        document.getElementById('depositRequestModal').style.display = 'none';
        document.body.style.overflow = '';
    };

    window.openEditDepositRequestModal = function (id, requestData) {
        document.getElementById('editDepositRequestModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        window.dispatchEvent(new CustomEvent('edit-request', { detail: requestData }));
    };
    window.closeEditDepositRequestModal = function () {
        document.getElementById('editDepositRequestModal').style.display = 'none';
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

    window.openViewEditModal = function (req) {
        document.getElementById('viewEditDepositRequestModal').style.display = 'flex';
        window.dispatchEvent(new CustomEvent('show-view-edit', { detail: req }));
    };

    window.closeViewEditModal = function () {
        document.getElementById('viewEditDepositRequestModal').style.display = 'none';
    };

    window.openBulkModal = function () {
        document.getElementById('bulkModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.closeBulkModal = function () {
        document.getElementById('bulkModal').style.display = 'none';
        document.body.style.overflow = '';
    };

    // Alpine Apps
    function bulkApp() {
        return {
            loading: false,
            studentQuery: '',
            selectedStudents: [],
            type: 'deposit',
            amount: 0,
            error: null,

            init() {
                window.addEventListener('open-bulk-modal', () => {
                    this.openBulkModal();
                });
            },

            openBulkModal() {
                document.getElementById('bulkModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            },

            closeBulkModal() {
                document.getElementById('bulkModal').style.display = 'none';
                document.body.style.overflow = '';
                // Reset form if needed
                this.selectedStudents = [];
                this.studentQuery = '';
                this.error = null;
            },

            async addStudent() {
                if (!this.studentQuery) return;

                // Check if already added
                if (this.selectedStudents.some(s => s.academic_id === this.studentQuery)) {
                    this.error = "Student already added.";
                    return;
                }

                this.loading = true;
                this.error = null;

                try {
                    const response = await fetch(`{{ route('wallet.search_member') }}?query=${this.studentQuery}`);
                    const data = await response.json();

                    if (data) {
                        this.selectedStudents.push(data);
                        this.studentQuery = '';
                    } else {
                        this.error = "Student not found.";
                    }
                } catch (e) {
                    this.error = "Search failed.";
                } finally {
                    this.loading = false;
                }
            },

            removeStudent(index) {
                this.selectedStudents.splice(index, 1);
            }
        }
    }

    function depositRequestApp() {
        return {
            method: 'cash'
        }
    }

    function editDepositRequestApp() {
        return {
            request: {
                id: null,
                payment_method: 'cash',
                amount: '',
                notes: '',
                phone_number: '',
                transfer_date: '',
                transfer_time: '',
                screenshot_url: null
            },
            init() {
                window.addEventListener('edit-request', (e) => {
                    // Deep copy to avoid mutating Original before save
                    this.request = JSON.parse(JSON.stringify(e.detail));
                    // Fix date format if needed
                    if(this.request.transfer_date) {
                        this.request.transfer_date = this.request.transfer_date.split('T')[0];
                    }
                });
            }
        }
    }

    function depositRequestsListApp() {
        return {
            loading: false,
            requests: [],
            searchQuery: '',
            get filteredRequests() {
                if (!this.searchQuery) return this.requests;
                const q = this.searchQuery.toLowerCase().trim();
                return this.requests.filter(req => {
                    const name = (req.user?.name || '').toLowerCase();
                    const email = (req.user?.email || '').toLowerCase(); // Email contains academic ID
                    return name.includes(q) || email.includes(q);
                });
            },
            init() {
                window.addEventListener('fetch-requests', () => {
                    this.fetchRequests();
                    this.searchQuery = '';
                });
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
            },
            viewEdit(req) {
                window.openViewEditModal(req);
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

    function viewEditRequestApp() {
        return {
            request: null,

            // ── Payment Method Helpers ─────────────────────────────────────
            methodLabel(m) {
                const map = { cash: 'Cash', vodafone_cash: 'Vodafone Cash', instapay: 'InstaPay' };
                return map[m] || (m || 'N/A');
            },
            methodColor(m, side) {
                const vivid = {
                    cash:         'bg-amber-100 text-amber-700 border border-amber-200',
                    vodafone_cash:'bg-red-100 text-red-700 border border-red-200',
                    instapay:     'bg-purple-100 text-purple-700 border border-purple-200',
                };
                const muted = {
                    cash:         'bg-amber-50 text-amber-400 border border-amber-100',
                    vodafone_cash:'bg-red-50 text-red-400 border border-red-100',
                    instapay:     'bg-purple-50 text-purple-400 border border-purple-100',
                };
                const palette = side === 'after' ? vivid : muted;
                return palette[m] || 'bg-gray-100 text-gray-500 border border-gray-200';
            },
            methodIcon(m) {
                const icons = { cash: 'fas fa-money-bill-wave', vodafone_cash: 'fas fa-mobile-alt', instapay: 'fas fa-university' };
                return icons[m] || 'fas fa-credit-card';
            },

            // ── Screenshot Helpers (robust fallback to raw old_values path) ─
            // Returns the old screenshot URL regardless of accessor null issues.
            oldScreenshot() {
                if (this.request.old_screenshot_url) return this.request.old_screenshot_url;
                // Fallback: raw screenshot_path stored inside old_values
                const raw = this.request.old_values && this.request.old_values.screenshot_path;
                return raw || null;
            },
            // Returns the current (new) screenshot URL.
            newScreenshot() {
                return this.request.screenshot_url || this.request.screenshot_path || null;
            },
            // True when the screenshot actually changed between old and new state.
            screenshotChanged() {
                const oldUrl = this.oldScreenshot();
                const newUrl = this.newScreenshot();
                // Both null → no screenshot involved at all → hide
                if (!oldUrl && !newUrl) return false;
                // Same URL → no change → hide
                if (oldUrl && newUrl && oldUrl === newUrl) return false;
                // Otherwise (one is different, or one is null) → show
                return true;
            },

            // ── Date/Time Helpers ─────────────────────────────────────────
            // Strips ISO timestamp (2026-05-30T00:00:00.000000Z) → '2026-05-30'
            normDate(d) {
                if (!d) return '';
                return String(d).split('T')[0];
            },

            // Returns 'May 30, 2026' from any date string/ISO
            formatDate(d) {
                if (!d) return 'N/A';
                const clean = String(d).split('T')[0];
                const parts = clean.split('-');
                if (parts.length < 3) return clean;
                const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                return `${months[parseInt(parts[1]) - 1]} ${parseInt(parts[2])}, ${parts[0]}`;
            },

            // Returns '02:30 PM' from '14:30:00' or '14:30'
            formatTime(t) {
                if (!t) return 'N/A';
                // Handle full ISO: strip date part
                const timePart = String(t).includes('T') ? String(t).split('T')[1] : String(t);
                const [hStr, mStr] = timePart.split(':');
                const h = parseInt(hStr);
                const m = mStr || '00';
                const period = h >= 12 ? 'PM' : 'AM';
                const h12 = h % 12 || 12;
                return `${String(h12).padStart(2, '0')}:${m} ${period}`;
            },

            init() {
                window.addEventListener('show-view-edit', (e) => {
                    this.request = e.detail;
                });
            },

            closeAndOpenDetails() {
                window.closeViewEditModal();
                if (this.request) {
                    window.dispatchEvent(new CustomEvent('show-request-details', { detail: this.request }));
                    document.getElementById('requestDetailsModal').style.display = 'flex';
                }
            }
        }
    }
</script>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- Fee Details Modal (Member & Leader — Post-Approval Financial Receipt)  --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div id="feeDetailsModal"
     class="fixed inset-0 z-[120] flex items-end md:items-center justify-center p-4 sm:p-6"
     style="display: none;">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity" onclick="closeFeeDetailsModal()"></div>

    {{-- Panel --}}
    <div id="feeDetailsPanel"
         class="bg-white dark:bg-gray-800 rounded-t-3xl md:rounded-2xl shadow-2xl w-full max-w-md relative z-10 border-t md:border border-gray-200 dark:border-gray-700 overflow-hidden transform transition-all">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-500 px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-receipt text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-white font-black text-base tracking-tight">Fee Details</h3>
                    <p class="text-emerald-100 text-[10px] font-medium uppercase tracking-widest">Transfer Audit Log</p>
                </div>
            </div>
            <button onclick="closeFeeDetailsModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 text-white transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">

            {{-- ① Transfer Summary --}}
            <div class="bg-gray-50 dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.15em] mb-3 flex items-center gap-2">
                    <i class="fas fa-info-circle text-teal-500"></i> Transfer Summary
                </p>
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400">METHOD</span>
                        <span id="fd-method"
                              class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400">AMOUNT</span>
                        <span id="fd-amount" class="text-base font-black text-emerald-600"></span>
                    </div>
                    <div id="fd-phone-row" class="flex justify-between items-center" style="display:none!important">
                        <span class="text-xs font-bold text-gray-400">SENT TO</span>
                        <span id="fd-phone" class="text-sm font-mono font-bold text-gray-700 dark:text-gray-300"></span>
                    </div>
                </div>
            </div>

            {{-- ② Transfer Timing (member-initiated) --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-4 border border-blue-100 dark:border-blue-800">
                <p class="text-[10px] font-black text-blue-400 uppercase tracking-[0.15em] mb-3 flex items-center gap-2">
                    <i class="fas fa-clock text-blue-500"></i> Transfer Initiated By Member
                </p>
                <div class="space-y-2">
                    <div id="fd-transfer-date-row" class="flex justify-between items-center" style="display:none!important">
                        <span class="text-xs font-bold text-gray-400">DATE / TIME</span>
                        <span id="fd-transfer-datetime" class="text-sm font-bold text-gray-700 dark:text-gray-300"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400">REQUEST SUBMITTED</span>
                        <span id="fd-submitted-at" class="text-sm font-bold text-gray-700 dark:text-gray-300"></span>
                    </div>
                </div>
            </div>

            {{-- ③ Transfer Proof / Screenshot --}}
            <div id="fd-screenshot-section" style="display:none">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.15em] mb-2 flex items-center gap-2">
                    <i class="fas fa-image text-indigo-500"></i> Transfer Proof
                </p>
                <a id="fd-screenshot-link" href="#" target="_blank"
                   class="block rounded-2xl overflow-hidden border-2 border-indigo-200 dark:border-indigo-700 relative group cursor-zoom-in shadow-md hover:shadow-lg transition-shadow">
                    <img id="fd-screenshot-img" src="" alt="Transfer Screenshot"
                         class="w-full object-cover max-h-60 group-hover:scale-[1.02] transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4">
                        <span class="text-white text-xs font-bold bg-black/50 px-3 py-1 rounded-full backdrop-blur-sm flex items-center gap-2">
                            <i class="fas fa-expand-alt"></i> View Full Size
                        </span>
                    </div>
                </a>
            </div>

            {{-- ④ Approval / Rejection Stamp --}}
            <div id="fd-approval-section"
                 class="rounded-2xl border-2 p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div id="fd-stamp-icon" class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
                        <i id="fd-stamp-icon-i" class="text-white text-sm"></i>
                    </div>
                    <div>
                        <p id="fd-stamp-label" class="text-[10px] font-black uppercase tracking-widest mb-1"></p>
                        <p id="fd-approval-text" class="text-sm font-bold leading-snug"></p>
                        <p id="fd-rejection-reason" class="text-sm font-medium mt-1 leading-snug" style="display:none"></p>
                        <p id="fd-no-approval-text" class="text-sm text-gray-400 italic" style="display:none">Approval timestamp not recorded.</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700 flex justify-end">
            <button onclick="closeFeeDetailsModal()"
                    class="px-6 py-2.5 bg-gray-800 hover:bg-black text-white rounded-xl font-bold text-sm transition-colors shadow-sm">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    // ── Fee Details Modal ────────────────────────────────────────────────────
    window.openFeeDetailsModal = function (data) {
        // --- Method badge
        const methodMap = { cash: 'Cash', vodafone_cash: 'Vodafone Cash', instapay: 'InstaPay' };
        document.getElementById('fd-method').textContent = methodMap[data.payment_method] || data.payment_method;

        // --- Amount
        document.getElementById('fd-amount').textContent = '+' + parseFloat(data.amount).toFixed(2) + ' EGP';

        // --- Phone
        const phoneRow = document.getElementById('fd-phone-row');
        if (data.phone_number && data.payment_method !== 'cash') {
            document.getElementById('fd-phone').textContent = data.phone_number;
            phoneRow.style.setProperty('display', 'flex', 'important');
        } else {
            phoneRow.style.setProperty('display', 'none', 'important');
        }

        // --- Transfer date/time
        const dtRow = document.getElementById('fd-transfer-date-row');
        if (data.transfer_date) {
            const dtText = data.transfer_date + (data.transfer_time ? ' at ' + data.transfer_time : '');
            document.getElementById('fd-transfer-datetime').textContent = dtText;
            dtRow.style.setProperty('display', 'flex', 'important');
        } else {
            dtRow.style.setProperty('display', 'none', 'important');
        }

        // --- Submitted at
        document.getElementById('fd-submitted-at').textContent = data.submitted_at || '—';

        // --- Screenshot
        const ssSection = document.getElementById('fd-screenshot-section');
        if (data.screenshot_url) {
            document.getElementById('fd-screenshot-link').href = data.screenshot_url;
            document.getElementById('fd-screenshot-img').src  = data.screenshot_url;
            ssSection.style.display = 'block';
        } else {
            ssSection.style.display = 'none';
        }

        // --- Approval / Rejection stamp
        const approvalSection = document.getElementById('fd-approval-section');
        const stampIcon       = document.getElementById('fd-stamp-icon');
        const stampIconI      = document.getElementById('fd-stamp-icon-i');
        const stampLabel      = document.getElementById('fd-stamp-label');
        const approvalText    = document.getElementById('fd-approval-text');
        const rejectionReason = document.getElementById('fd-rejection-reason');
        const noApprovalText  = document.getElementById('fd-no-approval-text');

        const isRejected = data.status === 'rejected';

        if (isRejected) {
            // Red / rejected styling
            approvalSection.className = 'rounded-2xl border-2 border-red-200 dark:border-red-700 bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 p-4 shadow-sm';
            stampIcon.className = 'w-9 h-9 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-md shadow-red-500/30';
            stampIconI.className = 'fas fa-times text-white text-sm';
            stampLabel.className = 'text-[10px] font-black text-red-600 uppercase tracking-widest mb-1';
            stampLabel.textContent = 'Rejected';
            approvalText.className = 'text-sm font-bold text-red-800 dark:text-red-300 leading-snug';

            if (data.approved_at) {
                approvalText.textContent = '🔴 Rejected by ' + (data.approved_by || 'Leader') + ' on ' + data.approved_at;
                approvalText.style.display = 'block';
                noApprovalText.style.display = 'none';
            } else {
                approvalText.style.display = 'none';
                noApprovalText.style.display = 'block';
            }

            // Show rejection reason if available
            if (data.rejection_reason) {
                rejectionReason.textContent = '💬 Reason: ' + data.rejection_reason;
                rejectionReason.className = 'text-sm font-medium text-red-600 dark:text-red-400 mt-1 leading-snug';
                rejectionReason.style.display = 'block';
            } else {
                rejectionReason.style.display = 'none';
            }
        } else {
            // Green / accepted styling
            approvalSection.className = 'rounded-2xl border-2 border-emerald-200 dark:border-emerald-700 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 p-4 shadow-sm';
            stampIcon.className = 'w-9 h-9 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-md shadow-emerald-500/30';
            stampIconI.className = 'fas fa-check text-white text-sm';
            stampLabel.className = 'text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1';
            stampLabel.textContent = 'Official Approval';
            approvalText.className = 'text-sm font-bold text-emerald-800 dark:text-emerald-300 leading-snug';
            rejectionReason.style.display = 'none';

            if (data.approved_at) {
                approvalText.textContent = '🟢 Accepted by ' + (data.approved_by || 'Leader') + ' on ' + data.approved_at;
                approvalText.style.display = 'block';
                noApprovalText.style.display = 'none';
            } else {
                approvalText.style.display = 'none';
                noApprovalText.style.display = 'block';
            }
        }

        document.getElementById('feeDetailsModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.closeFeeDetailsModal = function () {
        document.getElementById('feeDetailsModal').style.display = 'none';
        document.body.style.overflow = '';
    };
</script>