@extends('layouts.dashboard')

@section('title')
    Coa
@endsection

@section('content')
    <div class="flex flex-row justify-between items-center mb-8">
        <h1 class="text-2xl font-bold">Transaction Management</h1>

        <div class="flex flex-row gap-2">
            <button
                id="addTransactionBtn"
                class="flex flex-row gap-2 items-center justify-center bg-blue-100 text-blue-900 hover:bg-blue-900 hover:text-blue-100 py-2 px-4 rounded-md">
                @svg('heroicon-o-plus', 'w-6 h-6 border-blue-900') Add Transaction
            </button>
            <a
                href="{{route('transaction.export')}}"
                id="addTransactionBtn"
                class="flex flex-row gap-2 items-center justify-center bg-blue-100 text-blue-900 hover:bg-blue-900 hover:text-blue-100 py-2 px-4 rounded-md">
                @svg('heroicon-o-cloud-arrow-down', 'w-6 h-6 border-blue-900') Export Transaction
            </a>
        </div>
    </div>

    <table id="transaction-table" class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-xs uppercase text-gray-700">
        <tr>
            <th class="px-6 py-3" style="text-align: center;">Transaction Date</th>
            <th class="px-6 py-3" style="text-align: center;">Description</th>
            <th class="px-6 py-3" style="text-align: center;">Coa</th>
            <th class="px-6 py-3" style="text-align: center;">Amount</th>
            <th class="px-6 py-3" style="text-align: center;">Transaction Type</th>
            <th class="px-6 py-3" style="text-align: center;">Action</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" id="transaction-table-body">
        </tbody>
    </table>
@endsection

@push('script')
    <script type="module">
        $(document).ready(function () {

            $(function () {
                let url = '{{route('transaction.list')}}';
                $('#transaction-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: url,
                    order: [['code', 'desc']],
                    columns: [
                        {data: 'created_at', name: 'created_at', className: 'text-center', textAlign: 'center', orderable: true},
                        {data: 'description', name: 'description', className: 'text-center', textAlign: 'center', orderable: false},
                        {data: 'coa_name', name: 'coa_name', className: 'text-center', textAlign: 'center', orderable: false},
                        {data: 'amount', name: 'amount', className: 'text-center', textAlign: 'center', orderable: false},
                        {data: 'type', name: 'type', className: 'text-center', textAlign: 'center', orderable: false},
                        {data: 'action', textAlign: 'center', className: 'text-center', orderable: false},
                    ],

                });
            });

            $('#addTransactionBtn').on('click', function () {
                const modal = $('#myModal');

                modal.find('#modalTitle').text('Add Transaction');

                modal.find('#modalBody').html(`
                <form id="coa_category_form">
                    <input type="text" name="description" id="description" placeholder="Transaction Desc" class="w-full px-3 py-4 border-b border-gray-300 focus:border-blue-500 focus:outline-none mb-4"/>
                    <input type="text" name="amount" id="amount" placeholder="Transaction Amount" class="w-full px-3 py-4 border-b border-gray-300 focus:border-blue-500 focus:outline-none mb-4"/>
                    <select name="type" id="type" class="w-full bg-transparent border-b border-gray-300 text-gray-700 py-4 px-3 focus:outline-none focus:border-blue-600 mb-4">
                        <option value="" disabled selected>Select transaction type</option>
                        <option value="debit">Debit</option>
                        <option value="credit">Credit</option>
                    </select>
                    <select name="coa" id="coa" class="w-full bg-transparent border-b border-gray-300 text-gray-700 py-4 px-3 focus:outline-none focus:border-blue-600 mb-4">
                        <option value="" disabled selected>Select coa</option>
                    </select>
                </form>
                `);

                modal.find('#modalFooter').html(`
                <div class="flex flex-row gap-2">
                    <button id="saveTransactionBtn" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Save
                    </button>

                    <button id="closeModalBtn" class="mt-6 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                        Cancel
                    </button>
                </div>
                `);

                getCoas();
                showModal();
            });

            $(document).on('click', '#closeModalBtn', function () {
                closeModal();
            });

            $(document).on('click', '#saveTransactionBtn', function (e) {
                e.preventDefault();
                store();
            });

            $(document).on('click', '#deleteTransaction', function () {
                const id = $(this).data('id');

                const modal = $('#confirmModal');

                modal.find('#confirmModalTitle').text('Delete Transaction');

                modal.find('#confirmModalBody').html(`
                <div>
                    <p>Are you sure want to delete transaction?</p>
                </div>
                `);

                modal.find('#confirmModalFooter').html(`
                <div class="flex flex-row gap-2">
                    <button id="deleteCoaCategoryConfirmBtn" data-id="${id}" class="mt-6 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Delete
                    </button>

                    <button id="closeCoaCategoryConfirmBtn" class="mt-6 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                        Cancel
                    </button>
                </div>
                `);

                showConfirmModal();
            });

            $(document).on('click', '#closeCoaCategoryConfirmBtn', function (e) {
                closeConfirmModal();
            });

            $(document).on('click', '#deleteCoaCategoryConfirmBtn', function (e) {
                e.preventDefault();

                const id = $(this).data('id');
                deleteTransaction(id);
            });

            $(document).on('click', '#showTransaction', function (e) {
                const id = $(this).data('id');
                getTransactionDetail(id);
            });

            $(document).on('click', '#editTransaction', function () {
                const id = $(this).data('id');
                getTransactionDetail(id, 'edit');
            });

            function getTransactionDetail(id, type = 'detail') {
                $.ajax({
                    url: `/admin/transaction/${id}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        const modal = $('#myModal');
                        const data = response.data;

                        if (type === 'edit') {
                            const modal = $('#myModal');

                            modal.find('#modalTitle').text('Edit Transaction');

                            modal.find('#modalBody').html(`
                            <form id="coa_category_form">
                                <input type="hidden" name="id" id="id" value="${data.id}"/>
                                <input type="text" name="description" id="description" value="${data.description}" placeholder="" nsaction Desc" class="w-full px-3 py-4 border-b border-gray-300 focus:border-blue-500 focus:outline-none mb-4"/>
                                <input type="text" name="amount" id="amount" value="${data.amount}" placeholder="Transaction Amount" class="w-full px-3 py-4 border-b border-gray-300 focus:border-blue-500 focus:outline-none mb-4"/>
                                <select name="type" id="type" class="w-full bg-transparent border-b border-gray-300 text-gray-700 py-4 px-3 focus:outline-none focus:border-blue-600 mb-4">
                                    <option value="" disabled>Select transaction type</option>
                                    <option value="debit" ${data.type === 'debit' ? 'selected' : ''}>Debit</option>
                                    <option value="credit" ${data.type === 'credit' ? 'selected' : ''}>Credit</option>
                                </select>
                                <select name="coa" id="coa" class="w-full bg-transparent border-b border-gray-300 text-gray-700 py-4 px-3 focus:outline-none focus:border-blue-600 mb-4">
                                    <option value="" disabled selected>Select coa</option>
                                </select>
                            </form>
                            `);

                            modal.find('#modalFooter').html(`
                            <div class="flex flex-row gap-2">
                                <button id="saveEditTransactionBtn" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Save
                                </button>

                                <button id="closeModalBtn" class="mt-6 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                                    Cancel
                                </button>
                            </div>
                            `);

                            getCoas(data.coa_id);
                            showModal();
                        } else {
                            modal.find('#modalTitle').text('Transaction Detail');

                            modal.find('#modalBody').html(`
                            <div>
                                <p>Description: ${data.description}</p>
                                <p>Amount: ${data.amount}</p>
                                <p>Type: ${data.type}</p>
                                <p>COA: ${data.coa_name}</p>
                                <p>COA Category: ${data.coa_category_name}</p>
                                <p>Transaction Date: ${new Date(data.created_at).toLocaleString()}</p>
                            </div>
                        `);

                            modal.find('#modalFooter').html(`
                            <div class="flex flex-row gap-2">
                                <button id="closeModalBtn" class="mt-6 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                                    Cancel
                                </button>
                            </div>
                            `);

                            showModal();
                        }
                    },
                    error: function (xhr, status, error) {
                        const errResponse = JSON.parse(xhr.responseText);
                        showToast(errResponse.message, 'error');
                    }
                });
            }

            $(document).on('click', '#saveEditTransactionBtn', function (e) {
                const modal = $('#confirmModal');

                modal.find('#confirmModalTitle').text('Edit Coa Category');

                modal.find('#confirmModalBody').html(`
                <div>
                    <p>Are you sure want to edit Coa?</p>
                </div>
                `);

                modal.find('#confirmModalFooter').html(`
                <div class="flex flex-row gap-2">
                    <button id="editTransactionConfirmBtn" class="mt-6 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Edit
                    </button>

                    <button id="closeTransactionConfirmBtn" class="mt-6 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                        Cancel
                    </button>
                </div>
                `);

                showConfirmModal();
            });

            $(document).on('click', '#closeTransactionConfirmBtn', function (e) {
                closeConfirmModal();
            });

            $(document).on('click', '#editTransactionConfirmBtn', function (e) {
                e.preventDefault();
                store('edit');
            });

            function deleteTransaction(id) {
                $.ajax({
                    url: `/admin/transaction/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        showToast(data.message, 'success');
                        closeModal();
                        closeConfirmModal();

                        setTimeout(function () {
                            reloadDatatable();
                        }, 300);
                    },
                    error: function (xhr, status, error) {
                        const errResponse = JSON.parse(xhr.responseText);
                        showToast(errResponse.message, 'error');
                    }
                });
            }

            function store(type = 'add') {
                const modal = $('#myModal');
                const serializedData = modal.find('#modalBody').find('form').serialize();
                let baseUrl = '{{route('transaction.store')}}';
                let method = 'POST';

                if (type === 'edit') {
                    const id = modal.find('#modalBody').find('form').find('input[name="id"]').val();
                    baseUrl = `/admin/transaction/${id}`;
                    method = 'PUT';
                }

                $.ajax({
                    url: baseUrl,
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: serializedData,
                    success: function (data) {
                        showToast(data.message, 'success');
                        closeModal();
                        closeConfirmModal();

                        setTimeout(function () {
                            reloadDatatable();
                        }, 300);
                    },
                    error: function (xhr, status, error) {
                        const errResponse = JSON.parse(xhr.responseText);
                        showToast(errResponse.message, 'error');
                    }
                });
            }

            function reloadDatatable() {
                $('#transaction-table').DataTable().ajax.reload();
            }

            function getCoas(selectedId = null) {
                $.ajax({
                    url: '/admin/coa/list?source=dropdown',
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        const data = response.data;
                        const modal = $('#myModal');

                        data.forEach(coa => {
                            if (selectedId === coa.id) {
                                modal.find('form').find('#coa').append(`<option value="${coa.id}" selected>${coa.name}</option>`);
                            } else {
                                modal.find('form').find('#coa').append(`<option value="${coa.id}">${coa.name}</option>`);
                            }

                        });
                    },
                    error: function (xhr, status, error) {
                        const errResponse = JSON.parse(xhr.responseText);
                        showToast(errResponse.message, 'error');
                    }
                });
            }

        });
    </script>
@endpush
