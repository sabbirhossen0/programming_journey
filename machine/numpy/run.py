import numpy as np
# Create a NumPy array
arr = np.array([1, 2, 3, 4, 5])

print("Original Array:", arr)

# Add 10 to each element
arr_plus_10 = arr + 10
print("After adding 10:", arr_plus_10)

# Multiply each element by 2
arr_times_2 = arr * 2
print("After multiplying by 2:", arr_times_2)

# Get the mean of the array
mean = np.mean(arr)
print("Mean:", mean)

# Get the square of each element
squared = np.square(arr)
print("Squared:", squared)
