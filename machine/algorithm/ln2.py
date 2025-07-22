import pandas as pd
import numpy as np
import matplotlib.pyplot as plt

data = pd.read_csv("salary.csv")
npy = data.to_numpy()

length = len(npy)
train_len = round(length * 0.8)

train_x = npy[0:train_len, 1] 
train_y = npy[0:train_len, 2]

test_x = npy[train_len:, 1]
test_y = npy[train_len:, 2]

n = len(train_x)
mean_x = np.mean(train_x)
mean_y = np.mean(train_y)

SS_xy = 0
SS_xx = 0

for i in range(n):
    x_diff = train_x[i] - mean_x
    y_diff = train_y[i] - mean_y

    SS_xy += x_diff * y_diff
    SS_xx += x_diff ** 2

slope = SS_xy / SS_xx
intercept = mean_y - slope * mean_x

def predict(x):
    return slope * x + intercept

errors = [(predict(test_x[i]) - test_y[i]) ** 2 for i in range(len(test_x))]
mse = sum(errors) / len(errors)

print(f"{mse:.2f}")
line_x = np.linspace(np.min(train_x), np.max(train_x), 100)
line_y = predict(line_x)
plt.figure(figsize=(10, 6))
plt.scatter(train_x, train_y, color='blue', label='Training Data')
plt.scatter(test_x, test_y, color='green', label='Testing Data')
plt.plot(line_x, line_y, color='red', linewidth=2, label='Regression Line')
plt.title('Linear Regression Fit')
plt.xlabel('X-Values')
plt.ylabel('Y-Values')
plt.legend()
plt.show()