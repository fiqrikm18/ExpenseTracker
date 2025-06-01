<table id="pnl-table">
    <thead id="pnl-table-head">
    <tr>
        <th width="20" bgcolor="yellow">Transaction Date</th>
        <th width="20" bgcolor="yellow">Description</th>
        <th width="20" bgcolor="yellow">COA</th>
        <th width="20" bgcolor="yellow">Amount</th>
        <th width="20" bgcolor="yellow">Transaction Type</th>
    </tr>
    </thead>
    <tbody id="pnl-table-body">
    @foreach($transactions as $transaction)
        <tr>
            <td>{{$transaction['created_at']}}</td>
            <td>{{$transaction['description']}}</td>
            <td>{{$transaction['coa_name']}}</td>
            <td>{{$transaction['amount']}}</td>
            <td>{{$transaction['type']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
