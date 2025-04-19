<div class="home-main-container">
    <div class="home-main-container">
        <nav class="home-navbar">
            <div class="home-navbar__brand">
            Dashboard
            </div>
            <div class="home-navbar__menu">
            <button wire:click="logout" class="home-navbar__logout">
                Logout
            </button>
            </div>
        </nav>

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="home-alert home-alert--success" role="alert">
                <p>{{ session('message') }}</p>
            </div>
        @endif
    </div>

    <div class="home-card">
        <div class="home-header">
            <h2 class="home-title">Masterlist Records</h2>
            <div class="home-filters">
                <!-- Search Field -->
                <div class="home-filters">
                    <!-- Search Field -->
                    <div class="home-input-group">
                        <label for="search" class="sr-only">Search records</label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search records…"
                            class="home-input home-input--full" />
                    </div>

                    <!-- Filter by Barangay -->
                    <div class="home-input-group">
                        <label for="barangayFilter" class="sr-only">Filter by Barangay</label>
                        <select
                            id="barangayFilter"
                            name="barangayFilter"
                            wire:model.live="barangayFilter"
                            class="home-input">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $brgy)
                                <option value="{{ $brgy }}">{{ $brgy }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Add Button -->
                <button
                    type="button"
                    wire:click="showForm"
                    class="home-btn home-btn--primary home-input-group">
                    Add New Record
                </button>
                <button wire:click="downloadPdf" class="btn btn-primary home-input-group">
                    <i class="fas fa-download"></i> Download PDF
                </button>
            </div>
        </div>

        <!-- Add Form Modal -->
        @if ($showAddForm)
            <div class="home-modal-overlay">
                <div class="home-modal">
                    <h3 class="home-modal-title text-center">New Record</h3>

                    <form wire:submit.prevent="saveMasterlist">
                        @foreach($forms as $index => $form)
                            <div class="home-form-section" wire:key="form-{{ $index }}">
                                @if($index > 0)
                                    <button
                                        type="button"
                                        wire:click="removeForm({{ $index }})"
                                        class="home-btn btn-danger m-2"
                                        style="float: right;"
                                        title="Remove this member">
                                        &times;
                                    </button>
                                @endif
                                <h4 class="home-subtitle">
                                    {{ $index === 0 
                                        ? 'Leader' 
                                        : "Member $index" 
                                    }}
                                </h4>
                                <!-- First Name -->
                                <div class="home-form-group">
                                    <label for="forms.{{ $index }}.firstName" class="home-label">
                                        First Name <span class="home-required">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="forms.{{ $index }}.firstName"
                                        wire:model.defer="forms.{{ $index }}.firstName"
                                        class="home-input" />
                                    @error("forms.{$index}.firstName")
                                        <span class="home-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Middle Name -->
                                <div class="home-form-group">
                                    <label for="forms.{{ $index }}.middleName" class="home-label">
                                        Middle Name
                                    </label>
                                    <input
                                        type="text"
                                        id="forms.{{ $index }}.middleName"
                                        wire:model.defer="forms.{{ $index }}.middleName"
                                        class="home-input" />
                                    @error("forms.{$index}.middleName")
                                        <span class="home-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Family Name -->
                                <div class="home-form-group">
                                    <label for="forms.{{ $index }}.familyName" class="home-label">
                                        Family Name <span class="home-required">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="forms.{{ $index }}.familyName"
                                        wire:model.defer="forms.{{ $index }}.familyName"
                                        class="home-input" />
                                    @error("forms.{$index}.familyName")
                                        <span class="home-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Name Extension -->
                                <div class="home-form-group">
                                    <label for="forms.{{ $index }}.nameExtension" class="home-label">
                                        Name Extension
                                    </label>
                                    <input
                                        type="text"
                                        id="forms.{{ $index }}.nameExtension"
                                        wire:model.defer="forms.{{ $index }}.nameExtension"
                                        class="home-input"
                                        placeholder="Jr., Sr., III" />
                                    @error("forms.{$index}.nameExtension")
                                        <span class="home-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Barangay -->
                                <div class="home-form-group">
                                    <label for="forms.{{ $index }}.barangay" class="home-label">
                                        Barangay <span class="home-required">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="forms.{{ $index }}.barangay"
                                        wire:model.defer="forms.{{ $index }}.barangay"
                                        class="home-input" />
                                    @error("forms.{$index}.barangay")
                                        <span class="home-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Precinct No. -->
                                <div class="home-form-group">
                                    <label for="forms.{{ $index }}.precinctNo" class="home-label">
                                        Precinct No. <span class="home-required">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="forms.{{ $index }}.precinctNo"
                                        wire:model.defer="forms.{{ $index }}.precinctNo"
                                        class="home-input" />
                                    @error("forms.{$index}.precinctNo")
                                        <span class="home-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Role (auto‑assigned) -->
                                <div class="home-form-group">
                                    <label class="home-label">Role</label>
                                    <input
                                        type="hidden"
                                        wire:model.defer="forms.{{ $index }}.leader"
                                    />
                                    <span class="home-role-display">
                                        {{ $index === 0 ? 'Leader' : 'Member' }}
                                    </span>
                                </div>

                                <hr class="my-4" />
                            </div>
                        @endforeach

                        @error('duplicate')
                            <div class="home-error my-2">{{ $message }}</div>
                        @enderror

                        <div class="home-form-actions mt-6">
                            <button
                                type="button"
                                wire:click="hideForm"
                                class="home-btn home-btn--secondary">
                                Cancel
                            </button>
                            <button
                                type="button"
                                wire:click="addForm"
                                class="home-btn home-btn--secondary">
                                + Add Another
                            </button>
                            <button
                                type="submit"
                                class="home-btn home-btn--primary">
                                Save Record(s)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Data Table -->
        <div class="home-table-wrapper">
            <table class="home-table">
                <thead>
                    <tr class="text-center">
                        <th>
                            Action
                        </th>
                        <th wire:click="sortBy('firstName')">
                            First Name @if($sortField=='firstName')<span>{!! $sortDirection=='asc'?'↑':'↓' !!}</span>@endif
                        </th>
                        <th wire:click="sortBy('middleName')">
                            Middle Name @if($sortField=='middleName')<span>{!! $sortDirection=='asc'?'↑':'↓' !!}</span>@endif
                        </th>
                        <th wire:click="sortBy('familyName')">
                            Family Name @if($sortField=='familyName')<span>{!! $sortDirection=='asc'?'↑':'↓' !!}</span>@endif
                        </th>
                        <th wire:click="sortBy('nameExtension')">
                            Extension @if($sortField=='nameExtension')<span>{!! $sortDirection=='asc'?'↑':'↓' !!}</span>@endif
                        </th>
                        <th wire:click="sortBy('barangay')">
                            Barangay @if($sortField=='barangay')<span>{!! $sortDirection=='asc'?'↑':'↓' !!}</span>@endif
                        </th>
                        <th wire:click="sortBy('precinctNo')">
                            Precinct No. @if($sortField=='precinctNo')<span>{!! $sortDirection=='asc'?'↑':'↓' !!}</span>@endif
                        </th>
                        <th wire:click="sortBy('leader')">
                            Leader/Member @if($sortField=='leader')<span>{!! $sortDirection=='asc'?'↑':'↓' !!}</span>@endif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td class="home-form-actions">
                                <button
                                    type="button"
                                    class="home-btn home-btn--secondary home-btn--sm"
                                    wire:click.stop="selectRecord({{ $record->id }})" style="cursor: pointer;">
                                    Detail
                                </button>
                                <button
                                    type="button"
                                    class="home-btn home-btn--secondary home-btn--sm"
                                    wire:click="editRecord({{ $record->id }})">
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    wire:click="deleteRecord({{ $record->id }})"
                                    class="home-btn home-btn--danger home-btn--sm"
                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">
                                    Delete
                                </button>
                            </td>
                            <td>{{ $record->firstName }}</td>
                            <td>{{ $record->middleName }}</td>
                            <td>{{ $record->familyName }}</td>
                            <td>{{ $record->nameExtension }}</td>
                            <td>{{ $record->barangay }}</td>
                            <td>{{ $record->precinctNo }}</td>
                            <td>{{ $record->leader ? 'Leader' : 'Member' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="home-no-records text-center">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($showViewModal && $selectedRecord !== null)
                <div class="home-modal-overlay" wire:keydown.escape="hideViewModal">
                    <div class="home-modal">
                        <button class="home-modal-close" wire:click="hideViewModal">×</button>

                        <h3 class="home-modal-title">
                            {{ strtoupper($selectedRecord['leader'] ? 'Leader Details' : 'Member Details') }}
                        </h3>

                        <div class="home-form-group">
                            <strong>{{ strtoupper('Name:') }}</strong>
                            {{ strtoupper($selectedRecord['firstName']) }}
                            {{ strtoupper($selectedRecord['middleName']) }}
                            {{ strtoupper($selectedRecord['familyName']) }}
                            {{ strtoupper($selectedRecord['nameExtension']) }}
                        </div>

                        <div class="home-form-group">
                            <strong>{{ strtoupper('Barangay:') }}</strong>
                            {{ strtoupper($selectedRecord['barangay']) }}
                        </div>

                        <div class="home-form-group">
                            <strong>{{ strtoupper('Precinct No.:') }}</strong>
                            {{ strtoupper($selectedRecord['precinctNo']) }}
                        </div>

                        <hr/>

                        <h4 class="home-subtitle">
                            {{ strtoupper($selectedRecord['leader'] ? 'Members' : 'Leader & Peers') }}
                        </h4>

                        <ul class="home-list">
                            @foreach($selectedGroup as $person)
                                @if($person['id'] !== $selectedRecord['id'])
                                    <li>
                                        {{ strtoupper($person['firstName']) }}
                                        {{ strtoupper($person['middleName']) }}
                                        {{ strtoupper($person['familyName']) }}
                                        {{ strtoupper($person['nameExtension']) }}
                                        ({{ strtoupper($person['leader'] ? 'Leader' : 'Member') }})
                                        – {{ strtoupper($person['precinctNo']) }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>

                        <div class="home-form-actions">
                            <button class="home-btn home-btn--secondary" wire:click="hideViewModal">
                                {{ strtoupper('Close') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>
        @if($showEditModal)
            <div class="home-modal-overlay">
                <div class="home-modal">
                    <button class="home-modal-close" wire:click="hideEditModal">×</button>
                    <h3 class="home-modal-title text-center">Edit Record</h3>

                    <form wire:submit.prevent="updateRecord">
                        <!-- First Name -->
                        <div class="home-form-group">
                            <label for="editFirstName" class="home-label">
                                First Name <span class="home-required">*</span>
                            </label>
                            <input
                                type="text"
                                id="editFirstName"
                                wire:model.defer="editForm.firstName"
                                class="home-input" />
                            @error('editForm.firstName')
                                <span class="home-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Middle Name -->
                        <div class="home-form-group">
                            <label for="editMiddleName" class="home-label">
                                Middle Name
                            </label>
                            <input
                                type="text"
                                id="editMiddleName"
                                wire:model.defer="editForm.middleName"
                                class="home-input" />
                            @error('editForm.middleName')
                                <span class="home-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Family Name -->
                        <div class="home-form-group">
                            <label for="editFamilyName" class="home-label">
                                Family Name <span class="home-required">*</span>
                            </label>
                            <input
                                type="text"
                                id="editFamilyName"
                                wire:model.defer="editForm.familyName"
                                class="home-input" />
                            @error('editForm.familyName')
                                <span class="home-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Name Extension -->
                        <div class="home-form-group">
                            <label for="editNameExtension" class="home-label">
                                Name Extension
                            </label>
                            <input
                                type="text"
                                id="editNameExtension"
                                wire:model.defer="editForm.nameExtension"
                                class="home-input"
                                placeholder="Jr., Sr., III" />
                            @error('editForm.nameExtension')
                                <span class="home-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Barangay -->
                        <div class="home-form-group">
                            <label for="editBarangay" class="home-label">
                                Barangay <span class="home-required">*</span>
                            </label>
                            <input
                                type="text"
                                id="editBarangay"
                                wire:model.defer="editForm.barangay"
                                class="home-input" />
                            @error('editForm.barangay')
                                <span class="home-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Precinct No. -->
                        <div class="home-form-group">
                            <label for="editPrecinctNo" class="home-label">
                                Precinct No. <span class="home-required">*</span>
                            </label>
                            <input
                                type="text"
                                id="editPrecinctNo"
                                wire:model.defer="editForm.precinctNo"
                                class="home-input" />
                            @error('editForm.precinctNo')
                                <span class="home-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="home-form-actions mt-6">
                            <button
                                type="button"
                                wire:click="hideEditModal"
                                class="home-btn home-btn--secondary">
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="home-btn home-btn--primary">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
        <!-- Pagination -->
        <div class="home-pagination">
            {{ $records->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
