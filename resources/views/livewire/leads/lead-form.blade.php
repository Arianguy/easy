<div>
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        @if($leadId)
            <!-- Edit Mode Header using Flux -->
            <flux:header>
                <flux:heading size="lg">
                    {{ $this->isReadOnly() ? 'View Lead (Converted)' : 'Edit Lead' }}
                </flux:heading>
                
                @if($this->isReadOnly())
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Converted Lead - Read Only
                    </div>
                @endif

                <flux:spacer />

                <flux:button :href="route('leads.index')" wire:navigate variant="ghost">
                    Back to Leads
                </flux:button>
            </flux:header>
        @else
            <!-- Create Mode Progress Indicator -->
            <div class="mb-8">
                <div class="flex items-center justify-center">
                    <div class="flex items-center space-x-4">
                        <!-- Step 1: Mobile -->
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                {{ $step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                1
                            </div>
                            <span class="ml-2 text-sm font-medium {{ $step >= 1 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                                Mobile
                            </span>
                        </div>

                        <div class="w-8 h-0.5 {{ $step >= 2 ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700' }}"></div>

                        <!-- Step 2: Customer -->
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                {{ $step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                                2
                            </div>
                            <span class="ml-2 text-sm font-medium {{ $step >= 2 ? 'text-blue-600' : 'text-gray-500' }}">
                                Customer
                            </span>
                        </div>

                        <div class="w-8 h-0.5 {{ $step >= 3 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>

                        <!-- Step 3: Lead -->
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                {{ $step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                                3
                            </div>
                            <span class="ml-2 text-sm font-medium {{ $step >= 3 ? 'text-blue-600' : 'text-gray-500' }}">
                                Lead Details
                            </span>
                        </div>
                    </div>
                </div>
        </div>
    @endif



    <form wire:submit.prevent="save">
            <!-- Step 1: Mobile Number Input -->
            @if ($step == 1)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Enter Mobile Number</h2>
                        <p class="text-gray-600 dark:text-gray-400">We'll check if this customer already exists in our system</p>
                    </div>

                    <div class="max-w-md mx-auto">
                        <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Mobile Number <span class="text-red-500">*</span>
                        </label>
                                                <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 text-lg font-medium">+91</span>
                            </div>
                                                        <input
                                type="tel"
                                id="mobile"
                                wire:model.live="mobile"
                                class="block w-full pl-16 pr-4 py-3 text-lg border rounded-md text-center bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 @error('mobile') border-red-300 focus:ring-red-500 focus:border-red-500 @else border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 @enderror"
                                placeholder="Enter 10 digit mobile number"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                maxlength="10"
                                autofocus
                            />
                            @error('mobile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if ($mobileCheckMessage)
                            <div class="mt-4 p-4 {{ $mobileExists ? 'bg-green-50 border border-green-200' : 'bg-blue-50 border border-blue-200' }} rounded-md">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        @if($mobileExists)
                                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium {{ $mobileExists ? 'text-green-800' : 'text-blue-800' }}">
                                            {{ $mobileExists ? 'Customer Found!' : 'New Customer' }}
                                        </h3>
                                        <p class="text-sm {{ $mobileExists ? 'text-green-700' : 'text-blue-700' }} mt-1">
                                            {{ $mobileCheckMessage }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mt-6">
                            <button
                                type="button"
                                wire:click="nextStep"
                                class="w-full py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 font-medium {{ strlen($mobile) < 10 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500' }}"
                                {{ strlen($mobile) < 10 ? 'disabled' : '' }}
                            >
                                {{ strlen($mobile) < 10 ? 'Enter 10 digits to continue' : 'Continue' }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 2: Customer Details -->
            @if ($step == 2)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            {{ $mobileExists ? 'Existing Customer Details' : 'Customer Information' }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            @if($mobileExists)
                                Customer details are read-only for existing customers. Only lead details can be modified.
                            @else
                                Please fill in the customer details
                            @endif
                        </p>
                    </div>

                    @if($mobileExists)
                        <!-- Existing Customer Details Card -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ trim($first_name . ' ' . $last_name) }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Existing Customer</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mobile Number</label>
                                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">+91 {{ $mobile }}</p>
                                    </div>

                                    @if($phone)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $phone }}</p>
                                    </div>
                                    @endif

                                    @if($email)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Email</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $email }}</p>
                                    </div>
                                    @endif

                                    @if($company)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Company</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $company }}</p>
                                    </div>
                                    @endif
                                </div>

                                <div class="space-y-3">
                                    @if(count($customer_interests) > 0)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Interests</label>
                                        <div class="mt-1 flex flex-wrap gap-1">
                                            @foreach($interests->filter(function($interest) use ($customer_interests) { return in_array($interest->id, $customer_interests); }) as $interest)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                      style="background-color: {{ $interest->color }}; color: {{ $interest->color }};">
                                                    {{ $interest->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    @if($age_group)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Age Group</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $ageGroups[$age_group] ?? $age_group }}</p>
                                    </div>
                                    @endif

                                    @if($address)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Address</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $address }}</p>
                                    </div>
                                    @endif

                                    @if($remarks)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $remarks }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            @if ($mobileCheckMessage)
                                <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-green-800">{{ $mobileCheckMessage }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- New Customer Form -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="first_name"
                                    wire:model="first_name"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="First Name"
                                />
                                @if($errors->has('first_name'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('first_name') }}</p>
                                @endif
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="last_name"
                                    wire:model="last_name"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Last Name"
                                />
                                @if($errors->has('last_name'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('last_name') }}</p>
                                @endif
                            </div>

                            <!-- Mobile (Editable for new customers) -->
                            <div>
                                <label for="mobile_step2" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mobile Number <span class="text-red-500">*</span>
                                    <span class="ml-2 text-xs text-gray-500">(Click to edit)</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-medium">+91</span>
                                    </div>
                                    <input
                                        type="tel"
                                        id="mobile_step2"
                                        wire:model="mobile"
                                        wire:blur="checkMobileOnBlur"
                                        class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 {{ $errors->has('mobile') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : '' }}"
                                        placeholder="Enter 10 digit mobile number"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        maxlength="10"
                                    />
                                </div>
                                @error('mobile')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                @if ($mobileCheckMessage)
                                    <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-blue-800">{{ $mobileCheckMessage }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number
                                </label>
                                <input
                                    type="tel"
                                    id="phone"
                                    wire:model="phone"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Phone Number"
                                />
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    wire:model="email"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Email Address"
                                />
                                @if($errors->has('email'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('email') }}</p>
                                @endif
                            </div>

                            <!-- Company -->
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700 mb-2">
                                    Company
                                </label>
                                <input
                                    type="text"
                                    id="company"
                                    wire:model="company"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Company Name"
                                />
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Address
                                </label>
                                <textarea
                                    id="address"
                                    wire:model="address"
                                    rows="3"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Full Address"
                                ></textarea>
                            </div>

                            <!-- Customer Interests -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Customer Interests
                                </label>
                                <div class="space-y-2">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($interests as $interest)
                                            <label class="inline-flex items-center cursor-pointer interest-tag"
                                                   data-interest-id="{{ $interest->id }}"
                                                   data-interest-color="{{ $interest->color }}">
                                                <input
                                                    type="checkbox"
                                                    wire:model.live="customer_interests"
                                                    value="{{ $interest->id }}"
                                                    class="sr-only interest-checkbox"
                                                    id="interest_{{ $interest->id }}"
                                                >
                                                <span class="px-3 py-1 rounded-full text-sm font-medium border-2 transition-all duration-200 select-none interest-span {{ in_array($interest->id, $customer_interests) ? 'text-white' : 'text-gray-700 hover:border-gray-400' }}"
                                                     @if(in_array($interest->id, $customer_interests))
                                                        style="background-color: {{ $interest->color }}; border-color: {{ $interest->color }};"
                                                     @else
                                                        style="border-color: #d1d5db;"
                                                     @endif>
                                                    {{ $interest->name }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-500">Select multiple interests that apply to this customer</p>
                                    @if(count($customer_interests) > 0)
                                        <div class="text-xs text-blue-600">
                                            Selected: {{ count($customer_interests) }} interest(s)
                                        </div>
                                    @endif

                                    {{-- Debug info --}}
                                    @if(config('app.debug'))
                                        <div class="text-xs text-gray-400 mt-1">
                                            Debug: {{ json_encode($customer_interests) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Age Group -->
                            <div>
                                <label for="age_group" class="block text-sm font-medium text-gray-700 mb-2">
                                    Age Group
                                </label>
                                <select
                                    id="age_group"
                                    wire:model="age_group"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Select Age Group</option>
                                    @foreach($ageGroups as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Remarks -->
                            <div>
                                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                                    Remarks
                                </label>
                                <textarea
                                    id="remarks"
                                    wire:model="remarks"
                                    rows="3"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Additional notes about the customer"
                                ></textarea>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between mt-6">
                        <button
                            type="button"
                            wire:click="previousStep"
                            class="bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                        >
                            Back
                        </button>
                        <button
                            type="button"
                            wire:click="nextStep"
                            class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Continue
                        </button>
                    </div>
                </div>
            @endif

            <!-- Step 3: Lead Details -->
            @if ($step == 3)
                @if($leadId)
                    <!-- Edit Mode: Two-column Layout using Flux -->
                    <div class="max-w-7xl mx-auto">
                        <form wire:submit.prevent="save">
                            <!-- Mobile: Customer Info on top, Desktop: Two-column layout -->
                            <div class="flex flex-col lg:flex-row lg:gap-4">
                                <!-- Customer Information Section (Right sidebar on desktop, top on mobile) -->
                                <div class="order-1 lg:order-2 lg:w-1/3">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 lg:p-4 mb-4 lg:mb-0">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Customer Information</h3>
                                        <div class="space-y-4">
                                            <!-- First Name -->
                                            <div>
                                                <flux:field>
                                                    <flux:label>First Name <span class="text-red-500">*</span></flux:label>
                                                    <flux:input
                                                        wire:model="first_name"
                                                        placeholder="First Name"
                                                        :readonly="$this->isReadOnly()"
                                                    />
                                                    <flux:error name="first_name" />
                                                </flux:field>
                                            </div>

                                            <!-- Last Name -->
                                            <div>
                                                <flux:field>
                                                    <flux:label>Last Name <span class="text-red-500">*</span></flux:label>
                                                    <flux:input
                                                        wire:model="last_name"
                                                        placeholder="Last Name"
                                                        :readonly="$this->isReadOnly()"
                                                    />
                                                    <flux:error name="last_name" />
                                                </flux:field>
                                            </div>

                                            <!-- Mobile (Read-only) -->
                                            <div>
                                                <flux:field>
                                                    <flux:label>Mobile Number</flux:label>
                                                    <flux:input
                                                        value="+91 {{ $mobile }}"
                                                        readonly
                                                    />
                                                </flux:field>
                                            </div>

                                            <!-- Email -->
                                            <div>
                                                <flux:field>
                                                    <flux:label>Email</flux:label>
                                                    <flux:input
                                                        wire:model="email"
                                                        type="email"
                                                        placeholder="Email Address"
                                                        :readonly="$this->isReadOnly()"
                                                    />
                                                    <flux:error name="email" />
                                                </flux:field>
                                            </div>

                                            <!-- Company -->
                                            <div>
                                                <flux:field>
                                                    <flux:label>Company</flux:label>
                                                    <flux:input
                                                        wire:model="company"
                                                        placeholder="Company Name"
                                                    />
                                                </flux:field>
                                            </div>

                                            <!-- Phone -->
                                            <div>
                                                <flux:field>
                                                    <flux:label>Phone Number</flux:label>
                                                    <flux:input
                                                        wire:model="phone"
                                                        type="tel"
                                                        placeholder="Phone Number"
                                                    />
                                                </flux:field>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lead Information Section (Main content on left) -->
                                <div class="order-2 lg:order-1 lg:flex-1">
                            <!-- Lead Information Section -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Lead Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Lead Number -->
                                    <div>
                                        <flux:field>
                                            <flux:label>Lead Number</flux:label>
                                            <flux:input
                                                value="{{ $leadId ? $lead_number : $next_lead_number }}"
                                                readonly
                                            />
                                        </flux:field>
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <flux:field>
                                            <flux:label>Status <span class="text-red-500">*</span></flux:label>
                                            <flux:select wire:model="status" :disabled="$this->isReadOnly()">
                                                <option value="new">New</option>
                                                <option value="contacted">Contacted</option>
                                                <option value="qualified">Qualified</option>
                                                <option value="converted">Converted</option>
                                                <option value="lost">Lost</option>
                                            </flux:select>
                                            <flux:error name="status" />
                                        </flux:field>
                                    </div>

                                    <!-- Priority -->
                                    <div>
                                        <flux:field>
                                            <flux:label>Priority <span class="text-red-500">*</span></flux:label>
                                            <flux:select wire:model="priority" :disabled="$this->isReadOnly()">
                                                <option value="low">Low</option>
                                                <option value="medium">Medium</option>
                                                <option value="high">High</option>
                                            </flux:select>
                                            <flux:error name="priority" />
                                        </flux:field>
                                    </div>

                                    <!-- Lead Source -->
                                    <div>
                                        <flux:field>
                                            <flux:label>Lead Source <span class="text-red-500">*</span></flux:label>
                                            <flux:select wire:model="source">
                                                <option value="">--Select--</option>
                                                @foreach($leadSources as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </flux:select>
                                            <flux:error name="source" />
                                        </flux:field>
                                    </div>

                                    <!-- Follow-up Date -->
                                    <div>
                                        <flux:field>
                                            <flux:label>Follow-up Date <span class="text-red-500">*</span></flux:label>
                                            <flux:input
                                                wire:model="follow_up_date"
                                                type="date"
                                                min="{{ date('Y-m-d') }}"
                                            />
                                            <flux:error name="follow_up_date" />
                                        </flux:field>
                                    </div>

                                    <!-- Campaign -->
                                    <div>
                                        <flux:field>
                                            <flux:label>Campaign</flux:label>
                                            <flux:select wire:model="campaign_id">
                                                <option value="">--Select--</option>
                                                @foreach($campaigns as $campaign)
                                                    <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                                @endforeach
                                            </flux:select>
                                        </flux:field>
                                    </div>

                                    <!-- Rating -->
                                    <div>
                                        <flux:field>
                                            <flux:label>Rating</flux:label>
                                            <flux:select wire:model="rating">
                                                <option value="">--Select--</option>
                                                @foreach($ratings as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </flux:select>
                                        </flux:field>
                                    </div>

                                    <!-- Estimated Value -->
                                    <div>
                                        <flux:field>
                                            <flux:label>Estimated Value</flux:label>
                                            <flux:input
                                                wire:model="estimated_value"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                placeholder="0.00"
                                            />
                                            <flux:error name="estimated_value" />
                                        </flux:field>
                                    </div>

                                    <!-- Expected Close Date -->
                                    <div>
                                        <flux:field>
                                            <flux:label>Expected Close Date</flux:label>
                                            <flux:input
                                                wire:model="expected_close_date"
                                                type="date"
                                                min="{{ date('Y-m-d') }}"
                                            />
                                            <flux:error name="expected_close_date" />
                                        </flux:field>
                                    </div>

                                    <!-- Assigned User -->
                                    <div>
                                        <flux:field>
                                            <flux:label>Owner <span class="text-red-500">*</span></flux:label>
                                            <flux:select wire:model="assigned_user_id">
                                                <option value="">--Select--</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </flux:select>
                                            <flux:error name="assigned_user_id" />
                                        </flux:field>
                                    </div>

                                    <!-- Member Type -->
                                    <div>
                                        <flux:field>
                                            <flux:label>Member Type</flux:label>
                                            <flux:select wire:model="member_type">
                                                <option value="standard">Standard</option>
                                                <option value="premium">Premium</option>
                                                <option value="vip">VIP</option>
                                            </flux:select>
                                        </flux:field>
                                    </div>

                                    <!-- Description -->
                                    <div class="md:col-span-3">
                                        <flux:field>
                                            <flux:label>Description</flux:label>
                                            <flux:textarea
                                                wire:model="description"
                                                rows="3"
                                                placeholder="Lead description and details"
                                                :readonly="$this->isReadOnly()"
                                            />
                                        </flux:field>
                                    </div>

                                    <!-- Notes -->
                                    <div class="md:col-span-3">
                                        <flux:field>
                                            <flux:label>Notes</flux:label>
                                            <flux:textarea
                                                wire:model="notes"
                                                rows="2"
                                                placeholder="Additional notes"
                                                :readonly="$this->isReadOnly()"
                                            />
                                        </flux:field>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end space-x-4 mt-6">
                                <flux:button
                                    type="button"
                                    :href="route('leads.index')"
                                    wire:navigate
                                    variant="ghost"
                                >
                                    Cancel
                                </flux:button>

                                @if(!$this->isReadOnly())
                                    <flux:button
                                        type="submit"
                                        variant="filled"
                                    >
                                        Update Lead
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- Create Mode: Original Lead Details Form -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                Lead Information
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400">
                                Complete the lead details
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Lead Number -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Lead Number
                                </label>
                                <div class="flex items-center space-x-2">
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 font-mono">
                                        {{ $next_lead_number }}
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        (Next available number)
                                    </span>
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="status"
                                    wire:model="status"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="new">New</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="qualified">Qualified</option>
                                    <option value="converted">Converted</option>
                                    <option value="lost">Lost</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Priority <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="priority"
                                    wire:model="priority"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Lead Source -->
                            <div>
                                <label for="source" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Lead Source <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="source"
                                    wire:model="source"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">--Select--</option>
                                    @foreach($leadSources as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('source')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Follow-up Date -->
                            <div>
                                <label for="follow_up_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Follow-up Date <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="follow_up_date"
                                    wire:model="follow_up_date"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                    min="{{ date('Y-m-d') }}"
                                />
                                @error('follow_up_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Campaign -->
                            <div>
                                <label for="campaign_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Campaign
                                </label>
                                <select
                                    id="campaign_id"
                                    wire:model="campaign_id"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">--Select--</option>
                                    @foreach($campaigns as $campaign)
                                        <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Rating -->
                            <div>
                                <label for="rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Rating
                                </label>
                                <select
                                    id="rating"
                                    wire:model="rating"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">--Select--</option>
                                    @foreach($ratings as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Estimated Value -->
                            <div>
                                <label for="estimated_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Estimated Value
                                </label>
                                <input
                                    type="number"
                                    id="estimated_value"
                                    wire:model="estimated_value"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                />
                                @error('estimated_value')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Expected Close Date -->
                            <div>
                                <label for="expected_close_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Expected Close Date
                                </label>
                                <input
                                    type="date"
                                    id="expected_close_date"
                                    wire:model="expected_close_date"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                    min="{{ date('Y-m-d') }}"
                                />
                                @error('expected_close_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Assigned User -->
                            <div>
                                <label for="assigned_user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Owner <span class="text-red-500">*</span>
                                </label>
                                <select
                                     id="assigned_user_id"
                                     wire:model="assigned_user_id"
                                     class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                 >
                                     <option value="">--Select--</option>
                                     @foreach($users as $user)
                                         <option value="{{ $user->id }}">{{ $user->name }}</option>
                                     @endforeach
                                 </select>
                                 @error('assigned_user_id')
                                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                 @enderror
                             </div>

                             <!-- Member Type -->
                             <div>
                                 <label for="member_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                     Member Type
                                 </label>
                                 <select
                                     id="member_type"
                                     wire:model="member_type"
                                     class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                 >
                                     <option value="standard">Standard</option>
                                     <option value="premium">Premium</option>
                                     <option value="vip">VIP</option>
                                 </select>
                             </div>

                             <!-- Description -->
                             <div class="md:col-span-2">
                                 <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                     Description
                                 </label>
                                 <textarea
                                     id="description"
                                     wire:model="description"
                                     rows="3"
                                     class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                     placeholder="Lead description and details"
                                 ></textarea>
                             </div>

                             <!-- Notes -->
                             <div class="md:col-span-2">
                                 <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                     Notes
                                 </label>
                                 <textarea
                                     id="notes"
                                     wire:model="notes"
                                     rows="2"
                                     class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                     placeholder="Additional notes"
                                 ></textarea>
                             </div>
                         </div>

                         <!-- Navigation Buttons -->
                         <div class="flex justify-between pt-6">
                             <button
                                 type="button"
                                 wire:click="previousStep"
                                 class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                             >
                                 Back
                             </button>
                             <button
                                 type="submit"
                                 class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                             >
                                 Create Lead
                             </button>
                         </div>
                     </div>
                 @endif
            @endif
    </form>

    <!-- Mobile-specific styles -->
    <style>
        @media (max-width: 768px) {
            .max-w-4xl {
                max-width: 100%;
            }

            .p-4 {
                padding: 1rem;
            }

            input[type="tel"]:focus,
            input[type="number"]:focus,
            input[type="email"]:focus,
            input[type="text"]:focus,
            input[type="date"]:focus,
            select:focus,
            textarea:focus {
                font-size: 16px; /* Prevents zoom on iOS */
            }

            /* Large touch targets for mobile */
            button {
                min-height: 44px;
            }

            input, select, textarea {
                min-height: 44px;
            }
        }

        /* Number pad for mobile input */
        input[type="tel"] {
            -webkit-appearance: none;
            -moz-appearance: textfield;
        }

        /* Interest tag styles */
        .interest-tag {
            transition: transform 0.1s ease;
        }

        .interest-tag:hover {
            transform: scale(1.02);
        }

        .interest-tag:active {
            transform: scale(0.98);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced interest selection
            document.addEventListener('click', function(e) {
                if (e.target.closest('.interest-tag')) {
                    const tag = e.target.closest('.interest-tag');
                    const checkbox = tag.querySelector('.interest-checkbox');
                    const span = tag.querySelector('.interest-span');
                    const interestId = tag.dataset.interestId;
                    const interestColor = tag.dataset.interestColor;

                    // Toggle checkbox
                    checkbox.checked = !checkbox.checked;

                    // Trigger Livewire update
                    checkbox.dispatchEvent(new Event('input', { bubbles: true }));

                    // Update visual state immediately for better UX
                    if (checkbox.checked) {
                        span.style.backgroundColor = interestColor;
                        span.style.borderColor = interestColor;
                        span.style.color = 'white';
                        span.classList.remove('text-gray-700', 'hover:border-gray-400');
                        span.classList.add('text-white');
                    } else {
                        span.style.backgroundColor = '';
                        span.style.borderColor = '#d1d5db';
                        span.style.color = '';
                        span.classList.remove('text-white');
                        span.classList.add('text-gray-700', 'hover:border-gray-400');
                    }
                }
            });
        });

        // Listen for Livewire updates to refresh interest states
        document.addEventListener('livewire:updated', function() {
            // Update interest tag states after Livewire updates
            document.querySelectorAll('.interest-tag').forEach(function(tag) {
                const checkbox = tag.querySelector('.interest-checkbox');
                const span = tag.querySelector('.interest-span');
                const interestColor = tag.dataset.interestColor;

                if (checkbox.checked) {
                    span.style.backgroundColor = interestColor;
                    span.style.borderColor = interestColor;
                    span.style.color = 'white';
                    span.classList.remove('text-gray-700', 'hover:border-gray-400');
                    span.classList.add('text-white');
                } else {
                    span.style.backgroundColor = '';
                    span.style.borderColor = '#d1d5db';
                    span.style.color = '';
                    span.classList.remove('text-white');
                    span.classList.add('text-gray-700', 'hover:border-gray-400');
                }
            });
        });
    </script>
    </div>
</div>
