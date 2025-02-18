# Set is a collection which is unordered and unindexed. It does not allow duplicate members.

agelist = {10, 20, 30, 40, 50, 60}

# Single value access (Not possible directly because sets are unordered)
# We can convert it to a list and access an element
agelist_list = list(agelist)
print(agelist_list[1])  # Example: Access second element after conversion

# Multiple values (Iterating through the set)
for x in agelist:
    print(x)

# Display full set
print(agelist)  # Output: {10, 20, 30, 40, 50, 60} (order may vary)

# Add a new item to the set
agelist.add(13)
print(agelist)  # Output: {40, 10, 50, 20, 60, 13, 30} (order may vary)

# Add multiple items using update() (equivalent to insert)
agelist.update([80])
print(agelist)  # Output: {40, 10, 80, 50, 20, 60, 13, 30} (order may vary)

# Remove a specific element
agelist.remove(10)  # If the element is not found, it raises an error
print(agelist)

# Alternative way to remove (avoids error if element does not exist)
agelist.discard(100)  # Does nothing if 100 is not in the set
print(agelist)

# Sorting (Sets do not support sorting, so we need to convert to a list first)
sorted_set = sorted(agelist)  # Ascending order
print(sorted_set)  # Output: [13, 20, 30, 40, 50, 60, 80]

sorted_set_desc = sorted(agelist, reverse=True)  # Descending order
print(sorted_set_desc)  # Output: [80, 60, 50, 40, 30, 20, 13]
