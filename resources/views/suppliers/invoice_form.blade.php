<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding-top: 50px; }
        .card { max-width: 700px; margin: auto; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="card">
    <h4 class="mb-4">Submit Your Invoice</h4>

    <form id="supplier-invoice-form" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="invitation_id" value="{{ $invitation->id }}">

        <div class="mb-3">
            <label class="form-label">Supplier</label>
            <input type="text" class="form-control" value="{{ $invitation->supplier->name }} - {{ $invitation->supplier->company }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" class="form-control" value="{{ ucfirst($invitation->category) }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Invoice Number</label>
            <input type="text" name="invoice_number" class="form-control" required>
            <small class="text-danger" id="invoice_number-error"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">Invoice Date</label>
            <input type="date" name="invoice_date" class="form-control" required>
            <small class="text-danger" id="invoice_date-error"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">Due Date</label>
            <input type="date" name="due_date" class="form-control" required>
            <small class="text-danger" id="due_date-error"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">Total Amount</label>
            <input type="number" step="0.01" name="total_amount" class="form-control" required>
            <small class="text-danger" id="total_amount-error"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">Description (Optional)</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Attachment (Optional, PDF/JPG/PNG)</label>
            <input type="file" name="attachment" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit Invoice</button>
    </form>

    <div id="status-message" class="alert mt-3" style="display:none;"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    $('#supplier-invoice-form').submit(function(e){
        e.preventDefault();
        $('#supplier-invoice-form small.text-danger').text('');
        $('#status-message').hide();

        let formData = new FormData(this);

        $.ajax({

            url: "{{ route('supplier.invoice.submit', $invitation->id) }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                $('#status-message').removeClass('alert-danger').addClass('alert-success')
                    .text(res.message).show();
                $('#supplier-invoice-form')[0].reset();
            },
            error: function(xhr){
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value){
                        $('#' + key + '-error').text(value[0]);
                    });
                } else {
                    $('#status-message').removeClass('alert-success').addClass('alert-danger')
                        .text('Something went wrong.').show();
                }
            }
        });
    });

});
</script>

</body>
</html>