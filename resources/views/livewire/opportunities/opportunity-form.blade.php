<div>
    <flux:header>
        <flux:heading size="lg">
            {{ $isEditing ? 'Edit Opportunity' : 'Create Opportunity' }}
        </flux:heading>

        <flux:spacer />

        <flux:button :href="route('opportunities.index')" wire:navigate variant="ghost">
            Back to Opportunities
        </flux:button>
    </flux:header>

    <div class="max-w-4xl mx-auto">
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Opportunity Name -->
                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:label>Opportunity Name <span class="text-red-500">*</span></flux:label>
                            <flux:input
                                wire:model="name"
                                placeholder="Enter opportunity name"
                            />
                            <flux:error name="name" />
                        </flux:field>
                    </div>

                    <!-- Value -->
                    <div>
                        <flux:field>
                            <flux:label>Value ($) <span class="text-red-500">*</span></flux:label>
                            <flux:input
                                wire:model="value"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                            />
                            <flux:error name="value" />
                        </flux:field>
                    </div>

                    <!-- Stage -->
                    <div>
                        <flux:field>
                            <flux:label>Stage <span class="text-red-500">*</span></flux:label>
                            <flux:select wire:model.live="stage">
                                <option value="prospecting">Prospecting</option>
                                <option value="proposal">Proposal</option>
                                <option value="negotiation">Negotiation</option>
                                <option value="won">Won</option>
                                <option value="lost">Lost</option>
                            </flux:select>
                            <flux:error name="stage" />
                        </flux:field>
                    </div>

                    <!-- Probability -->
                    <div>
                        <flux:field>
                            <flux:label>Probability (%) <span class="text-red-500">*</span></flux:label>
                            <flux:input
                                wire:model="probability"
                                type="number"
                                min="0"
                                max="100"
                                placeholder="25"
                            />
                            <flux:error name="probability" />
                        </flux:field>
                    </div>

                    <!-- Expected Close Date -->
                    <div>
                        <flux:field>
                            <flux:label>Expected Close Date</flux:label>
                                                        <flux:input
                                wire:model="expected_close_date"
                                type="date"
                            />
                            <flux:error name="expected_close_date" />
                        </flux:field>
                    </div>

                    <!-- Actual Close Date (only show for won/lost) -->
                    @if(in_array($stage, ['won', 'lost']))
                        <div>
                            <flux:field>
                                <flux:label>Actual Close Date</flux:label>
                                <flux:input
                                    wire:model="actual_close_date"
                                    type="date"
                                />
                                <flux:error name="actual_close_date" />
                            </flux:field>
                        </div>
                    @endif

                    <!-- Branch -->
                    <div>
                        <flux:field>
                            <flux:label>Branch <span class="text-red-500">*</span></flux:label>
                            <flux:select wire:model.live="branch_id">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="branch_id" />
                        </flux:field>
                    </div>

                    <!-- Associated Lead -->
                    <div>
                        <flux:field>
                            <flux:label>Associated Lead <span class="text-red-500">*</span></flux:label>
                            <flux:select wire:model="lead_id">
                                <option value="">Select Lead</option>
                                @if($branch_id)
                                    @foreach($leads as $lead)
                                        <option value="{{ $lead->id }}">
                                            {{ $lead->title }}
                                            @if($lead->customer)
                                                ({{ $lead->customer->name }})
                                            @endif
                                        </option>
                                    @endforeach
                                @endif
                            </flux:select>
                            <flux:error name="lead_id" />
                            @if(!$branch_id)
                                <flux:description>Please select a branch first to see available leads</flux:description>
                            @endif
                        </flux:field>
                    </div>

                    <!-- Products/Services -->
                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:label>Products/Services</flux:label>
                            <flux:input
                                wire:model="products_services"
                                placeholder="Enter products or services (comma separated)"
                            />
                            <flux:description>Separate multiple items with commas</flux:description>
                            <flux:error name="products_services" />
                        </flux:field>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:label>Description</flux:label>
                            <flux:textarea
                                wire:model="description"
                                rows="4"
                                placeholder="Enter opportunity description..."
                            />
                            <flux:error name="description" />
                        </flux:field>
                    </div>

                    <!-- Close Reason (only show for won/lost) -->
                    @if(in_array($stage, ['won', 'lost']))
                        <div class="md:col-span-2">
                            <flux:field>
                                <flux:label>Close Reason</flux:label>
                                <flux:input
                                    wire:model="close_reason"
                                    placeholder="Reason for {{ $stage === 'won' ? 'winning' : 'losing' }} this opportunity"
                                />
                                <flux:error name="close_reason" />
                            </flux:field>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Calculated Values Display -->
            @if($value && $probability)
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-4">
                    <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Calculated Values</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">Opportunity Value:</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-100 ml-2">
                                ${{ number_format($value, 2) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">Weighted Value:</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-100 ml-2">
                                ${{ number_format(($value * $probability) / 100, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4">
                <flux:button
                    type="button"
                    wire:click="cancel"
                    variant="ghost"
                >
                    Cancel
                </flux:button>

                <flux:button
                    type="submit"
                    variant="filled"
                >
                    {{ $isEditing ? 'Update Opportunity' : 'Create Opportunity' }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
