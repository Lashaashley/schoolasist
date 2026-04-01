<x-custom-admin-layout>
<div class="pd-ltr-20 xs-pd-20-10">

    <h6 class="mb-4">Add LPO</h6>

    <div class="card-box pd-20 height-100-p mb-5">
        <form id="add-lpo-form">
            @csrf

            {{-- LPO Number --}}
            <div class="form-group mb-3">
                <label>LPO Number:</label>
                <input type="text" name="lpo_number" id="lpo_number" class="form-control" readonly>
            </div>

            {{-- Supplier --}}
            <div class="form-group mb-3">
                <label>Supplier:</label>
                <select name="supplier_id" id="supplier_id" class="form-control" required>
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">
                            {{ $supplier->name }} - {{ $supplier->company }}
                        </option>
                    @endforeach
                </select>
            </div>

            <h6 class="mt-4 mb-3">Items</h6>

            <table class="table table-bordered" id="items-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Unit Price (KES)</th>
                        <th>Total</th>
                        <th width="60">
                            <button type="button" id="add-item" class="btn btn-success btn-sm">+</button>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>
                            <select name="items[0][category_id]" class="form-control category-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" name="items[0][product_name]" class="form-control" placeholder="Enter item name" required>
                        </td>
                        <td>
                            <input type="number" name="items[0][quantity]" class="form-control quantity" min="1" value="1" required>
                        </td>
                        <td>
                            <input type="number" name="items[0][price]" class="form-control price" step="0.01" required>
                        </td>
                        <td>
                            <input type="text" class="form-control total" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item">x</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary mt-3">Create LPO</button>
        </form>
    </div>

    {{-- List of existing LPOs --}}
    <h6 class="mb-3">Existing LPOs</h6>
    <div class="card-box pd-20 height-100-p mb-30">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>LPO Number</th>
                    <th>Supplier</th>
                    <th>Category</th>
                    <th>Item Supplied</th>
                    <th>Quantity</th>
                    <th>Unit Price (KES)</th>
                    <th>Total (KES)</th>
                    <th>Status</th>
                    <th>Actions</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lpos as $lpo)
                    @foreach($lpo->items as $item)
                        <tr>
                            <td>{{ $lpo->lpo_number }}</td>
                            <td>{{ $lpo->supplier->name }} - {{ $lpo->supplier->company }}</td>
                            <td>{{ $item->category->name ?? 'N/A' }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ number_format($item->total, 2) }}</td>
                            <td>
                                @if($lpo->status === 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td class="d-flex align-items-center">
                                <a href="{{ route('lpo.edit', $lpo->id) }}" class="btn btn-primary btn-sm me-2">Edit</a>

                                <form action="{{ route('lpo.destroy', $lpo->id) }}" method="POST" class="mb-0" onsubmit="return confirm('Are you sure you want to delete this LPO?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                            <td>{{ $lpo->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<script>
$(document).ready(function(){

    let index = 1;

    // Generate LPO number
    $.get('{{ route("lpo.generateNumber") }}', function(data){
        $('#lpo_number').val(data.lpo_number);
    });

    // Calculate row total
    function calculateTotal(row){
        let qty = parseFloat(row.find('.quantity').val()) || 0;
        let price = parseFloat(row.find('.price').val()) || 0;
        let total = qty * price;
        row.find('.total').val(total.toFixed(2));
    }

    // Trigger calculation
    $('#items-table').on('input','.quantity, .price',function(){
        let row = $(this).closest('tr');
        calculateTotal(row);
    });

    // Add new row
    $('#add-item').click(function(){
        let row = $('#items-table tbody tr:first').clone();
        row.find('input, select').each(function(){
            let name = $(this).attr('name');
            if(name){
                let newName = name.replace(/\[\d+\]/,'['+index+']');
                $(this).attr('name', newName);
            }
            if($(this).hasClass('quantity')){
                $(this).val(1);
            } 
            else if($(this).hasClass('price') || $(this).hasClass('total')){
                $(this).val('');
            }
            else{
                $(this).val('');
            }
        });
        $('#items-table tbody').append(row);
        index++;
    });

    // Remove row
    $('#items-table').on('click','.remove-item',function(){
        if($('#items-table tbody tr').length > 1){
            $(this).closest('tr').remove();
        }
    });

    // Submit form
    $('#add-lpo-form').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: '{{ route("lpo.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success:function(res){
                alert("LPO Created Successfully\nLPO Number: " + res.lpo_number);
                location.reload();
            },
            error:function(xhr){
                if(xhr.responseJSON && xhr.responseJSON.errors){
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key){
                        alert(errors[key][0]);
                    });
                } else {
                    alert("Something went wrong.");
                }
            }
        });
    });

});
</script>
</x-custom-admin-layout>