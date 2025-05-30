@extends('layouts.dashboard')

@section('title')
    Coa
@endsection

@section('content')
    <div class="flex flex-row justify-between items-center mb-8">
        <h1 class="text-2xl font-bold">Coa Management</h1>

        <button
            id="addCoaBtn"
            class="flex flex-row gap-2 items-center justify-center bg-blue-100 text-blue-900 hover:bg-blue-900 hover:text-blue-100 py-2 px-4 rounded-md">
            @svg('heroicon-o-plus', 'w-6 h-6 border-blue-900') Add Coa
        </button>
    </div>

    <table id="coa-category-table" class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-xs uppercase text-gray-700">
        <tr>
            <th class="px-6 py-3" style="text-align: center;">Code</th>
            <th class="px-6 py-3" style="text-align: center;">Name</th>
            <th class="px-6 py-3" style="text-align: center;">Category</th>
            <th class="px-6 py-3" style="text-align: center;">Action</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" id="coa-category-table-body">
        </tbody>
    </table>
@endsection

@push('script')
    <script type="module">
        $(document).ready(function () {

            $(function () {
                let url = '{{route('coa.list')}}';
                $('#coa-category-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: url,
                    columns: [
                        {data: 'code', name: 'code', className: 'text-center', textAlign: 'center'},
                        {data: 'name', textAlign: 'center', className: 'text-center', orderable: false},
                        {
                            data: 'category',
                            textAlign: 'center',
                            className: 'text-center',
                            orderable: false,
                            searchable: false
                        },
                        {data: 'action', textAlign: 'center', className: 'text-center', orderable: false},
                    ],

                });
            });

            $('#addCoaBtn').on('click', function () {
                const modal = $('#myModal');

                modal.find('#modalTitle').text('Add Coa');

                modal.find('#modalBody').html(`
                <form id="coa_category_form">
                    <input type="text" name="code" id="code" placeholder="Coa Code" class="w-full px-3 py-4 border-b border-gray-300 focus:border-blue-500 focus:outline-none mb-4"/>
                    <input type="text" name="name" id="name" placeholder="Coa Name" class="w-full px-3 py-4 border-b border-gray-300 focus:border-blue-500 focus:outline-none mb-4"/>
                    <select name="category" id="category" class="w-full bg-transparent border-b border-gray-300 text-gray-700 py-4 px-3 focus:outline-none focus:border-blue-600">
                        <option value="" disabled selected>Select category</option>
                    </select>
                </form>
                `);

                modal.find('#modalFooter').html(`
                <div class="flex flex-row gap-2">
                    <button id="saveCoaBtn" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Save
                    </button>

                    <button id="closeModalBtn" class="mt-6 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                        Cancel
                    </button>
                </div>
                `);

                getCoaCategories();
                showModal();
            });

            $(document).on('click', '#saveEditCoaBtn', function (e) {
                const modal = $('#confirmModal');

                modal.find('#confirmModalTitle').text('Delete Coa Category');

                modal.find('#confirmModalBody').html(`
                <div>
                    <p>Are you sure want to edit Coa?</p>
                </div>
                `);

                modal.find('#confirmModalFooter').html(`
                <div class="flex flex-row gap-2">
                    <button id="editCoaConfirmBtn" class="mt-6 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Edit
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

            $(document).on('click', '#editCoaConfirmBtn', function (e) {
                e.preventDefault();
                storeCoa('edit');
            });

            $(document).on('click', '#closeModalBtn', function () {
                closeModal();
            });

            $(document).on('click', '#saveCoaBtn', function (e) {
                e.preventDefault();
                storeCoa();
            });

            $(document).on('click', '#editCoa', function () {
                const id = $(this).data('id');
                getCoaDetail(id);
            });

            $(document).on('click', '#deleteCoa', function (e) {
                const id = $(this).data('id');

                const modal = $('#confirmModal');

                modal.find('#confirmModalTitle').text('Delete Coa Category');

                modal.find('#confirmModalBody').html(`
                <div>
                    <p>Are you sure want to delete Coa?</p>
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

            $(document).on('click', '#closeCoaCategoryConfirmBtn', function () {
                closeConfirmModal();
            });

            $(document).on('click', '#deleteCoaCategoryConfirmBtn', function (e) {
                e.preventDefault();

                const id = $(this).data('id');
                deleteCoa(id);
            });

            function getCoaDetail(id) {
                $.ajax({
                    url: `/admin/coa/${id}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        const data = response.data

                        const modal = $('#myModal');

                        modal.find('#modalTitle').text('Edit Coa');

                        modal.find('#modalBody').html(`
                        <form id="coa_category_form">
                            <input type="hidden" name="id" id="id" value="${data.id}"/>
                            <input type="text" name="code" id="code" value="${data.code}" placeholder="Coa Code" class="w-full px-3 py-4 border-b border-gray-300 focus:border-blue-500 focus:outline-none mb-4"/>
                            <input type="text" name="name" id="name" value="${data.name}" placeholder="Coa Name" class="w-full px-3 py-4 border-b border-gray-300 focus:border-blue-500 focus:outline-none mb-4"/>
                            <select name="category" id="category" class="w-full bg-transparent border-b border-gray-300 text-gray-700 py-4 px-3 focus:outline-none focus:border-blue-600">
                                <option value="" disabled>Select category</option>
                            </select>
                        </form>
                        `);

                        modal.find('#modalFooter').html(`
                        <div class="flex flex-row gap-2">
                            <button id="saveEditCoaBtn" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Save
                            </button>

                            <button id="closeModalBtn" class="mt-6 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                                Cancel
                            </button>
                        </div>
                        `);

                        getCoaCategories(data.coa_category_id);
                        showModal();
                    },
                    error: function (xhr, status, error) {
                        const errResponse = JSON.parse(xhr.responseText);
                        showToast(errResponse.message, 'error');
                    }
                });
            }

            function deleteCoa(id) {
                $.ajax({
                    url: `/admin/coa/${id}`,
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

            function storeCoa(type = 'add') {
                const modal = $('#myModal');
                const serializedData = modal.find('#modalBody').find('form').serialize();
                let baseUrl = '{{route('coa.store')}}';
                let method = 'POST';

                if (type === 'edit') {
                    const id = modal.find('#modalBody').find('form').find('input[name="id"]').val();
                    baseUrl = `/admin/coa/${id}`;
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
                $('#coa-category-table').DataTable().ajax.reload();
            }

            function getCoaCategories(selectedId = null) {
                $.ajax({
                    url: '/admin/coa-category/list?source=dropdown',
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        const data = response.data;
                        const modal = $('#myModal');

                        data.forEach(coa => {
                            if (selectedId === coa.id) {
                                modal.find('form').find('#category').append(`<option value="${coa.id}" selected>${coa.name}</option>`);
                            } else {
                                modal.find('form').find('#category').append(`<option value="${coa.id}">${coa.name}</option>`);
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
