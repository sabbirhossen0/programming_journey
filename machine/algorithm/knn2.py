import pandas as pd
import numpy as np
from collections import Counter

data = pd.read_csv("diabetes.csv")
npy = data.to_numpy()

length = len(npy)
training_num = round(length * 0.8)

training_dataset = npy[0:training_num, :-1]
training_class = npy[0:training_num, -1] 

testing_dataset = npy[training_num:, :-1]
testing_class = npy[training_num:, -1]

def euclidean_distance(a, b):
  return np.sqrt(np.sum((a - b) ** 2))

def knn_predict(training_data, training_labels, test_point, k):
    distances = []
    for i in range(len(training_data)):
        distance = euclidean_distance(training_data[i], test_point)
        distances.append((distance, training_labels[i]))

    distances.sort(key=lambda x: x[0])
    k_nearest_labels = [label for (z, label) in distances[:k]]
    vote_result = Counter(k_nearest_labels).most_common(1)[0][0]
    return vote_result

k = int(input("Enter k: "))
predictions = []


for test_point in testing_dataset:
    prediction = knn_predict(training_dataset, training_class, test_point, k)
    predictions.append(prediction)

correct = 0
for i in range(len(predictions)):
    if predictions[i] == testing_class[i]:
        correct += 1

accuracy = (correct / len(testing_class)) * 100
print(f"Accuracy: {accuracy:.2f}%")