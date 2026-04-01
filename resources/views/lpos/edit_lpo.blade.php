<x-custom-admin-layout>
<div class="pd-ltr-20 xs-pd-20-10">

    <h6>Edit LPO: {{ $lpo->lpo_number }}</h6>

    <div class="card-box pd-20 height-100-p mb-30">
        <form id="edit-lpo-form" action="{{ route('lpo.update', $lpo->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- LPO Number --}}
            <div class="form-group">
                <label>LPO Number:</label>
                <input type="text" name="lpo_number" class="form-control" value="{{ $lpo->lpo_number }}" readonly>
            </div>

            {{-- Supplier --}}
            <div class="form-group">
                <label>Supplier:</label>
                <select name="supplier_id" class="form-control" required>
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $supplier->id == $lpo->supplier_id ? 'selected' : '' }}>
                            {{ $supplier->name }} - {{ $supplier->company }}
                        </option>
                    @endforeach
                </select>
            </div>

            <h6>Items</h6>
            <table class="table table-bordered" id="items-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Unit Price (KES)</th>
                        <th>Total</th>
                        <th width="50">
                            <button type="button" id="add-item" class="btn btn-success btn-sm">+</button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lpo->items as $index => $item)
                    <tr>
                        <td>
                            <select name="items[{{ $index }}][category_id]" class="form-control category-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $cat->id == $item->category_id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" name="items[{{ $index }}][product_name]" class="form-control" value="{{ $item->product_name }}" required>
                        </td>
                        <td>
                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" min="1" value="{{ $item->quantity }}" required>
                        </td>
                        <td>
                            <input type="number" name="items[{{ $index }}][price]" class="form-control price" step="0.01" value="{{ $item->unit_price }}" required>
                        </td>
                        <td>
                            <input type="text" class="form-control total" value="{{ number_format($item->total, 2) }}" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item">x</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary">Update LPO</button>
        </form>
    </div>
</div>

<script>
$(document).ready(function(){

    let index = {{ $lpo->items->count() }};

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

});
</script>
</x-custom-admin-layout>