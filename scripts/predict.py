import sys
import json
import pandas as pd
from statsmodels.tsa.holtwinters import ExponentialSmoothing

input_json = sys.argv[1]
data = json.loads(input_json)
output = {}

for fruit_id, records in data.items():
    df = pd.DataFrame(records)
    df['month'] = pd.to_datetime(df['month'])
    df = df.set_index('month').sort_index()

    # Holt-Winters Seasonal Model
    # if len(df) < 12:
    #     output[fruit_id] = {"error": "Data tidak cukup"}
    #     continue

    try:
        model = ExponentialSmoothing(df['total'], seasonal='add', seasonal_periods=5)
        fit = model.fit()
        forecast = fit.forecast(3)  # 3 bulan ke depan
        forecast_dict = {date.strftime("%Y-%m"): float(value) for date, value in forecast.items()}
        output[fruit_id] = forecast_dict
    except Exception as e:
        output[fruit_id] = {"error": str(e)}

print(json.dumps(output))
