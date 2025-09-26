import math
import csv
import random
import matplotlib.pyplot as plt  # For plotting

# -------------------- Helper Functions --------------------
def sigmoid(z):
    # Prevent overflow
    if z < -100: z = -100
    elif z > 100: z = 100
    return 1 / (1 + math.exp(-z))

def dot_product(a, b):
    return sum([a[i] * b[i] for i in range(len(a))])

# -------------------- CSV Preprocessing --------------------
def preprocess_csv(filename):
    X, y = [], []
    categorical_maps = {}  # store mapping for each categorical column
    
    with open(filename, 'r') as file:
        reader = csv.reader(file)
        headers = next(reader)  # skip header
        
        # Determine categorical columns using first non-header row
        sample_row = next(reader)
        file.seek(0)
        next(reader)
        categorical_cols = []
        for i, val in enumerate(sample_row[:-1]):  # last column = target
            try:
                float(val)
            except ValueError:
                categorical_cols.append(i)
        
        # Initialize mapping for each categorical column
        for col in categorical_cols:
            categorical_maps[col] = {}
        
        for row in reader:
            features = []
            for i, val in enumerate(row[:-1]):
                if val == '' or val is None:
                    val = '0'  # replace empty cells with 0
                if i in categorical_cols:
                    if val not in categorical_maps[i]:
                        categorical_maps[i][val] = len(categorical_maps[i])
                    features.append(categorical_maps[i][val])
                else:
                    try:
                        features.append(float(val))
                    except ValueError:
                        features.append(0.0)
            # Skip row if label is empty
            if row[-1] == '' or row[-1] is None:
                continue
            y.append(int(row[-1]))
            X.append(features)
    
    return X, y

# -------------------- Feature Normalization --------------------
def normalize_features(X):
    X_norm = []
    n_features = len(X[0])
    # Compute min and max for each column
    mins = [min([X[i][j] for i in range(len(X))]) for j in range(n_features)]
    maxs = [max([X[i][j] for i in range(len(X))]) for j in range(n_features)]
    
    for row in X:
        norm_row = []
        for j in range(n_features):
            if maxs[j] != mins[j]:
                norm_val = (row[j] - mins[j]) / (maxs[j] - mins[j])
            else:
                norm_val = 0.0
            norm_row.append(norm_val)
        X_norm.append(norm_row)
    
    return X_norm

# -------------------- Train/Test Split --------------------
def train_test_split(X, y, test_ratio=0.2):
    data = list(zip(X, y))
    random.shuffle(data)
    split = int(len(data) * (1 - test_ratio))
    train_data, test_data = data[:split], data[split:]
    X_train, y_train = zip(*train_data)
    X_test, y_test = zip(*test_data)
    return list(X_train), list(y_train), list(X_test), list(y_test)

# -------------------- Logistic Regression Training --------------------
def train_logistic_regression(X, y, lr=0.01, epochs=200):
    m = len(X)
    n = len(X[0])
    
    W = [0.0] * n
    b = 0.0
    totalErrorForCurvePlotting = []
    
    for i in range(epochs):
        totalError = 0.0
        for j in range(m):
            z = dot_product(W, X[j]) + b
            y_hat = sigmoid(z)
            
            # Error
            error = -(y[j] * math.log(y_hat + 1e-9) + (1 - y[j]) * math.log(1 - y_hat + 1e-9))
            totalError += error
            
            # Gradient update
            dv = [(y_hat - y[j]) * X[j][k] for k in range(n)]
            for k in range(n):
                W[k] -= lr * dv[k]
            b -= lr * (y_hat - y[j])
        
        # Print totalError at first epoch and every 50 epochs
        if i == 0 or (i+1) % 50 == 0:
            print(f"Epoch {i+1}, Total Error: {totalError:.4f}")
        
        totalErrorForCurvePlotting.append(totalError)
    
    return W, b, totalErrorForCurvePlotting

# -------------------- Prediction & Accuracy --------------------
def predict(X, W, b):
    y_pred = []
    for i in range(len(X)):
        z = dot_product(W, X[i]) + b
        y_hat = sigmoid(z)
        y_pred.append(1 if y_hat >= 0.5 else 0)
    return y_pred

def accuracy(y_true, y_pred):
    correct = sum([1 for i in range(len(y_true)) if y_true[i] == y_pred[i]])
    return (correct / len(y_true)) * 100

# -------------------- Main --------------------
if __name__ == "__main__":
    # Load and preprocess CSV
    X, y = preprocess_csv("accident.csv")  # replace with your CSV file
    
    # Normalize numeric features
    X = normalize_features(X)
    
    # Split dataset
    X_train, y_train, X_test, y_test = train_test_split(X, y, test_ratio=0.3)
    
    # Train logistic regression
    W, b, errors = train_logistic_regression(X_train, y_train, lr=0.01, epochs=200)
    
    # Test predictions
    y_pred = predict(X_test, W, b)
    
    # Print results
    acc = accuracy(y_test, y_pred)
    print("\nTesting Results:")
    print("Predictions:", y_pred)
    print("Accuracy: {:.2f}%".format(acc))
    print("Error: {:.2f}%".format(100 - acc))
    
    # Plot training error curve
    plt.plot(errors)
    plt.xlabel("Epochs")
    plt.ylabel("Total Training Error")
    plt.title("Iteration vs Training Error")
    plt.show()
