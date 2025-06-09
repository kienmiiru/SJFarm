import sys
import json
import warnings
import pandas as pd
from statsmodels.tsa.arima.model import ARIMA
from datetime import datetime

warnings.filterwarnings("ignore")

# Ambil data dari stdin
data = json.load(sys.stdin)

results = {}
for fruit_name, entries in data.items():
    df = pd.DataFrame(entries)
    df['requested_date'] = pd.to_datetime(df['requested_date'])
    df.set_index('requested_date', inplace=True)

    # Hitung total requested_stock_in_kg per bulan
    monthly = df['requested_stock_in_kg'].resample('M').sum()

    # Buang data bulan ini (jika ada)
    current_month = pd.Timestamp(datetime.now().strftime('%Y-%m-01'))
    monthly = monthly[monthly.index < current_month]

    # Simpan histori untuk keperluan visualisasi
    history = monthly.copy()

    # Jika data terlalu sedikit, abaikan dan beri warning
    if len(monthly) < 6:
        results[fruit_name] = {
            'warning': 'Data historis terlalu sedikit untuk prediksi (minimal 6 bulan dibutuhkan).',
            'history': history.to_dict()
        }
        continue

    try:
        model = ARIMA(monthly, order=(1, 1, 1))
        model_fit = model.fit()

        forecast = model_fit.forecast(steps=3)
        forecast.index = forecast.index.strftime('%Y-%m')
        history.index = history.index.strftime('%Y-%m')

        results[fruit_name] = {
            'forecast': forecast.to_dict(),
            'history': history.to_dict()
        }
    except Exception as e:
        results[fruit_name] = {
            'error': str(e),
            'history': history.to_dict()
        }

print(json.dumps(results, indent=2))
