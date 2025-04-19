<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SJFarm - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        <div class="sticky top-0 col-span-1 md:flex justify-between flex-col hidden bg-corange h-screen">
            <div class="p-2">
                <a href="/admin/dashboard" class="block my-1 bg-cpink rounded-md px-1 py-2 cursor-pointer">
                    <i class="fa fa-gauge"></i> Dashboard
                </a>
                <p class="text-sm">Menu</p>
                <a class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer">
                    <i class="fa fa-user-group"></i> Distributor
                </a>
                <a href="/admin/prediksi" class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer">
                    <i class="fa fa-chart-simple"></i> Prediksi Permintaan
                </a>
                <a class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer">
                    <i class="fa fa-handshake"></i> Permintaan & Pembelian
                </a>
                <a class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer">
                    <i class="fa fa-boxes-stacked"></i> Buah & Stok
                </a>
                <a class="block my-1 hover:bg-cpink rounded-md px-1 py-2 cursor-pointer">
                    <i class="fa fa-seedling"></i> Panen
                </a>
            </div>
            <div class="mt-auto bg-black text-white p-2">
                <p class="text-sm">Akun</p>
                <div class="my-1 rounded-md px-1 py-2 cursor-pointer">
                    <i class="fa fa-gear"></i> Pengaturan
                </div>
                <div class="my-1 rounded-md px-1 py-2 cursor-pointer">
                    <i class="fa fa-arrow-right-from-bracket"></i> Keluar
                </div>
            </div>
        </div>
        <div class="col-span-4">
            uiia
        </div>
    </div>
</body>

</html>