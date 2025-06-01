<table id="pnl-table">
    <thead id="pnl-table-head">
    <tr>
        <th bgcolor="yellow" rowspan="2" valign="center" align="center">Category</th>
        @for($i = 1; $i <= $currentMonth; $i++)
            <th bgcolor="yellow" align="center">{{$currentYear}}-{{str_pad($i, 2, '0', STR_PAD_LEFT)}}</th>
        @endfor
    </tr>
    <tr>
        @for($i = 1; $i <= $currentMonth; $i++)
            <th bgcolor="yellow" align="center">Amount</th>
        @endfor
    </tr>
    </thead>
    <tbody id="pnl-table-body">
    @foreach($transactionReport['income'] as $income)
        <tr>
            <td align="center">{{$income['coa_category_name']}}</td>

            @for($i = 1; $i <= $currentMonth; $i++)
                @php
                $row = array_values(array_filter($income['amount_data'], function($item) use($i) {
                   return $item['month'] == ($i);
                }));
                @endphp

                @if (count($row) > 0)
                    <td align="center">{{$row[0]['amount']}}</td>
                @else
                    <td align="center">0</td>
                @endif
            @endfor
        </tr>
    @endforeach
    <tr>
        <td bgcolor="#009F08" width="12" align="center">Total Income</td>
        @for($i = 1; $i <= $currentMonth; $i++)
            @php
                $totalIncome = 0;
                foreach ($transactionReport['income'] as $income) {
                    $row = array_values(array_filter($income['amount_data'], function($item) use($i) {
                       return $item['month'] == ($i);
                    }));

                    if (count($row) > 0) {
                        $totalIncome += $row[0]['amount'];
                    }
                }
            @endphp

            <td align="center" bgcolor="#009F08" >{{$totalIncome}}</td>
        @endfor
    </tr>

    @foreach($transactionReport['expense'] as $income)
        <tr>
            <td align="center">{{$income['coa_category_name']}}</td>

            @for($i = 1; $i <= $currentMonth; $i++)
                @php
                    $row = array_values(array_filter($income['amount_data'], function($item) use($i) {
                       return $item['month'] == ($i);
                    }));
                @endphp

                @if (count($row) > 0)
                    <td align="center">{{$row[0]['amount']}}</td>
                @else
                    <td align="center">0</td>
                @endif
            @endfor
        </tr>
    @endforeach
    <tr>
        <td bgcolor="#BA1D25" width="12" align="center">Total Expense</td>
        @for($i = 1; $i <= $currentMonth; $i++)
            @for($i = 1; $i <= $currentMonth; $i++)
                @php
                    $totalExpense = 0;
                    foreach ($transactionReport['expense'] as $income) {
                        $row = array_values(array_filter($income['amount_data'], function($item) use($i) {
                           return $item['month'] == ($i);
                        }));

                        if (count($row) > 0) {
                            $totalExpense += $row[0]['amount'];
                        }
                    }
                @endphp

                <td align="center" bgcolor="#BA1D25" >{{$totalExpense}}</td>
            @endfor
        @endfor
    </tr>
    </tbody>
</table>
