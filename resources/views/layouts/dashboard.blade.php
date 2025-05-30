<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Expense Tracker</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        .dataTables_filter input,
        .dataTables_length select {
            @apply border border-gray-300 bg-white text-sm px-3 py-1 text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 rounded;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            @apply text-sm text-gray-600 px-2 py-1 hover:text-indigo-600;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            @apply text-indigo-600 font-semibold underline;
        }

        table.dataTable td,
        table.dataTable th {
            @apply px-4 py-2;
        }

    </style>
</head>
<body class="text-gray-800">
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Sidebar -->
            <aside class="bg-white rounded-2xl shadow p-4 md:col-span-1">
                <h2 class="text-xl font-semibold mb-4">Expense Tracker</h2>
                <ul class="space-y-2">
                    <li><a href="{{route('admin.index')}}" class="text-gray-700 hover:text-blue-600">Dashboard</a></li>
                    <li><a href="#" class="text-gray-700 hover:text-blue-600">Transaction</a></li>
                    <li><a href="{{route('coa-category.index')}}" class="text-gray-700 hover:text-blue-600">COA Category</a></li>
                    <li><a href="{{route('coa.index')}}" class="text-gray-700 hover:text-blue-600">COA</a></li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="bg-white rounded-2xl shadow p-6 md:col-span-3">
                @yield('content')
            </main>
        </div>
    </div>
</div>

<div id="myModal" class="fixed inset-0 bg-black/50 hidden justify-center items-center z-50">
    <div id="modalContent"
         class="bg-white rounded-2xl p-6 w-full transform scale-95 opacity-0 transition duration-300 ease-out">
        <h2 class="text-xl font-bold mb-4" id="modalTitle">Modal Title</h2>
        <div id="modalBody">
            <p class="text-gray-700">Modal Body</p>
        </div>
        <div id="modalFooter" class="flex flex-row justify-end gap-2">
        </div>
    </div>
</div>

<div id="confirmModal" class="fixed inset-0 bg-black/50 hidden justify-center items-center z-50">
    <div id="confirmModalContent"
         class="bg-white rounded-2xl p-6 w-full transform scale-95 opacity-0 transition duration-300 ease-out">
        <h2 class="text-xl font-bold mb-4" id="confirmModalTitle">Modal Title</h2>
        <div id="confirmModalBody">
            <p class="text-gray-700">Modal Body</p>
        </div>
        <div id="confirmModalFooter" class="flex flex-row justify-end gap-2">
        </div>
    </div>
</div>

<div id="toastContainer" class="fixed top-5 right-5 z-50 space-y-2"></div>

<script>
    function showModal() {
        const modal = $('#myModal');

        modal.removeClass('hidden').addClass('flex');
        setTimeout(() => {
            modal.find('#modalContent').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100 max-w-md');
        }, 10);
    }

    function closeModal() {
        $('#modalContent').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(function () {
            $('#myModal').removeClass('flex').addClass('hidden');
        }, 300);
    }

    function showConfirmModal() {
        const modal = $('#confirmModal');

        modal.removeClass('hidden').addClass('flex');
        setTimeout(() => {
            modal.find('#confirmModalContent').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100 max-w-md');
        }, 10);
    }

    function closeConfirmModal() {
        $('#confirmModalContent').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(function () {
            $('#confirmModal').removeClass('flex').addClass('hidden');
        }, 300);
    }

    function showToast(message, type = 'success') {
        const id = Date.now(); // unique ID
        const bgColor = type === 'success'
            ? 'bg-green-500'
            : type === 'error'
                ? 'bg-red-500'
                : 'bg-gray-800';

        const toast = $(`
          <div id="toast-${id}" class="text-white ${bgColor} px-4 py-3 rounded shadow-md flex items-center justify-between min-w-[300px] animate-fade-in">
            <span>${message}</span>
            <button class="ml-4 text-white font-bold focus:outline-none" onclick="$('#toast-${id}').fadeOut(300, function(){ $(this).remove(); })">&times;</button>
          </div>
        `);

        $('#toastContainer').html(toast);

        // Auto remove after 3 seconds
        setTimeout(() => {
            $(`#toast-${id}`).fadeOut(300, function () {
                $(this).remove();
            });
        }, 3000);
    }
</script>
@stack('script')
</body>
</html>
