
# Tuple is a collection which is ordered and unchangeable (immutable). Allows duplicate members.

agelist = (10, 20, 30, 40, 50, 60)

# Single value
print(agelist[1])  # Output: 20

# Multiple values
for x in agelist:
    print(x)

# Display full tuple
print(agelist)  # Output: (10, 20, 30, 40, 50, 60)

# Tuples are immutable, so we must convert them to a list before modifying
temp_list = list(agelist)

# Add item using append
temp_list.append(13)

# Convert back to tuple
agelist = tuple(temp_list)
print(agelist)  # Output: (10, 20, 30, 40, 50, 60, 13)

# Add item using insert (convert to list first)
temp_list.insert(1, 80)  # Index number, value   
agelist = tuple(temp_list)
print(agelist)  # Output: (10, 80, 20, 30, 40, 50, 60, 13)

# Remove specific item (pop is not available in tuple, so convert to list)
temp_list.pop(0)
agelist = tuple(temp_list)
print(agelist)  # Output: (80, 20, 30, 40, 50, 60, 13)

# Sorting (Tuples are immutable, so we need to convert to list)
sorted_tuple = tuple(sorted(agelist))  # Ascending
print(sorted_tuple)  # Output: (13, 20, 30, 40, 50, 60, 80)

sorted_tuple_desc = tuple(sorted(agelist, reverse=True))  # Descending
print(sorted_tuple_desc)  # Output: (80, 60, 50, 40, 30, 20, 13)
