<div x-data="iosDateFilter({
    initialDate: '{{ request('date') }}'
})" class="relative inline-block" {{-- Changed w-full to inline-block to prevent container stretching --}} @role="region">
    {{-- Trigger Button --}}
    <button type="button" @click="open = !open"
        class="inline-flex items-center justify-between w-[240px] min-w-[240px] px-5 py-3 bg-white border border-gray-200 rounded-[12px] shadow-sm hover:border-blue-500 hover:shadow-md transition-all group active:scale-[0.98]">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-[15px] font-medium text-gray-700" x-text="displayText || 'Filter by Date'"></span>
        </div>
        <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- iOS Style Calendar Modal Overlay --}}
    <template x-teleport="body">
        <div x-show="open" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 overflow-hidden"
            x-cloak>

            {{-- Backdrop shadow --}}
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-[2px]"></div>

            {{-- Centered Picker Card - FIXED SIZE APPLIED HERE --}}
            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4" {{-- We use w-[340px] and min-w to prevent shrinking, and max-w-[95vw] as a safety guard for ultra-small phones --}}
                class="relative w-[340px] min-w-[340px] max-w-[95vw] bg-white rounded-[24px] shadow-[0_20px_70px_rgba(0,0,0,0.3)] border border-gray-100 overflow-hidden"
                @click.away="open = false">

                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-50">
                    <button type="button" @click="showMonthYearPicker = !showMonthYearPicker"
                        class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 px-3 py-1.5 rounded-xl transition-all group">
                        <span class="text-[17px] font-bold text-gray-900"
                            x-text="(showMonthYearPicker ? viewYear : viewMonthName + ' ' + viewYear)"></span>
                        <svg class="w-4 h-4 text-blue-500 transition-transform duration-200"
                            :class="showMonthYearPicker ? 'rotate-90' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-3">
                        <button @click="prevMonth()" type="button"
                            class="p-2 hover:bg-gray-100 rounded-full text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button @click="nextMonth()" type="button"
                            class="p-2 hover:bg-gray-100 rounded-full text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="relative min-h-[280px]"> {{-- Slightly increased min-height for consistent spacing --}}
                    <div x-show="!showMonthYearPicker" x-transition.opacity>
                        <div class="grid grid-cols-7 px-4 mt-2">
                            <template x-for="day in weekdays" :key="day">
                                <div class="text-center text-[11px] font-extrabold text-gray-400 py-1 uppercase tracking-widest"
                                    x-text="day"></div>
                            </template>
                        </div>

                        <div class="grid grid-cols-7 px-4 pb-4">
                            <template x-for="(dateObj, index) in days" :key="index">
                                <div class="aspect-square flex items-center justify-center">
                                    <button @click="selectDay(dateObj.date)" type="button"
                                        class="w-9 h-9 flex items-center justify-center rounded-full text-[15px] font-semibold transition-all relative"
                                        :class="{
                                            'text-gray-900 hover:bg-gray-100': dateObj.isCurrentMonth && !dateObj
                                                .isSelected,
                                            'text-gray-300 pointer-events-none': !dateObj.isCurrentMonth,
                                            'bg-blue-600 text-white shadow-lg shadow-blue-600/30': dateObj.isSelected,
                                            'text-blue-600 font-bold': dateObj.isToday && !dateObj.isSelected
                                        }"
                                        :disabled="!dateObj.isCurrentMonth">
                                        <span x-text="dateObj.day"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div x-show="showMonthYearPicker" x-transition.opacity
                        class="absolute inset-0 bg-white grid grid-cols-3 gap-2 p-5">
                        <template x-for="(month, index) in months" :key="month">
                            <button type="button" @click="selectMonth(index)"
                                class="py-3.5 rounded-2xl text-sm font-bold transition-all"
                                :class="index === viewDate.getMonth() ? 'bg-blue-600 text-white shadow-md' :
                                    'text-gray-600 hover:bg-gray-50'"
                                x-text="month">
                            </button>
                        </template>
                    </div>
                </div>

                <div x-show="showError" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-5 py-3 mx-4 mt-2 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-[12px] font-medium text-red-600 leading-tight" x-text="errorMessage"></p>
                </div>

                {{-- Footer --}}
                <div class="px-5 py-4 flex gap-3 border-t border-gray-50 bg-white">
                    <button @click="clear()" type="button"
                        class="flex-1 py-3 text-[14px] font-bold text-gray-500 hover:text-gray-900 transition-colors">
                        Clear
                    </button>
                    <button @click="apply()" type="button"
                        class="flex-[2] py-3 bg-blue-600 text-white rounded-2xl text-[14px] font-bold shadow-[0_10px_20px_-5px_rgba(37,99,235,0.4)] hover:bg-blue-700 transition-all active:scale-[0.98]">
                        Apply Filter
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('iosDateFilter', (config) => ({
            open: false,
            selectedDate: '',
            displayText: '',
            showError: false,
            errorMessage: '',

            viewDate: new Date(),
            viewMonthName: '',
            viewYear: '',
            showMonthYearPicker: false,
            months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                'Dec'
            ],
            weekdays: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
            days: [],

            init() {
                const urlParams = new URLSearchParams(window.location.search);
                const dateParam = urlParams.get('date');

                if (dateParam) {
                    this.selectedDate = dateParam;
                    this.viewDate = new Date(dateParam);
                    this.updateDisplay();
                } else {
                    this.viewDate = new Date();
                }

                this.generateCalendar();
            },

            updateDisplay() {
                if (!this.selectedDate) {
                    this.displayText = '';
                    return;
                }
                const date = new Date(this.selectedDate);
                const options = {
                    day: '2-digit',
                    month: '2-digit',
                    year: '2-digit'
                };
                this.displayText = date.toLocaleDateString('en-GB', options);
            },

            generateCalendar() {
                if (!(this.viewDate instanceof Date) || isNaN(this.viewDate)) {
                    this.viewDate = new Date();
                }

                const year = this.viewDate.getFullYear();
                const month = this.viewDate.getMonth();

                this.viewYear = year;
                this.viewMonthName = new Intl.DateTimeFormat('en-US', {
                    month: 'long'
                }).format(this.viewDate);

                const firstDayOfMonth = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const daysInPrevMonth = new Date(year, month, 0).getDate();

                const newDays = [];

                for (let i = firstDayOfMonth - 1; i >= 0; i--) {
                    newDays.push({
                        day: daysInPrevMonth - i,
                        date: '',
                        isCurrentMonth: false,
                        isToday: false
                    });
                }

                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const selectedDateOnly = this.selectedDate ? this.selectedDate.split(' ')[0] : '';

                for (let i = 1; i <= daysInMonth; i++) {
                    const dateStr =
                        `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                    const current = new Date(year, month, i);
                    current.setHours(0, 0, 0, 0);

                    newDays.push({
                        day: i,
                        date: dateStr,
                        isCurrentMonth: true,
                        isToday: current.getTime() === today.getTime(),
                        isSelected: dateStr === selectedDateOnly
                    });
                }

                const remainingCells = 42 - newDays.length;
                for (let i = 1; i <= remainingCells; i++) {
                    newDays.push({
                        day: i,
                        date: '',
                        isCurrentMonth: false,
                        isToday: false
                    });
                }
                this.days = newDays;
            },

            prevMonth() {
                if (this.showMonthYearPicker) {
                    this.viewDate.setFullYear(this.viewDate.getFullYear() - 1);
                } else {
                    this.viewDate.setMonth(this.viewDate.getMonth() - 1);
                }
                this.generateCalendar();
            },

            nextMonth() {
                if (this.showMonthYearPicker) {
                    this.viewDate.setFullYear(this.viewDate.getFullYear() + 1);
                } else {
                    this.viewDate.setMonth(this.viewDate.getMonth() + 1);
                }
                this.generateCalendar();
            },

            selectMonth(monthIndex) {
                this.viewDate.setMonth(monthIndex);
                this.showMonthYearPicker = false;
                this.generateCalendar();
            },

            selectDay(date) {
                this.selectedDate = date;
                this.showError = false; // Clear error when a new date is picked
                this.updateDisplay();
                this.generateCalendar();
            },



            apply() {
                this.showError = false;
                
                if (this.selectedDate) {
                    const selected = new Date(this.selectedDate);
                    const now = new Date();
                    now.setHours(23, 59, 59, 999); 

                    if (selected > now) {
                        this.errorMessage = 'Invalid Date: You cannot filter for future dates as no order data exists yet.';
                        this.showError = true;
                        return;
                    }
                }

                this.open = false;
                const dateParam = this.selectedDate ? `${this.selectedDate}` : '';
                
                if (dateParam) {
                    window.location.href = window.location.pathname + '?date=' + dateParam;
                } else {
                    window.location.href = window.location.pathname;
                }
            },

            clear() {
                this.selectedDate = '';
                this.showError = false;
                this.updateDisplay();
                this.open = false;
                window.location.href = window.location.pathname;
            }
        }));
    });
</script>
