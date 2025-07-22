import csv
import random
import math


# Load CSV with header skip
def load_csv(filename):
    with open(filename, 'r') as file:
        reader = csv.reader(file)
        next(reader)  # skip header
        dataset = list(reader)
        for row in dataset:
            for i in range(len(row) - 1):
                row[i] = float(row[i])
            row[-1] = int(row[-1])
        return dataset


# Split dataset into train/test
def split_dataset(dataset, split_ratio=0.7):
    random.shuffle(dataset)
    split_point = int(len(dataset) * split_ratio)
    return dataset[:split_point], dataset[split_point:]

# Euclidean distance
def euclidean_distance(row1, row2):
    return math.sqrt(sum((row1[i] - row2[i])**2 for i in range(len(row1) - 1)))

# Get neighbors
def get_neighbors(train_set, test_row, k):
    distances = []
    for train_row in train_set:
        dist = euclidean_distance(test_row, train_row)
        distances.append((train_row, dist))
    distances.sort(key=lambda x: x[1])
    neighbors = [distances[i][0] for i in range(k)]
    return neighbors

# Predict label by majority vote
def predict_classification(neighbors):
    votes = {}
    for row in neighbors:
        label = row[-1]
        votes[label] = votes.get(label, 0) + 1
    return max(votes, key=votes.get)

# Calculate accuracy
def calculate_accuracy(test_set, predictions):
    correct = sum(1 for i in range(len(test_set)) if test_set[i][-1] == predictions[i])
    return (correct / len(test_set)) * 100

# ====== MAIN ======

filename = 'diabetes.csv'  # Put your CSV filename here

dataset = load_csv(filename)

# Columns where 0 means missing value (based on Pima dataset knowledge)
zero_missing_cols = [1, 2, 3, 4, 5]  # Glucose, BloodPressure, SkinThickness, Insulin, BMI

# dataset = impute_zeros_with_mean(dataset, zero_missing_cols)
# dataset = normalize_dataset(dataset)

for k in [1, 3, 5, 7, 9, 11, 13, 15]:
    train_set, test_set = split_dataset(dataset)
    predictions = []
    for test_row in test_set:
        neighbors = get_neighbors(train_set, test_row, k)
        prediction = predict_classification(neighbors)
        predictions.append(prediction)
    accuracy = calculate_accuracy(test_set, predictions)
    print(f"K = {k}, Accuracy = {accuracy:.2f}%")
