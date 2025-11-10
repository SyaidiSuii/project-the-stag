@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-grid">
    <!-- Link to User Account -->
    <div class="form-group form-group-full">
        <label for="user_id" class="form-label">Select User</label>
        <select id="user_id" name="user_id" class="form-control" {{ $employee->exists ? 'disabled' : 'required' }}>
            <option value="">-- Select a User --</option>
            @foreach($users as $user)
                {{-- Only show users who don't already have an employee profile --}}
                @if(!$user->employee || $employee->user_id == $user->id)
                    <option value="{{ $user->id }}" {{ old('user_id', $employee->user_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endif
            @endforeach
        </select>
        @if($employee->exists)
            <small class="form-text">The user for an employee profile cannot be changed once created.</small>
        @else
            <small class="form-text">Select the system user to create an employee profile for.</small>
        @endif
    </div>

    <!-- Position -->
    <div class="form-group">
        <label for="position" class="form-label">Position</label>
        <input type="text" id="position" name="position" class="form-control" value="{{ old('position', $employee->position) }}" required placeholder="e.g., Head Chef, Waiter, Manager">
    </div>

    <!-- Salary -->
    <div class="form-group">
        <label for="salary" class="form-label">Salary (RM)</label>
        <input type="number" id="salary" name="salary" class="form-control" value="{{ old('salary', $employee->salary) }}" required step="0.01" min="0">
    </div>

    <!-- Hire Date -->
    <div class="form-group">
        <label for="hire_date" class="form-label">Hire Date</label>
        <input type="date" id="hire_date" name="hire_date" class="form-control" value="{{ old('hire_date', $employee->hire_date) }}" required>
    </div>

    <!-- Date of Birth -->
    <div class="form-group">
        <label for="date_of_birth" class="form-label">Date of Birth</label>
        <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $employee->date_of_birth) }}">
    </div>
    
    <!-- Phone Number -->
    <div class="form-group">
        <label for="phone_number" class="form-label">Work Phone Number (Optional)</label>
        <input type="tel" id="phone_number" name="phone_number" class="form-control" value="{{ old('phone_number', $employee->phone_number) }}">
    </div>

    <!-- Address -->
    <div class="form-group form-group-full">
        <label for="address" class="form-label">Address</label>
        <textarea id="address" name="address" class="form-control" rows="3">{{ old('address', $employee->address) }}</textarea>
    </div>

    <!-- Experience Details -->
    <div class="form-group form-group-full">
        <label for="experience_details" class="form-label">Experience Details</label>
        <textarea id="experience_details" name="experience_details" class="form-control" rows="4">{{ old('experience_details', $employee->experience_details) }}</textarea>
    </div>
</div>
