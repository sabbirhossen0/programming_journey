import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
from sklearn.preprocessing import StandardScaler

# 1. Choose K (number of clusters).

# 2. Initialize K centroids randomly.

# 3. Assign each data point to the nearest centroid.

# 4. Update centroids by calculating the mean.

# 5. Repeat until convergence.




# Load dataset
dataset = pd.read_csv("penguins.csv")
data = dataset.copy()
data.dropna(inplace=True)

# Encode 'sex' if needed
sex_map = {'MALE': 1, 'FEMALE': 0}
if 'sex' in data.columns:
    data['sex'] = data['sex'].map(sex_map)

# Features to use for clustering
features = ['culmen_length_mm','culmen_depth_mm','flipper_length_mm','body_mass_g']
X_data = data[features]

# Scale the features
scaler = StandardScaler()
X = scaler.fit_transform(X_data)

# K-Means implementation
def kmeans(X, K=3, max_iter=300):
    def euclidean_distance(a, b):
        return np.sqrt(np.sum((a - b) ** 2))

    num_rows = X.shape[0]
    centroids = X[np.random.choice(num_rows, K, replace=False)]

    for i in range(max_iter):
        clusters = np.zeros(num_rows, dtype=int)

        # Assign points to nearest centroid
        for idx, point in enumerate(X):
            distances = [euclidean_distance(point, c) for c in centroids]
            clusters[idx] = np.argmin(distances)

        # Update centroids
        new_centroids = np.array([X[clusters == k].mean(axis=0) if len(X[clusters == k]) > 0 else centroids[k] 
                                  for k in range(K)])
        
        if np.allclose(new_centroids, centroids):
            break
        centroids = new_centroids

    return clusters

# Run K-Means
final_clusters = kmeans(X, K=3, max_iter=300)

# Add cluster labels to dataframe
data['cluster'] = final_clusters

# Plot clusters
plt.figure(figsize=(10,7))
plt.scatter(data['culmen_length_mm'], data['culmen_depth_mm'],
            c=data['cluster'], cmap='viridis', alpha=0.7)
plt.xlabel("Culmen Length (mm)")
plt.ylabel("Culmen Depth (mm)")
plt.title("Penguin Clusters Found by K-Means")
plt.colorbar(label='Cluster ID')
plt.grid(True)
plt.show()
