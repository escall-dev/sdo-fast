# Project Rules

### UI/UX Form Guidelines
- **Multi-select Fields**: When a user needs to select multiple options and Select2 (or similar libraries) are not in use, ALWAYS use a group of styled checkboxes (e.g., `form-check` in a grid or flex-wrap layout) instead of a native HTML `<select multiple>`.
- **Dependent Sections**: If a dynamic section (like a document checklist) depends on a sub-selection (e.g., "Mode of Travel"), do not render or show the section until that sub-selection is made. Hide the section completely or show a prompt like "Please select [X] to view requirements."

### Context & Terminology
- **"Transaction Type" / "Category" Editing**: Whenever the user talks about editing the requirements for a transaction type or category, ALWAYS default to looking at the Super Admin Settings (`views/settings/index.php`), not the user-facing submit form, unless explicitly stated otherwise.

### Refactoring & Data Migration
- **Hardcoded to Dynamic**: Whenever refactoring hardcoded frontend logic (like conditional checklists) into dynamic database-driven logic, ALWAYS write a database script to migrate the existing hardcoded mappings into the database. Do not just delete the hardcoded UI logic, as that will break existing behavior until the user manually configures the database.

### UI/UX Modal Consistency
- **Custom Confirmations**: NEVER use the native browser `window.confirm()` or `window.alert()` for user notifications or destructive action confirmations. ALWAYS use the system's built-in UI utilities (e.g., `API.confirmAction()` or `API.showToast()`) to maintain a consistent look and feel.
- **Destructive Actions**: Any action that deletes data from the screen or database (like removing a row from a list or deactivating an item) MUST first prompt the user with a confirmation modal before proceeding.
