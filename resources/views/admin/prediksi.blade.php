<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="grid grid-cols-5">
        <div class="col-span-1 md:flex justify-between flex-col hidden p-2 bg-green-400 h-screen">
            <div>
                <div class="my-1 hover:bg-green-600 rounded-md px-1 py-2 text-green-800 hover:text-green-100 cursor-pointer">
                    <i class="fa fa-gauge"></i> <a href="/admin/dashboard">Dashboard</a>
                </div>
                <p class="text-green-800 text-sm">Menu</p>
                <div class="my-1 hover:bg-green-600 rounded-md px-1 py-2 text-green-800 hover:text-green-100 cursor-pointer">
                    <i class="fa fa-user-group"></i> Distributor
                </div>
                <div class="my-1 bg-green-600 rounded-md px-1 py-2 text-green-100 cursor-pointer">
                    <i class="fa fa-chart-simple"></i> <a href="/admin/prediksi">Prediksi Permintaan</a>
                </div>
                <div class="my-1 hover:bg-green-600 rounded-md px-1 py-2 text-green-800 hover:text-green-100 cursor-pointer">
                    <i class="fa fa-handshake"></i> Permintaan & Pembelian
                </div>
                <div class="my-1 hover:bg-green-600 rounded-md px-1 py-2 text-green-800 hover:text-green-100 cursor-pointer">
                    <i class="fa fa-boxes-stacked"></i> Buah & Stok
                </div>
                <div class="my-1 hover:bg-green-600 rounded-md px-1 py-2 text-green-800 hover:text-green-100 cursor-pointer">
                    <i class="fa fa-seedling"></i> Panen
                </div>
            </div>
            <div class="mt-auto">
                <p class="text-green-800 text-sm">Akun</p>
                <div class="my-1 hover:bg-green-600 rounded-md px-1 py-2 text-green-800 hover:text-green-100 cursor-pointer">
                    <i class="fa fa-gear"></i> Pengaturan
                </div>
                <div class="my-1 hover:bg-green-600 rounded-md px-1 py-2 text-green-800 hover:text-green-100 cursor-pointer">
                    <i class="fa fa-arrow-right-from-bracket"></i> Keluar
                </div>
            </div>
        </div>
        <div class="col-span-4">
<div class="p-4">
    <h2 class="text-xl font-bold mb-4">Prediksi Permintaan Buah (3 Bulan Ke Depan)</h2>
    @foreach ($predictions as $fruitId => $forecast)
        <div class="mb-8 p-4 rounded-md border border-green-300 bg-green-100">
            <h3 class="font-semibold mb-2">Buah ID: {{ $fruitId }}</h3>
            
            @if (isset($forecast['error']))
                <p class="text-red-500">Error: {{ $forecast['error'] }}</p>
            @else
            @php
                $historicalDataFruit = collect($historicalData[$fruitId])
                                            ->mapWithKeys(fn($item) => [$item['month'] => (float) $item['total']])
                                            ->toArray();
            @endphp
                <canvas id="chart-{{ $fruitId }}" height="100"></canvas>
                <script>
                    const ctx{{ $fruitId }} = document.getElementById('chart-{{ $fruitId }}').getContext('2d');
                    new Chart(ctx{{ $fruitId }}, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode(array_merge(collect($historicalData[$fruitId])->pluck('month')->toArray(), array_keys($forecast))) !!}, // Gabungkan bulan historis dan prediksi
                            datasets: [
                                {
                                    label: 'Data Historis (kg)',
                                    data: {!! json_encode($historicalDataFruit) !!},
                                    borderColor: 'rgba(75,192,192,1)',
                                    backgroundColor: 'rgba(75,192,192,0.2)',
                                    fill: true,
                                    tension: 0.3,
                                    pointRadius: 5,
                                    pointBackgroundColor: 'rgba(75,192,192,1)'
                                },
                                {
                                    label: 'Prediksi Permintaan (kg)',
                                    data: {!! json_encode($forecast) !!},
                                    borderColor: 'rgba(34,197,94,1)',
                                    backgroundColor: 'rgba(34,197,94,0.2)',
                                    fill: true,
                                    tension: 0.3,
                                    pointRadius: 5,
                                    pointBackgroundColor: 'rgba(34,197,94,1)'
                                }
                            ]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Jumlah (kg)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Bulan'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.parsed.y + ' kg';
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>
            @endif
        </div>
    @endforeach
</div>
    </div>
</body>

</html>