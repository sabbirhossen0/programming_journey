"""
Arithmetic operators
Assignment operators
Comparison operators
Logical operators
Identity operators
Membership operators
Bitwise operators
"""

x=20
x+=1
print(x)

#arithmatic operator
a=5+5
b=20-10
c=5*2
d=20/2
e=40%30
#floor division
f=21//2
#power
p=2**5

print(a,b,c,d,e,f,p) 

#Assignment operators
x=10
x+=1
print(x)

x-=1
print(x)

x*=1
print(x)

x/=1
print(x)

x//=1
print(x)

x**=1
print(x)

# Python Comparison Operators

x=10
y=20

print(x==y)
print(x<y)
print(x>y)
print(x>=y)
print(x<=y)

# Python Logical Operators

a=10
b=20
print("Python Logical Operators")
print(a>b and b<a) #false  must me two condition true then return true
print(b>a or a<b)  #one condition is true then return true
print(not(a < 5 and a < 8)) #true  cause both conditon are false

print("Python Identity Operators")

a=10
b=20

print(a is b) #false
print(a is not b) #true

print("Python Membership Operators")

x = ["apple", "banana"]

print("banana" in x)

# returns True because a sequence with the value "banana" is in the list

x = ["apple", "banana"]

print("chili" not in x)


#Operator Precedence

# Operator	Description	
# ()	Parentheses	
# **	Exponentiation	
# +x  -x  ~x	Unary plus, unary minus, and bitwise NOT	
# *  /  //  %	Multiplication, division, floor division, and modulus	
# +  -	Addition and subtraction	
# <<  >>	Bitwise left and right shifts	
# &	Bitwise AND	
# ^	Bitwise XOR	
# |	Bitwise OR	
# ==  !=  >  >=  <  <=  is  is not  in  not in 	Comparisons, identity, and membership operators	
# not	Logical NOT	
# and	AND	
# or	OR