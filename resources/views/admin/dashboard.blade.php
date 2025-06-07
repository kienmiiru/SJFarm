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
        @include('components.admin-sidebar')
        <div class="col-span-full md:col-span-4 min-h-screen">
            <div class="text-center text-xl m-4">
                Selamat datang! Anda login sebagai <span class="font-bold">{{ $admin->username }}</span>
            </div>
            <div class="md:flex md:flex-wrap justify-around">
                <div class="m-4 bg-white p-0 pb-4 rounded-2xl shadow md:w-2/5">
                    <div class="bg-ccgreen p-4 rounded-t-2xl font-medium text-xl">Permintaan</div>
                    <div class="m-4">
                        Total permintaan yang belum diproses: <span class="font-bold">{{ $unconfirmedRequestsCount }}</span>
                    </div>
                </div>
                <div class="m-4 bg-white p-0 pb-4 rounded-2xl shadow md:w-2/5">
                    <div class="bg-ccgreen p-4 rounded-t-2xl font-medium text-xl">Pemasukan</div>
                    <div class="m-4">
                        Pemasukan bulan ini: <span class="font-bold">{{ Number::format($incomeThisMonth, locale: 'id') }}</span>
                    </div>
                </div>
                <div class="m-4 bg-white p-0 pb-4 rounded-2xl shadow md:w-2/5">
                    <div class="bg-ccgreen p-4 rounded-t-2xl font-medium text-xl">Distributor</div>
                    <div class="m-4">
                        Distributor aktif: <span class="font-bold">{{ $distributorsCount }}</span>
                    </div>
                </div>
                <div class="m-4 bg-white p-0 pb-4 rounded-2xl shadow md:w-2/5">
                    <div class="bg-ccgreen p-4 rounded-t-2xl font-medium text-xl">Stok Buah</div>
                    <div class="m-4">
                        @foreach ($fruits as $fruit)
                            {{ $fruit->name }}: <span class="font-bold">{{ Number::format($fruit->stock_in_kg, locale: 'id') }}</span>
                            <br>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>