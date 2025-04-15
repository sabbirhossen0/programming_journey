with open("file.txt", "w") as f:
    f.write("this is file !\n")
    f.write("Second line.")

n = open("file.txt", "r")
print(n.read())   


