<x-custom-admin-layout>
<div class="container">
    <h2>Pay Fees</h2>

    @if(session('alert'))
        <div class="alert alert-success">{{ session('alert') }}</div>
    @endif

    <form action="{{ route('mpesa.stkPush') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Parent Phone Number</label>
            <input type="text" name="phone" class="form-control" placeholder="2547XXXXXXXX" required>
        </div>

        <div class="form-group">
            <label>Student</label>
            <select name="student_info" class="form-control" required>
                <option value="">Select Student</option>
                @foreach($students as $student)
                    {{-- Combine admission number and stream safely --}}
                    <option value="{{ $student->admno }}-{{ $student->stream }}">
                        {{ $student->sirname }} {{ $student->othername }} ({{ $student->admno }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Amount</label>
            <input type="number" name="amount" class="form-control" placeholder="Amount" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Pay</button>
    </form>
</div>
</x-custom-admin-layout>