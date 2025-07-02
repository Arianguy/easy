# Converted Leads Read-Only Feature

## Overview

Once a lead is converted to an opportunity, it becomes read-only to maintain data integrity and prevent accidental modifications.

## How it Works

### Backend Logic

-   When a lead has `status = 'converted'`, the `LeadForm` component automatically enters read-only mode
-   The `isReadOnly()` method returns `true` for converted leads
-   The `save()` method prevents any modifications and shows an error message

### User Interface

-   **Header Changes**: Shows "View Lead (Converted)" instead of "Edit Lead"
-   **Visual Indicators**: Purple badge displays "Converted Lead - Read Only"
-   **Form Fields**: All input fields become grayed out and uneditable
-   **Save Button**: Hidden and replaced with explanatory text
-   **Styling**: Read-only fields have gray background and disabled cursor

### Form Fields Affected

-   Lead title
-   Status dropdown
-   Priority dropdown
-   Customer details (name, email, etc.)
-   Description
-   Notes
-   All other form fields

## Implementation Details

### Files Modified

1. `app/Livewire/Leads/LeadForm.php`

    - Added `$isConverted` property
    - Added `isReadOnly()` method
    - Modified `loadLead()` and `save()` methods

2. `resources/views/livewire/leads/lead-form.blade.php`
    - Updated header with conditional messaging
    - Applied read-only styling to form fields
    - Hidden save button for converted leads

### Testing

A test lead can be converted for testing purposes using:

```php
$lead = App\Models\Lead::first();
$lead->update(['status' => 'converted']);
```

Then navigate to `/leads/{id}/edit` to see the read-only interface.

## Benefits

-   **Data Integrity**: Prevents accidental modification of converted leads
-   **Clear UX**: Users understand why they cannot edit the lead
-   **Audit Trail**: Maintains original lead data for historical purposes
-   **Business Logic**: Enforces the rule that converted leads are immutable
