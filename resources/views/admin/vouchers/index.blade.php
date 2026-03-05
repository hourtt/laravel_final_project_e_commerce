<x-admin-layout>

    {{-- ── Page header ─────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Voucher Management</h1>
            <p class="text-xs text-gray-400 mt-0.5">Create and manage discount voucher codes</p>
        </div>
        <a href="{{ route('admin.vouchers.create') }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition-colors shadow-sm shadow-blue-500/30">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                    clip-rule="evenodd" />
            </svg>
            Create Voucher
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div
            class="mb-5 flex items-center gap-2 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Table ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">
                            Code</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">
                            Discount</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">
                            Product Scope</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">
                            Usage</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">
                            Expires</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">
                            Status</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($vouchers as $voucher)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            {{-- Code --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-mono text-xs font-bold tracking-widest">
                                        {{ $voucher->code }}
                                    </span>
                                    {{-- Hidden input — holds the code for synchronous copy --}}
                                    <input type="text" id="code-input-{{ $voucher->id }}"
                                        value="{{ $voucher->code }}" readonly tabindex="-1" aria-hidden="true"
                                        style="position:absolute;width:1px;height:1px;opacity:0;pointer-events:none;">
                                    {{-- Copy button --}}
                                    <button type="button" onclick="copyCode('{{ $voucher->id }}', this)"
                                        class="relative text-gray-300 hover:text-indigo-500 transition-colors"
                                        title="Copy code">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>

                            {{-- Discount --}}
                            <td class="px-4 py-4">
                                @if ($voucher->discount_type === 'percentage')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ $voucher->discount_value }}% OFF
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        ${{ number_format($voucher->discount_value, 2) }} OFF
                                    </span>
                                @endif
                            </td>

                            {{-- Product Scope --}}
                            <td class="px-4 py-4">
                                @if ($voucher->product)
                                    <span class="text-xs text-gray-700 font-medium">{{ $voucher->product->name }}</span>
                                @else
                                    <span class="text-xs text-gray-400 italic">Any product</span>
                                @endif
                            </td>

                            {{-- Usage --}}
                            <td class="px-4 py-4">
                                <span class="text-xs text-gray-700 font-semibold tabular-nums">
                                    {{ $voucher->used_count }}
                                    @if ($voucher->usage_limit !== null)
                                        / {{ $voucher->usage_limit }}
                                    @else
                                        / ∞
                                    @endif
                                </span>
                                @if ($voucher->usage_limit !== null)
                                    <div class="mt-1 h-1 w-20 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-400 rounded-full"
                                            style="width: {{ $voucher->usage_limit > 0 ? min(100, round(($voucher->used_count / $voucher->usage_limit) * 100)) : 0 }}%">
                                        </div>
                                    </div>
                                @endif
                            </td>

                            {{-- Expires --}}
                            <td class="px-4 py-4">
                                @if ($voucher->expires_at)
                                    @if ($voucher->expires_at->isPast())
                                        <span class="text-xs text-red-500 font-semibold">
                                            Expired {{ $voucher->expires_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-600">
                                            {{ $voucher->expires_at->format('M d, Y') }}
                                            <br>
                                            <span
                                                class="text-gray-400">{{ $voucher->expires_at->diffForHumans() }}</span>
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400 italic">Never</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4">
                                @if ($voucher->status)
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.vouchers.edit', $voucher->id) }}"
                                        class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors"
                                        title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST"
                                        onsubmit="return confirm('Delete voucher {{ $voucher->code }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-colors"
                                            title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center text-gray-400 text-sm">
                                <div class="flex flex-col items-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-200"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                    <p>No vouchers yet. <a href="{{ route('admin.vouchers.create') }}"
                                            class="text-blue-600 font-semibold hover:underline">Create your first
                                            voucher →</a></p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($vouchers->hasPages())
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $vouchers->links() }}
            </div>
        @endif
    </div>

    {{-- ── Copy modal (appears when Brave Shields blocks direct clipboard write) ──
         Guaranteed to work in ALL browsers — native text selection + Ctrl+C
    ──────────────────────────────────────────────────────────────────────────── --}}
    <div id="copy-modal" onclick="closeCopyModal(event)" style="display:none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-80 mx-4" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-bold text-gray-900">Copy Voucher Code</p>
                <button onclick="closeCopyModal()" class="text-gray-400 hover:text-gray-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <input id="copy-modal-input" type="text" readonly
                class="w-full px-4 py-3 rounded-xl border-2 border-indigo-300 bg-indigo-50 text-indigo-800 font-mono font-bold text-lg tracking-widest text-center focus:outline-none focus:border-indigo-500 cursor-text select-all">
            <p id="copy-modal-hint" class="mt-3 text-center text-xs text-gray-500">
                Press <kbd
                    class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-300 font-mono text-[11px]">Ctrl+C</kbd>
                to copy
            </p>
            <p id="copy-modal-success" class="mt-3 text-center text-xs text-emerald-600 font-bold hidden">
                ✓ Copied to clipboard!
            </p>
        </div>
    </div>

    <script>
        function copyCode(voucherId, btn) {
            const input = document.getElementById('code-input-' + voucherId);
            if (!input) return;
            const code = input.value;

            // ── Strategy 1: navigator.clipboard (modern, HTTPS / secure context) ──
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(code)
                    .then(() => showCopiedTip(btn))
                    .catch(() => openCopyModal(code));
                return;
            }

            // ── Strategy 2: execCommand (synchronous fallback) ─────────────
            const ta = document.createElement('textarea');
            ta.value = code;
            ta.style.cssText =
                'position:fixed;top:0;left:0;width:200px;height:40px;opacity:0.01;z-index:-1;border:none;outline:none;';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            ta.setSelectionRange(0, code.length);
            const ok = document.execCommand('copy');
            document.body.removeChild(ta);

            if (ok) {
                showCopiedTip(btn);
            } else {
                // ── Strategy 3: modal with pre-selected text (Brave Shields bypass) ──
                openCopyModal(code);
            }
        }

        function openCopyModal(code) {
            const modal = document.getElementById('copy-modal');
            const input = document.getElementById('copy-modal-input');
            const hint = document.getElementById('copy-modal-hint');
            const success = document.getElementById('copy-modal-success');

            input.value = code;
            hint.classList.remove('hidden');
            success.classList.add('hidden');
            modal.style.display = 'flex';

            // Auto-select text so user just presses Ctrl+C
            setTimeout(() => {
                input.focus();
                input.select();
                input.setSelectionRange(0, 99999);
            }, 50);

            // Listen for Ctrl+C / Cmd+C to show success feedback
            function onKey(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
                    hint.classList.add('hidden');
                    success.classList.remove('hidden');
                    setTimeout(closeCopyModal, 1000);
                    document.removeEventListener('keydown', onKey);
                }
            }
            document.addEventListener('keydown', onKey);
        }

        function closeCopyModal(e) {
            document.getElementById('copy-modal').style.display = 'none';
        }

        function showCopiedTip(btn) {
            btn.querySelector('.copy-tip')?.remove();
            const tip = document.createElement('span');
            tip.className = 'copy-tip';
            tip.textContent = 'Copied!';
            tip.style.cssText = [
                'position:absolute',
                'bottom:calc(100% + 6px)',
                'left:50%',
                'transform:translateX(-50%)',
                'background:#312e81',
                'color:#fff',
                'font-size:10px',
                'font-weight:700',
                'padding:3px 9px',
                'border-radius:6px',
                'white-space:nowrap',
                'pointer-events:none',
                'z-index:99',
                'opacity:1',
                'transition:opacity 0.3s ease',
            ].join(';');
            btn.appendChild(tip);
            btn.classList.add('text-indigo-500');
            setTimeout(() => {
                tip.style.opacity = '0';
                setTimeout(() => {
                    tip.remove();
                    btn.classList.remove('text-indigo-500');
                }, 300);
            }, 1400);
        }
    </script>

</x-admin-layout>
