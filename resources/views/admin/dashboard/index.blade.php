@extends('layouts.dashboard')

@section('title')
    Dashboard
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-4">Welcome to your Dashboard</h1>
    <p class="text-gray-600 mb-12">Here’s a quick overview of what’s going on.</p>

    <div class="flex flex-col gap-16">
        <div>
            <h2 class="mb-4 font-bold">Transaction Summary</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="p-4 bg-blue-100 rounded-xl">
                    <p class="text-sm text-blue-800">Total Transaction</p>
                    <p class="text-2xl font-bold text-blue-900">{{$transactionSummary['transactionCount']}}</p>
                </div>
                <div class="p-4 bg-green-100 rounded-xl">
                    <p class="text-sm text-green-800">Total Income</p>
                    <p class="text-2xl font-bold text-green-900">{{$transactionSummary['transactionIncome']}}</p>
                </div>
                <div class="p-4 bg-red-100 rounded-xl">
                    <p class="text-sm text-red-800">Total Outcome</p>
                    <p class="text-2xl font-bold text-red-900">{{$transactionSummary['transactionExpense']}}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8">
            <div>
                <h2 class="mb-4 font-bold text-center">Income Chart</h2>
                <canvas id="income-chart"></canvas>
            </div>

            <div>
                <h2 class="mb-4 font-bold text-center">Expense Chart</h2>
                <canvas id="expense-chart"></canvas>
            </div>
        </div>

        <div>
            <h2 class="mb-4 font-bold">Profit Loss Report</h2>
            <div class="overflow-x-auto">
                <table id="pnl-table" class="min-w-full divide-y divide-gray-200">
                    <thead id="pnl-table-head" class="bg-gray-100">
                    </thead>
                    <tbody id="pnl-table-body" class="bg-white divide-y divide-gray-200 text-sm text-gray-800">
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h2 class="font-bold">Last 10 Transaction Histories</h2>
            <table id="transaction-table" class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-3" style="text-align: center;">Transaction Date</th>
                    <th class="px-6 py-3" style="text-align: center;">Description</th>
                    <th class="px-6 py-3" style="text-align: center;">Amount</th>
                    <th class="px-6 py-3" style="text-align: center;">Transaction Type</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="transaction-table-body">
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script type="module">
        $(document).ready(function () {

            $(function () {
                $.ajax({
                    url: `/admin/transaction/report`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        const data = response.data

                        const currentMonth = new Date().getMonth().toString();
                        const currentYear = new Date().getFullYear();
                        const table = $('#pnl-table');

                        const tableHead = table.find('thead');
                        const tableBody = table.find('tbody');

                        let tableHeadContent = ``;
                        let tableBodyContent = '';

                        tableHeadContent += `<tr>`;
                        tableHeadContent += `<td class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase text-center" rowspan="2">No.</td>`;
                        tableHeadContent += `<td class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase text-center" rowspan="2">Category</td>`;
                        for (let i = 0; i <= currentMonth; i++) {
                            tableHeadContent += `<td class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase text-center">${currentYear}-${(i + 1).toString().padStart(2, '0')}</td>`;
                        }
                        tableHeadContent += `</tr>`;

                        tableHeadContent += `<tr>`;
                        for (let i = 0; i <= currentMonth; i++) {
                            tableHeadContent += `<td class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase text-center">Amount</td>`;
                        }
                        tableHeadContent += `</tr>`;

                        data.income.forEach((item, idx) => {
                            tableBodyContent += `<tr>`;
                            tableBodyContent += `<td class="px-6 py-3 text-xs font-semibold text-gray-700 text-center">${idx+1}</td>`;
                            tableBodyContent += `<td class="px-6 py-3 text-xs font-semibold text-gray-700 text-center">${item.coa_category_name}</td>`;
                            for (let i = 0; i <= currentMonth; i++) {
                                const row = item.amount_data.filter(filter => {
                                    return filter.month == i+1;
                                })[0];

                                tableBodyContent += `<td class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase text-center">${row !== undefined ? row.amount : 0}</td>`;
                            }
                            tableBodyContent += `</tr>`;
                        });

                        tableBodyContent += `<tr class="bg-green-300">`;
                        tableBodyContent += `<td colspan="2" class="px-6 py-3 text-xs font-semibold text-gray-700 text-center">Total Income</td>`;
                        for (let i = 0; i <= currentMonth; i++) {
                            let totalIncome = 0;
                            data.income.forEach((item) => {
                                const row = item.amount_data.filter(filter => {
                                    return filter.month == i+1;
                                })[0];

                                totalIncome += row !== undefined ? row.amount : 0;
                            });

                            tableBodyContent += `<td class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase text-center">${totalIncome}</td>`;
                        }
                        tableBodyContent += `</tr>`;

                        data.expense.forEach((item, idx) => {
                            tableBodyContent += `<tr>`;
                            tableBodyContent += `<td class="px-6 py-3 text-xs font-semibold text-gray-700 text-center">${idx+1}</td>`;
                            tableBodyContent += `<td class="px-6 py-3 text-xs font-semibold text-gray-700 text-center">${item.coa_category_name}</td>`;
                            for (let i = 0; i <= currentMonth; i++) {
                                const row = item.amount_data.filter(filter => {
                                    return filter.month == i+1;
                                })[0];

                                tableBodyContent += `<td class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase text-center">${row !== undefined ? row.amount : 0}</td>`;
                            }
                            tableBodyContent += `</tr>`;
                        });

                        tableBodyContent += `<tr class="bg-red-300">`;
                        tableBodyContent += `<td colspan="2" class="px-6 py-3 text-xs font-semibold text-gray-700 text-center">Total Expense</td>`;
                        for (let i = 0; i <= currentMonth; i++) {
                            let totalExpense = 0;
                            data.expense.forEach((item) => {
                                const row = item.amount_data.filter(filter => {
                                    return filter.month == i+1;
                                })[0];

                                totalExpense += row !== undefined ? row.amount : 0;
                            });

                            tableBodyContent += `<td class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase text-center">${totalExpense}</td>`;
                        }
                        tableBodyContent += `</tr>`;

                        tableBody.html(tableBodyContent);
                        tableHead.html(tableHeadContent);
                    },
                    error: function (xhr, status, error) {
                        const errResponse = JSON.parse(xhr.responseText);
                        showToast(errResponse.message, 'error');
                    }
                });
            });

            $(function () {
                $.ajax({
                    url: `/admin/transaction/chart?type=income`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        const data = response.data;
                        const labels = data.map(function (item) {
                            return item.coa_category_name;
                        });
                        const dataSets = data.map(function (item) {
                            return item.total_amount;
                        });

                        new Chart(
                            document.getElementById('income-chart'),
                            {
                                type: 'pie',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Income In Rupiah',
                                        data: dataSets,
                                        hoverOffset: 4,
                                    }],
                                },
                            }
                        );
                    },
                    error: function (xhr, status, error) {
                        const errResponse = JSON.parse(xhr.responseText);
                        showToast(errResponse.message, 'error');
                    }
                });
            });

            $(function () {
                $.ajax({
                    url: `/admin/transaction/chart?type=expense`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        const data = response.data;
                        const labels = data.map(function (item) {
                            return item.coa_category_name;
                        });
                        const dataSets = data.map(function (item) {
                            return item.total_amount;
                        });

                        new Chart(
                            document.getElementById('expense-chart'),
                            {
                                type: 'pie',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Expense In Rupiah',
                                        data: dataSets,
                                        hoverOffset: 4,
                                    }],
                                },
                            }
                        );
                    },
                    error: function (xhr, status, error) {
                        const errResponse = JSON.parse(xhr.responseText);
                        showToast(errResponse.message, 'error');
                    }
                });
            });

            $(function () {
                let url = '/admin/transaction/list?period=30';
                $('#transaction-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: url,
                    order: [['code', 'desc']],
                    searching: false,
                    ordering: false,
                    lengthChange: false,
                    paging: false,
                    info: false,
                    columns: [
                        {
                            data: 'created_at',
                            name: 'created_at',
                            className: 'text-center',
                            textAlign: 'center',
                            orderable: true
                        },
                        {
                            data: 'description',
                            name: 'description',
                            className: 'text-center',
                            textAlign: 'center',
                            orderable: false
                        },
                        {
                            data: 'amount',
                            name: 'amount',
                            className: 'text-center',
                            textAlign: 'center',
                            orderable: false
                        },
                        {
                            data: 'type',
                            name: 'type',
                            className: 'text-center',
                            textAlign: 'center',
                            orderable: false
                        },
                    ],

                });
            });

        });
    </script>
@endpush
