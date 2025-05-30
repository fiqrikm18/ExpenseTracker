<div class="flex flex-row gap-2 justify-center items-center">
    <button class="bg-blue-400 p-1 rounded-sm" id="{{$edit_btn_id}}" data-id="{{$data->id}}">@svg('heroicon-o-pencil', 'w-5 h-5 inline-block
        border-white outline-white stroke-white')
    </button>
    <button class="bg-red-400 p-1 rounded-sm"  id="{{$delete_btn_id}}" data-id="{{$data->id}}">@svg('heroicon-o-trash', 'w-5 h-5 inline-block
        border-white outline-white stroke-white')
    </button>
</div>
