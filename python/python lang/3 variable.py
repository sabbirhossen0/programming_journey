#Variables are containers for storing data values.


a=10
b='hello Sabbir'
c=10j
print(a,b,c)
#type checking
print(type(a),type(b), type(c))


# Variable Name 
"""
A variable name must start with a letter or the underscore character
A variable name cannot start with a number
A variable name can only contain alpha-numeric characters and underscores (A-z, 0-9, and _ )
Variable names are case-sensitive (age, Age and AGE are three different variables)
A variable name cannot be any of the Python keywords.

"""
myvar = "John"
my_var = "John"
_my_var = "John"
myVar = "John"
MYVAR = "John"
myvar2 = "John"


print(myvar)
print(my_var)
print(_my_var)
print(myVar)
print(MYVAR)
print(myvar2)


#many Value and multiple variable 

name1,name2,name3="hasib","sunvi","nahid"
print(name1+" "+name2+" "+name3)

#one value and multiple variable 

name10=name20=name49=45 
print(name10,name20,name49)



# global variable    goto top and show global variable 

"""
Variables that are created outside of a function (as in all of the examples in the previous pages) are known as global variables.

Global variables can be used by everyone, both inside of functions and outside.

""" 

#modify value 

a=10
print(a)
a=20
print(a)
a=30
print(a)

