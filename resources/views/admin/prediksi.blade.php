<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SJFarm - Prediksi Permintaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        @include('components.admin-sidebar')
        <div class="col-span-full md:col-span-4 min-h-screen p-6">
            <h2 class="text-2xl font-bold mb-4">Prediksi Permintaan (bulan ini dan 2 bulan kedepan)</h2>

            @foreach ($results as $fruit => $data)
                <div class="mb-5">
                    <h4 class="font-bold">{{ $fruit }}</h4>

                    @if (isset($data['warning']))
                        <div>{{ $data['warning'] }}</div>
                    @elseif (isset($data['history']))
                        <canvas id="chart-{{ Str::slug($fruit, '-') }}" height="100"></canvas>
                        <script>
                            const ctx_{{ Str::slug($fruit, '_') }} = document.getElementById('chart-{{ Str::slug($fruit, '-') }}').getContext('2d');

                            historyLabels = {!! json_encode(array_keys($data['history'])) !!};
                            historyData = {!! json_encode(array_values($data['history'])) !!};

                            forecastLabels = {!! isset($data['forecast']) ? json_encode(array_keys($data['forecast'])) : '[]' !!};
                            forecastData = {!! isset($data['forecast']) ? json_encode(array_values($data['forecast'])) : '[]' !!};

                            new Chart(ctx_{{ Str::slug($fruit, '_') }}, {
                                type: 'line',
                                data: {
                                    labels: historyLabels.concat(forecastLabels),
                                    datasets: [
                                        {
                                            label: 'Historis (kg)',
                                            data: historyData,
                                            fill: false,
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            tension: 0.1
                                        },
                                        {
                                            label: 'Prediksi (kg)',
                                            data: Array(historyData.length).fill(null).concat(forecastData),
                                            fill: false,
                                            borderColor: 'rgba(255, 99, 132, 1)',
                                            borderDash: [5, 5],
                                            tension: 0.1
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top'
                                        },
                                        title: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        </script>
                    @else
                        <div class="alert alert-danger">Data tidak tersedia.</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</body>

</html>
