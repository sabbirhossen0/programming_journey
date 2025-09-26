# -----------------------------
# Import required libraries
# -----------------------------
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt

# -----------------------------
# Step 1: Load dataset
# -----------------------------
data = pd.read_csv("penguins.csv")   # Load your dataset

# Replace "NA" with NaN and drop rows with missing values
data = data.replace("NA", np.nan).dropna()

# Select only numeric features for clustering
X = data[["culmen_length_mm", "culmen_depth_mm",
          "flipper_length_mm", "body_mass_g"]].astype(float).values

# Save the species (or sex) column if available
true_labels = data["species"].values if "species" in data.columns else (
              data["sex"].values if "sex" in data.columns else None)

# -----------------------------
# Step 2: Define KMeans functions
# -----------------------------

# Randomly choose 'k' points as initial centroids
def initialize_centroids(X, k):
    np.random.seed(42)   # for reproducibility
    indices = np.random.choice(X.shape[0], k, replace=False)
    return X[indices]

# Compute distances of all points from each centroid
def compute_distances(X, centroids):
    distances = np.zeros((X.shape[0], len(centroids)))
    for i, c in enumerate(centroids):
        distances[:, i] = np.linalg.norm(X - c, axis=1)
    return distances

# Assign each point to the nearest centroid
def assign_clusters(distances):
    return np.argmin(distances, axis=1)

# Update centroids as mean of all points in a cluster
def update_centroids(X, labels, k):
    new_centroids = []
    for i in range(k):
        points = X[labels == i]
        if len(points) > 0:
            new_centroids.append(points.mean(axis=0))
        else:
            # handle empty cluster by reinitializing randomly
            new_centroids.append(X[np.random.choice(X.shape[0])])
    return np.array(new_centroids)

# Main KMeans algorithm
def kmeans(X, k=3, max_iters=100, tol=1e-4):
    centroids = initialize_centroids(X, k)   # Step 1: pick initial centroids
    for _ in range(max_iters):
        old_centroids = centroids
        distances = compute_distances(X, centroids)   # Step 2: compute distances
        labels = assign_clusters(distances)           # Step 3: assign clusters
        centroids = update_centroids(X, labels, k)    # Step 4: update centroids
        # Stop if centroids do not change much
        if np.linalg.norm(centroids - old_centroids) < tol:
            break
    return labels, centroids

# -----------------------------
# Step 3: Run KMeans
# -----------------------------
k = 3 if true_labels is not None and "species" in data.columns else 2
pred_labels, centroids = kmeans(X, k)

# -----------------------------
# Step 4: Accuracy calculation (fixed version for string labels)
# -----------------------------
if true_labels is not None:
    label_map = {}
    for cluster in np.unique(pred_labels):
        mask = pred_labels == cluster
        if len(true_labels[mask]) > 0:
            values, counts = np.unique(true_labels[mask], return_counts=True)
            most_common = values[np.argmax(counts)]   # find most common label
            label_map[cluster] = most_common

    mapped_preds = [label_map[c] for c in pred_labels]
    accuracy = np.mean(mapped_preds == true_labels) * 100
    print(f"Clustering Accuracy: {accuracy:.2f}%")
else:
    print("No label column (species/sex) found → only clusters are shown.")

# -----------------------------
# Step 5: Visualization
# -----------------------------
plt.figure(figsize=(8,6))

# Plot first two features (culmen length vs culmen depth)
plt.scatter(X[:, 0], X[:, 1], c=pred_labels, cmap="viridis", s=50, alpha=0.7)

# Plot centroids
plt.scatter(centroids[:, 0], centroids[:, 1],
            c='red', marker='X', s=200, label="Centroids")

plt.xlabel("Culmen Length (mm)")
plt.ylabel("Culmen Depth (mm)")
plt.title(f"K-Means Clustering (k={k}) on Penguins Dataset")
plt.legend()
plt.show()
