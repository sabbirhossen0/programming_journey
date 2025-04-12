# A function is a block of code which only runs when it is called.

# general function define
def myfunction():
    print("hello this is function")
myfunction()


# with paramitter and return value
def sum(a,b):
    return a+b
print(sum(10,20))

# without paramitter and return value
def sum():
    a=10
    b=20
    return a+b
print(sum())

# without paramitter and no return value
def sum():
    a=10
    b=20 
    print(a+b)

sum()

# with paramitter and no return value
def sum(a,b):
    print(a+b)

sum(10,20)




# default paramitter 
def my_function(country = "Norway"):
  print("I am from " + country)

my_function("Sweden")
my_function()

