<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDoctorRequest;
use App\Models\Doctor;
use App\Models\Service;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('service')->orderBy('name')->paginate(15);
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        return view('admin.doctors.create', compact('services'));
    }

    public function store(StoreDoctorRequest $request)
    {
        Doctor::create($request->validated() + ['is_active' => $request->boolean('is_active', true)]);
        return redirect()->route('admin.doctors.index')->with('success', 'Doctor created.');
    }

    public function edit(Doctor $doctor)
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        return view('admin.doctors.edit', compact('doctor', 'services'));
    }

    public function update(StoreDoctorRequest $request, Doctor $doctor)
    {
        $doctor->update($request->validated() + ['is_active' => $request->boolean('is_active')]);
        return redirect()->route('admin.doctors.index')->with('success', 'Doctor updated.');
    }

    public function destroy(Doctor $doctor)
    {
        if ($doctor->tokens()->exists()) {
            return back()->with('error', 'Cannot delete: tokens exist for this doctor.');
        }
        $doctor->delete();
        return redirect()->route('admin.doctors.index')->with('success', 'Doctor deleted.');
    }
}
