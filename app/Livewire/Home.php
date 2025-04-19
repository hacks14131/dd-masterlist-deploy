<?php

namespace App\Livewire;

use App\Models\Masterlist;
use App\Models\MemberList;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class Home extends Component
{
    use WithPagination;

    public $search = '';
    public $barangayFilter = '';
    public $sortField = 'firstName';
    public $sortDirection = 'asc';
    public $showAddForm = false;
    
    // Form fields
    public $firstName = '';
    public $middleName = '';
    public $familyName = '';
    public $nameExtension = '';
    public $barangay = '';

    // show connections
    public $showViewModal      = false;
    public $selectedRecord     = null;
    public $selectedGroup      = [];

    public $masterList = [];
    public $forms = [];

    // edit feature
    public $showEditModal = false;
    public $editForm = [
        'id'            => null,
        'firstName'     => '',
        'middleName'    => '',
        'familyName'    => '',
        'nameExtension' => '',
        'barangay'      => '',
        'precinctNo'    => '',
        'leader'        => 0,
    ];

    protected $queryString = ['search', 'barangayFilter', 'sortField', 'sortDirection'];
    protected $listeners = ['refresh' => '$refresh'];

    protected function rules()
    {
        $rules = [];

        foreach ($this->forms as $i => $form) {
            $rules["forms.$i.firstName"]     = 'required|string';
            $rules["forms.$i.middleName"]    = 'nullable|string';
            $rules["forms.$i.familyName"]    = 'required|string';
            $rules["forms.$i.nameExtension"] = 'nullable|string';
            $rules["forms.$i.barangay"]      = 'required|string';
            $rules["forms.$i.precinctNo"]    = 'required|string';
            // <-- change this:
            $rules["forms.$i.leader"]        = 'required|boolean';
        }

        return $rules;
    }

    public function removeForm(int $index)
    {
        // drop that entry
        array_splice($this->forms, $index, 1);

        // re‑index so your keys stay 0,1,2...
        $this->forms = array_values($this->forms);
    }

    public function updateRecord()
    {
        $this->validate([
            'editForm.firstName'     => 'required|string',
            'editForm.middleName'    => 'nullable|string',
            'editForm.familyName'    => 'required|string',
            'editForm.nameExtension' => 'nullable|string',
            'editForm.barangay'      => 'required|string',
            'editForm.precinctNo'    => 'required|string',
            'editForm.leader'        => 'required|in:0,1',
        ]);

        $rec = Masterlist::findOrFail($this->editForm['id']);
        $rec->update([
            'firstName'     => $this->editForm['firstName'],
            'middleName'    => $this->editForm['middleName'],
            'familyName'    => $this->editForm['familyName'],
            'nameExtension' => $this->editForm['nameExtension'],
            'barangay'      => $this->editForm['barangay'],
            'precinctNo'    => $this->editForm['precinctNo'],
            'leader'        => $this->editForm['leader'],
        ]);

        session()->flash('message', 'Record updated successfully.');
        $this->hideEditModal();
    }

    public function hideEditModal()
    {
        $this->showEditModal = false;
    }

    public function editRecord(int $id)
    {
        $record = Masterlist::findOrFail($id);

        $this->editForm = [
            'id'            => $record->id,
            'firstName'     => $record->firstName,
            'middleName'    => $record->middleName,
            'familyName'    => $record->familyName,
            'nameExtension' => $record->nameExtension,
            'barangay'      => $record->barangay,
            'precinctNo'    => $record->precinctNo,
            'leader'        => $record->leader,
        ];

        $this->showEditModal = true;
    }

    public function deleteRecord(int $id)
    {
        try {
            DB::transaction(function() use ($id) {
                $record = Masterlist::findOrFail($id);

                if ($record->leader) {
                    // delete only this leader’s members
                    foreach ($record->members as $member) {
                        $member->delete();
                    }
                }

                // delete the record itself
                $record->delete();
            });

            session()->flash('message', 'Record deleted successfully.');
            return redirect('/home');

        } catch (\Throwable $th) {
            // log the exception
            Log::error('Error deleting Masterlist record: '.$th->getMessage());

            // optionally log the stack trace
            Log::error($th->getTraceAsString());

            session()->flash('message', 'An error occurred. Could not delete record.');
            return redirect()->back();
        }
    }

    public function selectRecord(int $id)
    {
        $this->selectedRecord = Masterlist::findOrFail($id);

        if ($this->selectedRecord->leader) {
            // leader → its members
            $this->selectedGroup = $this->selectedRecord
                                    ->members    // belongsToMany pivot from leader → members
                                    ->toArray();

        } else {
            // member → its leader(s), then that leader’s members
            $leaderModel = $this->selectedRecord
                                ->leaders()  // belongsToMany from member → leader
                                ->first();    // get the first (or only) leader

            if ($leaderModel) {
                $this->selectedGroup = collect([$leaderModel->toArray()])
                    ->merge(
                        $leaderModel
                            ->members  // pivot from leader → members
                            ->toArray()
                    )
                    ->all();
            } else {
                $this->selectedGroup = [];
            }
        }

        $this->showViewModal = true;
    }

    public function hideViewModal()
    {
        $this->showViewModal = false;
        $this->selectedRecord = null;
        $this->selectedGroup  = [];
    }

    private function makeFormData(string $role): array
    {
        return [
            'firstName'     => '',
            'middleName'    => '',
            'familyName'    => '',
            'nameExtension' => '',
            'barangay'      => '',
            'precinctNo'    => '',
            'leader'        => $role === 'Leader' ? 1 : 0,
        ];
    }

    public function addForm()
    {
        $this->forms[] = $this->makeFormData('Member');
    }

    public function mount()
    {
        $this->forms = [
            $this->makeFormData('Leader'),
        ];
    }

    public function downloadPdf() {
        // Ensure we have data to export
        if (empty($this->masterList)) {
            session()->flash('error', 'No records available to export.');
            return;
        }

        // Create a view for the PDF
        $pdf = Pdf::loadView('livewire.masterlist-pdf', [
            'masterList' => $this->masterList,
        ]);
        
        // Set paper size and orientation (optional)
        $pdf->setPaper('a4', 'landscape');
        
        // Return the PDF as a download
        return response()->streamDownload(
            fn () => print($pdf->output()),
            'masterlist_' . date('Y-m-d') . '.pdf'
        );
    }

    public function logout() {
        Auth::logout();

        // you can use the session() helper instead of injecting Request
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedBarangayFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function showForm()
    {
        $this->showAddForm = true;
        $this->resetForm();
    }

    public function hideForm()
    {
        $this->showAddForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->firstName = '';
        $this->middleName = '';
        $this->familyName = '';
        $this->nameExtension = '';
        $this->barangay = '';
        $this->resetErrorBag();
    }

    public function saveMasterlist()
    {
        try {
            // 1) Validate all sub‑forms
            $validated = $this->validate();

            // 2) Clean up and fingerprint
            $formEntries  = [];
            $fingerprints = [];
            foreach ($validated['forms'] as $i => $data) {
                $cleaned = [
                    'firstName'     => trim($data['firstName'] ?? ''),
                    'middleName'    => trim($data['middleName'] ?? ''),
                    'familyName'    => trim($data['familyName'] ?? ''),
                    'nameExtension' => trim($data['nameExtension'] ?? ''),
                    'barangay'      => trim($data['barangay'] ?? ''),
                    'precinctNo'    => trim($data['precinctNo'] ?? ''),
                    'leader'        => (isset($data['leader']) && $data['leader'] === 1) ? 1 : 0,
                ];
                $formEntries[] = $cleaned;

                $fp = strtolower(
                    $cleaned['firstName'] . '|'
                    . $cleaned['middleName'] . '|'
                    . $cleaned['familyName'] . '|'
                    . $cleaned['nameExtension']
                );
                if (in_array($fp, $fingerprints, true)) {
                    session()->flash('message', "Duplicate entry in form: {$cleaned['firstName']} {$cleaned['familyName']}.");
                    return redirect()->route('home');
                }
                $fingerprints[] = $fp;
            }

            // 3) Database duplicate check (by names+extension only)
            $exists = Masterlist::where(function($q) use ($formEntries) {
                foreach ($formEntries as $e) {
                    $q->orWhere(function($q2) use ($e) {
                        $q2->where('firstName',     $e['firstName'])
                        ->where('middleName',    $e['middleName'])
                        ->where('familyName',    $e['familyName'])
                        ->where('nameExtension', $e['nameExtension']);
                    });
                }
            })->exists();
            if ($exists) {
                session()->flash('message', "Person already registered with those name details.");
                return redirect()->route('home');;
            }

            // 4) Find the one leader entry and the zero‑or‑more member entries
            $leaders = array_filter($formEntries, fn($e) => $e['leader'] === 1);
            // dd($leaders);
            if (count($leaders) !== 1) {
                session()->flash('message', 'You must designate exactly one Leader.');
                return redirect()->route('home');;
            }
            $leaderData  = array_shift($leaders);
            $membersData = array_filter($formEntries, fn($e) => $e['leader'] === 0);

            // 5) Persist in a transaction
            DB::transaction(function() use ($leaderData, $membersData) {
                $leader = Masterlist::create($leaderData);

                $pivotRows = [];
                foreach ($membersData as $m) {
                    $member = Masterlist::create($m);
                    $pivotRows[] = [
                        'leaderId'   => $leader->id,
                        'memberId'   => $member->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if ($pivotRows) {
                    MemberList::insert($pivotRows);
                }
            });

            session()->flash('message', 'Leader and member records created successfully.');
            $this->hideForm();
            return redirect()->route('home');

        } catch (ValidationException $e) {
            session()->flash('message', implode(' ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            Log::error('Save error: ' . $e->getMessage());
            session()->flash('message', 'An error occurred. Please try again.');
        }
    }


    public function render()
    {
        // 1) Pull and uppercase distinct barangays for dropdown
        $barangays = Masterlist::select('barangay')
            ->distinct()
            ->orderBy('barangay')
            ->pluck('barangay')
            ->map(fn($b) => strtoupper($b));

        // 2) Build filtered query *with* eager‑loaded members
        $query = Masterlist::with('members');

        if (!empty($this->search)) {
            $pattern = '%' . $this->search . '%';

            $query->where(fn($q) => $q
                ->where('firstName',     'like', $pattern)
                ->orWhere('middleName',   'like', $pattern)
                ->orWhere('familyName',   'like', $pattern)
                ->orWhere('nameExtension','like', $pattern)
                ->orWhere('barangay',     'like', $pattern)
                ->orWhereRaw('SOUNDEX(firstName)     = SOUNDEX(?)', [$this->search])
                ->orWhereRaw('SOUNDEX(middleName)    = SOUNDEX(?)', [$this->search])
                ->orWhereRaw('SOUNDEX(familyName)    = SOUNDEX(?)', [$this->search])
                ->orWhereRaw('SOUNDEX(nameExtension) = SOUNDEX(?)', [$this->search])
            );
        }

        if (!empty($this->barangayFilter)) {
            $query->where('barangay', $this->barangayFilter);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        // 3) Paginate the results (models already have 'members' loaded)
        $paginator = $query->paginate(10)->withPath('/home');

        // 4) Uppercase every string attribute on each record
        $paginator->getCollection()->load('members')->transform(function($item) {
            foreach ($item->getAttributes() as $key => $value) {
                if (is_string($value)) {
                    $item->$key = strtoupper($value);
                }
            }
            return $item;
        });

        // 5) Ensure the loaded Collection includes members
        $collectionWithMembers = $paginator->getCollection()->load('members');

        // 6) Save the page's records (with members) as an array
        $this->masterList = $collectionWithMembers->toArray();
        $this->masterList = array_values(
            array_filter($this->masterList, fn($item) => $item['leader'] === 1)
        );

        // 7) Return view
        return view('livewire.home', [
            'records'   => $paginator,
            'barangays' => $barangays,
        ]);
    }

}