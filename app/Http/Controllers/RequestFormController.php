<?php

// app/Http/Controllers/RequestFormController.php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use App\Models\RequestForm;
use App\Services\ContactService;
use Illuminate\Support\Facades\Auth;

class RequestFormController extends Controller
{
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
        $this->middleware(['auth', 'permission:view request forms'])->only('index', 'show');
        $this->middleware(['auth', 'permission:create request forms'])->only('create', 'store');
        $this->middleware(['auth', 'permission:edit request forms'])->only('edit', 'update');
        $this->middleware(['auth', 'permission:delete request forms'])->only('destroy');
    }

    /**
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->can('view request forms')) {
            $contacts = RequestForm::with('user', 'module')->latest()->paginate(10);
        } else {
            $contacts = Auth::user()->contacts()->with('module')->latest()->paginate(10);
        }

        return view('request_forms.index', compact('contacts'));
    }

    /**
     * Display the form to submit a request.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $modules = Module::all();
        return view('request_forms.create', [
            'formTypes' => ContactService::getFormTypes(),
            'modules' => $modules,
        ]);
    }

    /**
     * Store a newly created request in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu
        $rules = [
            'type' => ['required', 'integer', 'in:1,2,3,4'],
            'content' => ['required', 'string', 'max:1000'],
            'module_id' => ['required', 'exists:modules,id'],
        ];
        $request->validate($rules);

        $this->contactService->createContact($request->all(), Auth::user());

        return redirect()->back()->with('success', 'Yêu cầu của bạn đã được gửi thành công!');
    }

    /**
     * Show the form for editing the specified contact.
     *
     * @param  \App\Models\RequestForm  $contact
     * @return \Illuminate\View\View
     */
    public function edit(RequestForm $contact)
    {
        if (Auth::user()->id !== $contact->user_id) {
            abort(403);
        }

        $modules = Module::all();

        return view('request_forms.edit', [
            'contact' => $contact,
            'formTypes' => ContactService::getFormTypes(),
            'modules' => $modules,
            'statuses' => ContactService::getStatus(),
        ]);
    }

    /**
     * Update the specified contact in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RequestForm  $contact
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, RequestForm $contact)
    {
        if (Auth::user()->id !== $contact->user_id) {
            abort(403);
        }

        $rules = [
            'type' => ['required', 'integer', 'in:' . implode(',', array_keys(ContactService::getFormTypes()))],
            'content' => ['required', 'string', 'max:1000'],
            'module_id' => ['nullable', 'exists:modules,id'],
            'status' => ['required', 'integer', 'in:' . implode(',', array_keys(ContactService::getStatus()))],
        ];

        $request->validate($rules);

        $this->contactService->updateContact($contact, $request->all(), Auth::user());

        return redirect()->route('request_forms.index')->with('success', 'Yêu cầu liên hệ đã được cập nhật thành công!');
    }
}
