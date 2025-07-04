<div class="md:hidden bg-corange col-span-full p-4">
    <a href="#" class="fa fa-bars" id="menu-button"></a>
</div>
<div id="sidebar" class="md:sticky top-0 col-span-full md:col-span-1 md:flex justify-between flex-col hidden bg-corange md:h-screen">
    <div class="p-2">
        <a href="/distributor/dashboard" class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer {{ Request::is('distributor/dashboard') ? 'bg-cpink' : '' }}">
            <i class="fa fa-gauge"></i> Dashboard
        </a>
        <p class="text-sm">Menu</p>
        <a href="/distributor/buah" class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer {{ Request::is('distributor/buah') ? 'bg-cpink' : '' }}">
            <i class="fa fa-boxes-stacked"></i> Daftar Buah
        </a>
        <a href="/distributor/pemesanan" class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer {{ Request::is('distributor/pemesanan') ? 'bg-cpink' : '' }}">
            <i class="fa fa-handshake"></i> Pemesanan Buah
        </a>
        <a href="/distributor/riwayat" class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer {{ Request::is('distributor/riwayat') ? 'bg-cpink' : '' }}">
            <i class="fa fa-clock"></i> Riwayat Permintaan
        </a>
    </div>
    <div class="mt-auto bg-black text-white p-2">
        <p class="text-sm">Akun</p>
        <a href="/distributor/akun" class="block my-1 rounded-md px-1 py-2 cursor-pointer">
            <i class="fa fa-gear"></i> Pengaturan
        </a>
        <a href="/logout" class="block my-1 rounded-md px-1 py-2 cursor-pointer">
            <i class="fa fa-arrow-right-from-bracket"></i> Keluar
        </a>
    </div>
</div>
<script>
    document.getElementById('menu-button').addEventListener('click', function (event) {
        event.preventDefault();
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('hidden');
    });
</script>