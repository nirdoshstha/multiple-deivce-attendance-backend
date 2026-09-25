<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\LeaveApprovedMail;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Exists;
use Krbaidik\AdBsConverter\Facades\NepaliDate;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LeaveApplicationController extends BackendBaseController implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:leave-applications.index', only: ['index']),
            new Middleware('permission:leave-applications.show', only: ['show']),
            new Middleware('permission:leave-applications.store', only: ['store']),
            new Middleware('permission:leave-applications.edit', only: ['edit']),
            new Middleware('permission:leave-applications.update', only: ['update']),
            new Middleware('permission:leave-applications.destroy', only: ['destroy']),
        ];
    }
    private $model;
    protected $panel = "Leave Application";

    public function __construct()
    {
        $this->model = new LeaveApplication();
    }

    public function index()
    {
        $leave_applications = LeaveApplication::with('leaveType', 'user', 'role')->get();
        $leave_type = LeaveType::get();

        return response()->json([
            'leave_applications' => $leave_applications,
            'leave_type' => $leave_type
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {

    //     // 1. Get Leave Type
    //     $leaveType = LeaveType::findOrFail($request->leave_type_id);

    //     $leave_type_total_days = (float) $leaveType->days_per_year;


    //     // 2. Parse Nepali/BS dates
    //     $leave_start_date = NepaliDate::parse($request->date_from);
    //     $leave_end_date   = NepaliDate::parse($request->date_to);


    //     // 3. Calculate current application days
    //     $leave_application_days =
    //         $leave_start_date->diffInDays($leave_end_date) + 1;


    //     // 4. Get already applied leave days for this user + leave type
    //     $userId = $request->user_id;
    //     $leaveTypeId = $request->leave_type_id;

    //     $sumDays = LeaveApplication::where('user_id', $userId)
    //         ->where('leave_type_id', $leaveTypeId)
    //         ->sum('total_days');

    //     $sumDays = (float) $sumDays;

    //     // 5. Calculate total after this application
    //     $total_leave_days = $sumDays + $leave_application_days;

    //     // 6. Validation for From Date & To Date Check overlapping leave dates

    //     $overlappingLeave = LeaveApplication::where('user_id', $userId)
    //         // ->where('leave_type_id', $leaveTypeId)
    //         ->where(function ($query) use ($request) {
    //             $query->where('date_from', '<=', $request->date_to)
    //                 ->where('date_to', '>=', $request->date_from);
    //         })
    //         ->exists();

    //     // 7. Validate
    //     $validated = $request->validate([
    //         'user_id' => [
    //             'required',
    //         ],

    //         'role_id' => [
    //             'required',
    //         ],

    //         'leave_type_id' => [
    //             'required',
    //             'exists:leave_types,id',

    //             function ($attribute, $value, $fail) use (
    //                 $sumDays,
    //                 $leave_application_days,
    //                 $total_leave_days,
    //                 $leave_type_total_days,
    //                 $leaveType

    //             ) {

    //                 // Current application itself is greater than allowed limit
    //                 if ($leave_application_days > $leave_type_total_days && $leaveType->is_paid) {
    //                     $fail(
    //                         "You are applying for {$leave_application_days} days, "
    //                             . "but this leave type allows only {$leave_type_total_days} days per year."
    //                     );

    //                     return;
    //                 }

    //                 // Existing + new application exceeds yearly limit
    //                 if ($total_leave_days > $leave_type_total_days && $leaveType->is_paid) {

    //                     $remainingDays = $leave_type_total_days - $sumDays;

    //                     $fail(
    //                         "You have already applied for {$sumDays} days. "
    //                             . "You can apply for only {$remainingDays} more days. "
    //                             . "This leave type allows {$leave_type_total_days} days per year."
    //                     );
    //                 }
    //             },
    //         ],

    //         'day_type' => [
    //             'required'
    //         ],


    //         'date_from' => [
    //             'required',
    //             'string',
    //             function ($attribute, $value, $fail) use ($overlappingLeave) {

    //                 if ($overlappingLeave) {
    //                     $fail(
    //                         'You have already applied for leave on one or more of the selected dates.'
    //                     );
    //                 }
    //             },
    //         ],

    //         'date_to' => [
    //             'required',
    //             'string',
    //             function ($attribute, $value, $fail) use ($overlappingLeave) {

    //                 if ($overlappingLeave) {
    //                     $fail(
    //                         'You have already applied for leave on one or more of the selected dates.'
    //                     );
    //                 }
    //             },

    //         ],
    //         'reason' => [
    //             'required',
    //             'string',
    //         ],

    //     ], [
    //         'user_id.required' => 'Please select User.',
    //         'role_id.required' => 'Please select User Role.',
    //         'leave_type_id.required' => 'Please select Leave Type.',
    //         'day_type.required' => 'Please select Day Type.',
    //         'date_from.required' => 'Please select leave start date.',
    //         'date_to.required' => 'Please select leave end date.',
    //         'reason.required' => 'Please enter reason'
    //     ]);


    //     $leaveType = LeaveType::find($validated['leave_type_id']);

    //     $leave_application = $this->model->create([
    //         'user_id' => $validated['user_id'],
    //         'role_id' => $validated['role_id'],
    //         'leave_type_id' => $validated['leave_type_id'],
    //         'day_type' => $request['day_type'],
    //         'date_from' => $request->date_from,
    //         'date_to' => $request->date_to,
    //         'total_days' => $leave_application_days ?? 0,
    //         'reason' => $request->reason,
    //         'created_by' => auth('sanctum')->user()->id
    //     ]);

    //     return response()->json([
    //         'status' => 200,
    //         'message' => $this->panel . ' stored successfully'
    //     ]);
    // }


    public function store(Request $request)
    {
        // 1. Basic shape validation first
        $request->validate([
            'user_id'       => ['required'],
            'role_id'       => ['required'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'day_type'      => ['required'],
            'date_from'     => ['required', 'string'],
            'date_to'       => ['required', 'string'],
            'reason'        => ['required', 'string'],
        ], [
            'user_id.required' => 'Please select User.',
            'role_id.required' => 'Please select User Role.',
            'leave_type_id.required' => 'Please select Leave Type.',
            'leave_type_id.exists' => 'Selected leave type is invalid.',
            'day_type.required' => 'Please select Day Type.',
            'date_from.required' => 'Please select leave start date.',
            'date_to.required' => 'Please select leave end date.',
            'reason.required' => 'Please enter reason',
        ]);

        // 2. Now safe to parse dates / query DB
        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $leave_type_total_days = (float) $leaveType->days_per_year;

        try {
            $leave_start_date = NepaliDate::parse($request->date_from);
            $leave_end_date   = NepaliDate::parse($request->date_to);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'date_to' => ['Invalid date format supplied.'],
            ]);
        }

        $leave_application_days = $leave_start_date->diffInDays($leave_end_date) + 1;

        if ($leave_application_days < 1) {
            throw ValidationException::withMessages([
                'date_to' => ['End date must be on or after start date.'],
            ]);
        }

        $userId = $request->user_id;
        $leaveTypeId = $request->leave_type_id;

        $sumDays = (float) LeaveApplication::where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->sum('total_days');

        $total_leave_days = $sumDays + $leave_application_days;

        $overlappingLeave = LeaveApplication::where('user_id', $userId)
            ->where(function ($query) use ($request) {
                $query->where('date_from', '<=', $request->date_to)
                    ->where('date_to', '>=', $request->date_from);
            })
            ->exists();

        // 3. Business-rule validation — collect into standard array-of-strings shape
        $errors = [];

        if ($leave_application_days > $leave_type_total_days && $leaveType->is_paid) {
            $errors['leave_type_id'] = [
                "You are applying for {$leave_application_days} days, "
                    . "but this leave type allows only {$leave_type_total_days} days per year."
            ];
        } elseif ($total_leave_days > $leave_type_total_days && $leaveType->is_paid) {
            $remainingDays = $leave_type_total_days - $sumDays;
            $errors['leave_type_id'] = [
                "You have already applied for {$sumDays} days. "
                    . "You can apply for only {$remainingDays} more days. "
                    . "This leave type allows {$leave_type_total_days} days per year."
            ];
        }

        if ($overlappingLeave) {
            $errors['date_from'] = ['You have already applied for leave on one or more of the selected dates.'];
            $errors['date_to']   = ['You have already applied for leave on one or more of the selected dates.'];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        // 4. Create
        $leave_application = $this->model->create([
            'user_id'       => $request->user_id,
            'role_id'       => $request->role_id,
            'leave_type_id' => $request->leave_type_id,
            'day_type'      => $request->day_type,
            'date_from'     => $request->date_from,
            'date_to'       => $request->date_to,
            'total_days'    => $leave_application_days,
            'reason'        => $request->reason,
            'created_by'    => auth('sanctum')->user()->id,
        ]);

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' stored successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $leave_application = $this->model->with('leaveType', 'user', 'role')->find($id);
        return response()->json([
            'leave_application' => $leave_application
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // public function isApproved(Request $request, $id)
    // {
    //     $leave = $this->model->find($id);   // find the actual record via the model

    //     // return $leave->role->name;
    //     $staff_email = $leave->user?->email ;

    //     $details = [
    //         "name"=> $leave->user?->name,
    //         "email"=>$leave->user?->email,
    //         "staff"=>$leave->role->name,
    //         "approval_remarks" => $leave->approval_remarks

    //     ];
    //     // return $staff_details;

    //     Mail::to($staff_email)->send(new LeaveApprovedMail($details));


    //     if (!$leave) {
    //         return response()->json([
    //             'message' => 'Leave application not found'
    //         ], 404);
    //     }




    //    $leave->update([
    //         'approved_by' => auth('sanctum')->user()->id,
    //         'approval_remarks' => $request->approval_remarks,
    //         'is_approved' => $request->is_approved,
    //         'approved_at' => now()
    //     ]);



    //     // if($is_leave_approved){
    //     //     Mail::to($request->user())->send(new MailableClass);
    //     // }

    //     return response()->json([
    //         'message' => 'Is Approved saved successfully'
    //     ]);
    // }
    public function isApproved(Request $request, $id)
{
    $leave = $this->model->with(['user', 'role'])->find($id);

    // Check first
    if (!$leave) {
        return response()->json([
            'message' => 'Leave application not found'
        ], 404);
    }

    // Update leave first
    $leave->update([
        'approved_by' => auth('sanctum')->user()->id,
        'approval_remarks' => $request->approval_remarks,
        'is_approved' => $request->is_approved,
        'approved_at' => now()
    ]);

    // Get staff email
    $staffEmail = $leave->user?->email;

    // Send email only if email exists
    if ($staffEmail) {

        $details = [
            'name' => $leave->user?->name,
            'email' => $leave->user?->email,
            'staff' => $leave->role?->name,
            'approval_remarks' => $leave->approval_remarks,
            'is_approved' => $leave->is_approved,
        ];

        Mail::to($staffEmail)->send(
            new LeaveApprovedMail($details)
        );
    }

    return response()->json([
        'message' => 'Leave approval saved successfully'
    ]);
}
}
