# Function to read the CSV data
def read_csv(filepath):
    x = []
    y = []
    with open(filepath, 'r') as file:
        lines = file.readlines()
        for line in lines[1:]:  # Skip header
            if line.strip() == "":
                continue
            parts = line.strip().split(',')
            x.append(float(parts[0]))  # YearsExperience
            y.append(float(parts[1]))  # Salary
    return x, y

# File name
filename = "salary.csv" 

# Read data
x_train, y_train = read_csv(filename)
x_test, y_test = x_train, y_train  # Using same data as test

# Step 1: Means
n = len(x_train)
x_mean = sum(x_train) / n
y_mean = sum(y_train) / n

# Step 2: SS_xy and SS_xx
SS_xy = sum((x_train[i] - x_mean) * (y_train[i] - y_mean) for i in range(n))
SS_xx = sum((x_train[i] - x_mean) ** 2 for i in range(n))

# Step 3: Coefficients
b1 = SS_xy / SS_xx
b0 = y_mean - b1 * x_mean

print(f"Line: y = {b0:.2f} + {b1:.2f}x")

# Step 4: Predictions and MSE
squared_errors = []
for i in range(n):
    y_pred = b0 + b1 * x_test[i]
    error = y_test[i] - y_pred
    squared_errors.append(error ** 2)

# Step 5: MSE
mse = sum(squared_errors) / n
print(f"Mean Squared Error: {mse:.2f}")
