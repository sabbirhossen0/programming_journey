import csv
import random
import math

# Load CSV file and skip header
def load_csv(filename):
    with open(filename, 'r') as file:
        reader = csv.reader(file)
        next(reader)  # Skip header row
        dataset = list(reader)
        for row in dataset:
            for i in range(len(row) - 1):
                row[i] = float(row[i])
        return dataset

# Normalize dataset using Min-Max scaling
def normalize_dataset(dataset):
    min_max = []
    for i in range(len(dataset[0]) - 1):  # For each feature column
        col_values = [row[i] for row in dataset]
        min_val = min(col_values)
        max_val = max(col_values)
        min_max.append((min_val, max_val))
        for row in dataset:
            if max_val - min_val == 0:
                row[i] = 0.0
            else:
                row[i] = (row[i] - min_val) / (max_val - min_val)
    return dataset

# Split dataset into train and test sets
def split_dataset(dataset, split_ratio=0.7):
    random.shuffle(dataset)
    split_point = int(split_ratio * len(dataset))
    return dataset[:split_point], dataset[split_point:]

# Euclidean distance between two data points
def euclidean_distance(row1, row2):
    distance = 0.0
    for i in range(len(row1) - 1):
        distance += (row1[i] - row2[i]) ** 2
    return math.sqrt(distance)

# Get k nearest neighbors
def get_neighbors(train_set, test_row, k):
    distances = []
    for train_row in train_set:
        dist = euclidean_distance(test_row, train_row)
        distances.append((train_row, dist))
    distances.sort(key=lambda x: x[1])
    neighbors = [distances[i][0] for i in range(k)]
    return neighbors

# Predict the class label based on neighbors
def predict_classification(neighbors):
    class_votes = {}
    for row in neighbors:
        label = row[-1]
        if label not in class_votes:
            class_votes[label] = 0
        class_votes[label] += 1
    return max(class_votes, key=class_votes.get)

# Calculate accuracy percentage
def calculate_accuracy(test_set, predictions):
    correct = 0
    for i in range(len(test_set)):
        if test_set[i][-1] == predictions[i]:
            correct += 1
    return (correct / len(test_set)) * 100

# ======== MAIN ========
filename = 'diabetes.csv'  # Your CSV file in the same folder

dataset = load_csv(filename)
dataset = normalize_dataset(dataset)

# for k in [1, 3, 5, 7, 9, 11, 13, 15]:
for k in [25,27,29,31,33,35]:
    train_set, test_set = split_dataset(dataset)
    predictions = []
    for test_row in test_set:
        neighbors = get_neighbors(train_set, test_row, k)
        result = predict_classification(neighbors)
        predictions.append(result)
    accuracy = calculate_accuracy(test_set, predictions)
    print(f"K = {k}, Accuracy = {accuracy:.2f}%")
