@extends('layouts.dashboard')

@section('title')
    Coa Category
@endsection

@section('content')
    <div class="flex flex-row justify-between items-center mb-8">
        <h1 class="text-2xl font-bold">Coa Category Management</h1>

        <button
            id="addCoaCategoryBtn"
            class="flex flex-row gap-2 items-center justify-center bg-blue-100 text-blue-900 hover:bg-blue-900 hover:text-blue-100 py-2 px-4 rounded-md">
            @svg('heroicon-o-plus', 'w-6 h-6 border-blue-900') Add Coa Category
        </button>
    </div>

    <table id="coa-category-table" class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-xs uppercase text-gray-700">
        <tr>
            <th class="px-6 py-3" style="text-align: center;">Name</th>
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
                let url = '{{route('coa-category.list')}}';
                // create a datatable
                $('#coa-category-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: url,
                    columns: [
                        {data: 'name', textAlign: 'center', className: 'text-center'},
                        {data: 'action', width: '20%', textAlign: 'center', className: 'text-center'},
                    ],

                });
            });

            $('#addCoaCategoryBtn').on('click', function () {
                const modal = $('#myModal');

                modal.find('#modalTitle').text('Add Coa Category');

                modal.find('#modalBody').html(`
                <form id="coa_category_form">
                    <input type="text" name="name" id="name" placeholder="Category name" class="w-full px-3 py-4 border-b border-gray-300 focus:border-blue-500 focus:outline-none mb-4"/>
                </form>
                `);

                modal.find('#modalFooter').html(`
                <div class="flex flex-row gap-2">
                    <button id="saveCoaCategoryBtn" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Save
                    </button>

                    <button id="closeModalBtn" class="mt-6 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                        Cancel
                    </button>
                </div>
                `);

                showModal();
            });

            $(document).on('click', '#closeModalBtn', function () {
                closeModal();
            });

            $(document).on('click', '#saveCoaCategoryBtn', function (e) {
                e.preventDefault();
                storeCoaCategory();
            });

            $(document).on('click', '#deleteCoaCategory', function () {
                const id = $(this).data('id');

                const modal = $('#confirmModal');

                modal.find('#confirmModalTitle').text('Delete Coa Category');

                modal.find('#confirmModalBody').html(`
                <div>
                    <p>Are you sure want to delete Coa Category?</p>
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

            $(document).on('click', '#deleteCoaCategoryConfirmBtn', function () {
                const id = $(this).data('id');
                closeConfirmModal();
                deleteCoaCategory(id);
            })

            $(document).on('click', '#editCoaCategory', function () {
                const id = $(this).data('id');
                editCoaCategory(id);
            });

            $(document).on('click', '#editCoaCategoryBtn', function () {
                const modal = $('#confirmModal');

                modal.find('#confirmModalTitle').text('Delete Coa Category');

                modal.find('#confirmModalBody').html(`
                <div>
                    <p>Are you sure want to edit Coa Category?</p>
                </div>
                `);

                modal.find('#confirmModalFooter').html(`
                <div class="flex flex-row gap-2">
                    <button id="editCoaCategoryConfirmBtn" class="mt-6 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Edit
                    </button>

                    <button id="closeCoaCategoryConfirmBtn" class="mt-6 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                        Cancel
                    </button>
                </div>
                `);

                showConfirmModal()
            });

            $(document).on('click', '#editCoaCategoryConfirmBtn', function () {
                storeCoaCategory('edit');
            });

            function reloadDatatable() {
                $('#coa-category-table').DataTable().ajax.reload();
            }

            function storeCoaCategory(type = 'add') {
                const modal = $('#myModal');
                const serializedData = modal.find('#modalBody').find('form').serialize();
                let baseUrl = '{{route('coa-category.store')}}';
                let method = 'POST';

                if (type === 'edit') {
                    const id = modal.find('#modalBody').find('form').find('input[name="id"]').val();
                    baseUrl = `/admin/coa-category/${id}`;
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
                            $('#myModal').removeClass('flex').addClass('hidden');
                            reloadDatatable();
                        }, 300);
                    },
                    error: function (xhr, status, error) {
                        const errResponse = JSON.parse(xhr.responseText);
                        showToast(errResponse.message, 'error');
                    }
                });
            }

            function editCoaCategory(id) {
                $.ajax({
                    url: `/admin/coa-category/${id}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        const data = response.data;
                        const modal = $('#myModal');

                        modal.find('#modalTitle').text('Edit Coa Category');

                        modal.find('#modalBody').html(`
                            <form id="coa_category_form">
                                <input type="hidden" name="id" id="id" value="${data.id}"/>
                                <input type="text" name="name" id="name" value="${data.name}" placeholder="Category name" class="w-full px-3 py-4 border-b border-gray-300 focus:border-blue-500 focus:outline-none mb-4"/>
                            </form>
                        `);

                        modal.find('#modalFooter').html(`
                            <div class="flex flex-row gap-2">
                                <button id="editCoaCategoryBtn" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" data-id="${data.id}">
                                    Save
                                </button>

                                <button id="closeModalBtn" class="mt-6 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                                    Cancel
                                </button>
                            </div>
                        `);

                        showModal();
                    },
                    error: function (xhr, status, error) {
                        const errResponse = JSON.parse(xhr.responseText);
                        showToast(errResponse.message, 'error');
                    }
                });
            }

            function deleteCoaCategory(id) {
                $.ajax({
                    url: `/admin/coa-category/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        showToast(data.message, 'success');
                        $('#modalContent').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');

                        setTimeout(function () {
                            $('#myModal').removeClass('flex').addClass('hidden');
                            reloadDatatable();
                        }, 300);
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
