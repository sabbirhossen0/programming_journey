
# While loop

i = 1
while i < 6:
  print(i)
  i += 1

# break
i = 1
while i < 6:
  print(i)
  if i == 3:
    break
  i += 1

    #Continue
i = 0
while i < 6:
  i += 1
  if i == 3:
    continue
  print(i)

# else
i = 1
while i < 6:
  print(i)
  i += 1
else:
  print("i is no longer less than 6")



# For loop

# break statement
fruits = ["apple", "banana", "cherry"]
for x in fruits:
  print(x)
  if x == "banana":
    break

#continue statement
fruits = ["apple", "banana", "cherry"]
for x in fruits:
  if x == "banana":
    continue
  print(x)

#range

for x in range(6):
  print(x)  

for x in range(2, 6):
  print(x) 

for x in range(2, 30, 3):
  print(x)

for x in range(6):
  print(x)
else:
  print("Finally finished!")  
