<div class="sticky top-0 col-span-1 md:flex justify-between flex-col hidden bg-corange h-screen">
    <div class="p-2">
        <a href="/admin/dashboard" class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer {{ Request::is('admin/dashboard') ? 'bg-cpink' : '' }}">
            <i class="fa fa-gauge"></i> Dashboard
        </a>
        <p class="text-sm">Menu</p>
        <a href="/admin/distributor" class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer {{ Request::is('admin/distributor') ? 'bg-cpink' : '' }}">
            <i class="fa fa-user-group"></i> Distributor
        </a>
        <a href="/admin/prediksi" class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer {{ Request::is('admin/prediksi') ? 'bg-cpink' : '' }}">
            <i class="fa fa-chart-simple"></i> Prediksi Permintaan
        </a>
        <a href="/admin/permintaan-pembelian" class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer {{ Request::is('admin/permintaan-pembelian') ? 'bg-cpink' : '' }}">
            <i class="fa fa-handshake"></i> Permintaan & Pembelian
        </a>
        <a href="/admin/stok" class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer {{ Request::is('admin/stok') ? 'bg-cpink' : '' }}">
            <i class="fa fa-boxes-stacked"></i> Buah & Stok
        </a>
        <a href="/admin/panen" class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer {{ Request::is('admin/panen') ? 'bg-cpink' : '' }}">
            <i class="fa fa-seedling"></i> Panen
        </a>
    </div>
    <div class="mt-auto bg-black text-white p-2">
        <p class="text-sm">Akun</p>
        <a href="/admin/akun" class="block my-1 rounded-md px-1 py-2 cursor-pointer">
            <i class="fa fa-gear"></i> Pengaturan
        </a>
        <a href="/logout" class="block my-1 rounded-md px-1 py-2 cursor-pointer">
            <i class="fa fa-arrow-right-from-bracket"></i> Keluar
        </a>
    </div>
</div>