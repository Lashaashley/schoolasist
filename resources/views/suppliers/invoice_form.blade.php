<div class="container">

    <h4 class="page-title">Invoice Submission</h4>

    <!-- LPO Summary -->
    <div class="card">
        <h5>LPO Details</h5>
        <div class="flex-row">
            <div class="flex-col">
                <label>LPO Number</label>
                <input type="text" value="{{ $invitation->lpo->id ?? 'N/A' }}" readonly>
            </div>
            <div class="flex-col">
                <label>Supplier</label>
                <input type="text" value="{{ $invitation->supplier->name ?? '' }} - {{ $invitation->supplier->company ?? '' }}" readonly>
            </div>
            <div class="flex-col">
                <label>Category</label>
                <input type="text" value="{{ $invitation->category ?? 'N/A' }}" readonly>
            </div>
        </div>
        <div class="flex-row">
            <div class="flex-col">
                <label>LPO Date</label>
                <input type="text" value="{{ optional($invitation->lpo->created_at)->format('d M Y') ?? '-' }}" readonly>
            </div>
            <div class="flex-col">
                <label>Expiry Date</label>
                <input type="text" value="{{ optional($invitation->expires_at)->format('d M Y') ?? '-' }}" readonly>
            </div>
        </div>
    </div>

    <!-- LPO Items Table -->
    <div class="card">
        <h5>LPO Items</h5>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invitation->lpo->items ?? [] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>KES {{ number_format($item->unit_price, 2) }}</td>
                            <td>KES {{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right">Grand Total</td>
                        <td>KES {{ number_format($invitation->amount ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Invoice Submission Form -->
    <div class="card">
        <h5>Submit Your Invoice</h5>
        <form action="{{ route('supplier.invoice.submit', $invitation->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex-row">
                <div class="flex-col">
                    <label>Invoice Number *</label>
                    <input type="text" name="invoice_number" placeholder="Enter invoice number" required>
                </div>
                <div class="flex-col">
                    <label>Invoice Date *</label>
                    <input type="date" name="invoice_date" required>
                </div>
            </div>

            <div class="flex-row" style="margin-top:10px;">
                <div class="flex-col">
                    <label>Attachment (Optional)</label>
                    <input type="file" name="attachment">
                </div>
                <div class="flex-col">
                    <label>Description (Optional)</label>
                    <textarea name="description" rows="2" placeholder="Enter description"></textarea>
                </div>
            </div>

            <div style="margin-top:15px;">
                <button type="submit" class="btn-primary">Submit Invoice</button>
            </div>
        </form>
    </div>

</div>

<style>
/* Basic resets */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f7;
    margin: 0;
    padding: 0;
}

/* Container */
.container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 0 15px;
}

/* Page title */
.page-title {
    color: #333;
    margin-bottom: 20px;
}

/* Card style */
.card {
    background-color: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

/* Flex layout for rows */
.flex-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
}

/* Columns */
.flex-col {
    flex: 1 1 300px;
    min-width: 200px;
}

/* Form elements */
label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
    color: #555;
}

input[type="text"], input[type="date"], textarea, input[type="file"] {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
}

textarea {
    resize: vertical;
}

/* Table styling */
.table-wrapper {
    overflow-x:auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

table th, table td {
    border: 1px solid #ddd;
    padding: 8px;
    font-size: 14px;
}

table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

table tfoot td {
    font-weight: bold;
}

.text-right {
    text-align: right;
}

/* Button */
.btn-primary {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}

.btn-primary:hover {
    background-color: #0056b3;
}

/* Responsive tweaks */
@media (max-width: 768px) {
    .flex-col {
        flex: 1 1 100%;
    }

    table th, table td {
        font-size: 12px;
        padding: 6px;
    }
}
</style>