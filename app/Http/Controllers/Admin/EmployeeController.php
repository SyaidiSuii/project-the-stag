<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('user')->latest()->paginate(10);
        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employee = new Employee();
        // Eager load 'employee' relationship to filter out users who are already employees
        $users = User::whereDoesntHave('employee')->orderBy('name')->get();
        return view('admin.employees.create', compact('employee', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id|unique:employees,user_id',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'experience_details' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
        ]);

        // Phone number processing
        if ($request->filled('phone_number')) {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $numberProto = $phoneUtil->parse($request->phone_number, 'MY');
                if ($phoneUtil->isValidNumber($numberProto)) {
                    $validatedData['phone_number'] = $phoneUtil->format($numberProto, PhoneNumberFormat::E164);
                } else {
                    return back()->withErrors(['phone_number' => 'Invalid phone number format.'])->withInput();
                }
            } catch (NumberParseException $e) {
                return back()->withErrors(['phone_number' => 'Invalid phone number: ' . $e->getMessage()])->withInput();
            }
        }

        Employee::create($validatedData);

        return redirect()->route('admin.employees.index')->with('message', 'Employee record has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        // For the edit form, we need the current user of the employee profile,
        // and also other users who are not yet employees, in case of a mistake.
        // However, since we disable changing user, we only need the current user.
        // For simplicity, we'll just pass the single user associated with the employee.
        $users = User::where('id', $employee->user_id)->get();
        return view('admin.employees.edit', compact('employee', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validatedData = $request->validate([
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'experience_details' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
        ]);

        // Phone number processing
        if ($request->filled('phone_number')) {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $numberProto = $phoneUtil->parse($request->phone_number, 'MY');
                if ($phoneUtil->isValidNumber($numberProto)) {
                    $validatedData['phone_number'] = $phoneUtil->format($numberProto, PhoneNumberFormat::E164);
                } else {
                    return back()->withErrors(['phone_number' => 'Invalid phone number format.'])->withInput();
                }
            } catch (NumberParseException $e) {
                return back()->withErrors(['phone_number' => 'Invalid phone number: ' . $e->getMessage()])->withInput();
            }
        } else {
            $validatedData['phone_number'] = null;
        }

        $employee->update($validatedData);

        return redirect()->route('admin.employees.index')->with('message', 'Employee record has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('message', 'Employee record has been deleted.');
    }
}
